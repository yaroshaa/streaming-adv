<?php

namespace Modules\MarketingOverview\Services\Sync;

use App\ClickHouse\ClickHouseException;
use Carbon\Carbon;
use Exception;
use Illuminate\Log\Logger;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Modules\MarketingOverview\ClickHouse\Repositories\MarketingExpenseRepository;

class MarketingOverviewSyncService
{
    const ZERO_VALUE = 0.00;
    const CACHE_TTL_FOR_PERIOD_VALUE = 60 * 60 * 24 * 7;

    private Logger $logger;

    private MarketingExpenseRepository $exposeRepository;

    private array $config;

    private bool $debugQuery;
    private bool $debugData;

    public function __construct(array $config, MarketingExpenseRepository $exposeRepository)
    {
        $this->config = $config;
        $this->debugQuery = $config['debug_query'];
        $this->debugData = $config['debug_data'];
        $this->exposeRepository = $exposeRepository;
        $this->logger = Log::channel('marketing-overview');
    }

    /**
     * @param MarketingSyncServiceInterface|string $service
     * @throws Exception
     */
    public function processService(string $service)
    {
        if (!$this->enabled()) {
            throw new Exception('Marketing sync disabled in config. Pls configure and enable.');
        }

        $items = $this->getConfigBySyncService($service);

        if (empty($items)) {
            $this->logger->debug(sprintf('Service "%s" not configured, empty config.', $service::name()));
            return;
        }

        foreach ($items as $item) {
            try {
                /** @var MarketingSyncServiceInterface $service */
                $service = new $service($item, $this->logger);
                $this->processMarketingSyncService($service);
            } catch (Exception $exception) {
                $this->logger->error($exception);
            }
        }
    }

    public function enabled(): bool
    {
        return Arr::get($this->config, 'enabled', false);
    }

    /**
     * @param string|MarketingSyncServiceInterface $service
     * @return array
     */
    public function getConfigBySyncService(string $service): array
    {
        return Arr::get($this->config, $service::alias());
    }

    /**
     * @throws ClickHouseException
     * @throws Exception
     */
    public function processMarketingSyncService(MarketingSyncServiceInterface $service)
    {
        $this->logger->debug(sprintf('Start processing: %s', $service));
        $currentDate = Carbon::now($service->timezone());
        $currentDate->startOfMinute();

        list($amount, $serviceAmount, $value) = $this->calculateAmount($service);

        if ($this->debugData) {
            $this->logger->debug([
                'serviceAmount' => $serviceAmount,
                'totalAmount' => $amount,
                'diff' => $value,
                'cachedValueOfInitialPeriodProcessing' => Cache::get($this->getCacheKeyOfService($service)),
                'periodFrom' => $service->periodFrom()->toDateTimeLocalString(),
                'periodFromUTC' => (clone $service->periodFrom())->setTimezone('UTC')->toDateTimeLocalString(),
                'currentDate' => $currentDate->toDateTimeLocalString(),
                'currentDateUTC' => (clone $currentDate)->setTimezone('UTC')->toDateTimeLocalString(),
                'cacheKey' => $this->getCacheKeyOfService($service)
            ]);
        }

        if ($amount > $serviceAmount) {
            $this->logger->debug('Broken logic, existing totals greater service amount');
        }

        if ($value === self::ZERO_VALUE) {
            $this->logger->debug('Zero value');
        } else if ($this->expenseExist(
            $service->getChannelId(),
            $service->getMarketId(),
            $service->getCurrencyId(),
            $value,
            $currentDate,
        )) {
            $this->logger->debug(sprintf('Current expense exist %s', $value));
        } else {
            $this->logger->debug(sprintf('Adding new expense, %s', $value));

            $this->expenseInsert(
                $service->getChannelId(),
                $service->getMarketId(),
                $service->getCurrencyId(),
                $value,
                $currentDate
            );

            $this->logger->debug('Expense added');
        }
    }

    /**
     * @param MarketingSyncServiceInterface $service
     * @return array
     */
    private function calculateAmount(MarketingSyncServiceInterface $service): array
    {
        $amount = $this->expenseAmount(
            $service->getChannelId(),
            $service->getMarketId(),
            $service->getCurrencyId(),
            $service->periodFrom()
        );

        $serviceAmount = $service->getAmount();
        $serviceCacheKey = $this->getCacheKeyOfService($service);
        $iteration = $this->getIterationNumberOfPeriod($service);

        $this->logger->debug(sprintf('Iteration: %d', $iteration));

        // @todo check after live usage in performission
        if ($service instanceof MarketingSyncServiceSkipCheckAmountInterface && $service->skipAmountCheckByInterval()) {
            $this->logger->debug('Skipping amount check in by implement skip chek interface.');
            return [$amount, $serviceAmount, (float)abs(bcsub($amount, $serviceAmount, 2))];
        }

        // If the first iteration, then the entire amount will be written
        if ($iteration === 1) {
            $value = abs(bcsub($amount, $serviceAmount, 2));
            Cache::put($serviceCacheKey, self::ZERO_VALUE, self::CACHE_TTL_FOR_PERIOD_VALUE);
        } else if (Cache::has($serviceCacheKey)) {
            // If has value on cache - will be minus from service amount for calc current actual value on this period
            $value = bcsub($serviceAmount - Cache::get($serviceCacheKey), $amount, 2);
            if ($value < 0) {
                $value = self::ZERO_VALUE;
            }
        } else {
            // If not first iteration and not has value in cache, write total from service to cache, and "skip" iteration
            $value = self::ZERO_VALUE;
            Cache::put($serviceCacheKey, $serviceAmount);
        }

        return [$amount, $serviceAmount, (float)$value];
    }

    /**
     * @param int $channelId
     * @param int $marketId
     * @param int $currencyId
     * @param Carbon $createdAt
     * @return float
     */
    private function expenseAmount(int $channelId, int $marketId, int $currencyId, Carbon $createdAt): float
    {
        $createdAt = clone $createdAt;
        $sql = <<< SQL
SELECT round(sum(value), 2) as amount FROM %s
where
            marketing_chanel_id = %s and
            market_id = %s and
            currency_id = %s and
      marketing_expense.created_at >= toDateTime('%s')
SQL;

        $query = sprintf(
            $sql,
            $this->exposeRepository->tableName(),
            $channelId,
            $marketId,
            $currencyId,
            $createdAt->setTimezone('UTC')->toDateTimeLocalString()
        );
        if ($this->debugQuery) {
            $this->logger->debug($query);
        }
        return Arr::get($this->exposeRepository->getArrayResult($query), '0.amount', self::ZERO_VALUE);
    }

    private function getCacheKeyOfService(MarketingSyncServiceInterface $service): string
    {
        return sprintf(
            'marketing.expense.%s.%s.%s.%s',
            $service->periodFrom()->toDateTimeLocalString(),
            $service->getChannelId(),
            $service->getMarketId(),
            $service->getCurrencyId()
        );
    }

    private function getIterationNumberOfPeriod(MarketingSyncServiceInterface $service): int
    {
        $currentDate = Carbon::now($service->timezone());
        $fromDate = $service->periodFrom();

        return intdiv($currentDate->getTimestamp() - $fromDate->getTimestamp(), $service::getIntervalOnSeconds());
    }

    private function expenseExist(
        int $channelId,
        int $marketId,
        int $currencyId,
        float $value,
        Carbon $createdAt
    ): bool
    {
        $createdAt = clone $createdAt;
        $sql = <<<SQL
SELECT 1 FROM %s
where
      created_at = toDateTime('%s') and
      value = %s and
      marketing_chanel_id = %s and
      market_id = %s and
      currency_id = %s
LIMIT 1
SQL;
        $query = sprintf(
            $sql,
            $this->exposeRepository->tableName(),
            $createdAt->setTimezone('UTC')->toDateTimeLocalString(),
            $value,
            $channelId,
            $marketId,
            $currencyId
        );

        if ($this->debugQuery) {
            $this->logger->debug($query);
        }

        return $this->exposeRepository->existByQuery($query);
    }

    /**
     * @throws ClickHouseException
     */
    private function expenseInsert(
        int $channelId,
        int $marketId,
        int $currencyId,
        float $value,
        Carbon $createdAt
    ): void
    {
        $createdAt = clone $createdAt;
        $this->exposeRepository->insert([
            'marketing_chanel_id' => $channelId,
            'market_id' => $marketId,
            'currency_id' => $currencyId,
            'value' => $value,
            'created_at' => $createdAt->setTimezone('UTC')->toDateTimeLocalString()
        ]);
    }
}
