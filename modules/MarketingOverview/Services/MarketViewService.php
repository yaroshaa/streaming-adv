<?php

namespace Modules\MarketingOverview\Services;

use App\ClickHouse\DateGranularityInterface;
use App\Entities\HolidayEvent;
use App\Entities\Market;
use App\Entities\MarketingChannel;
use App\Repositories\HolidayEventRepository;
use App\Repositories\MarketingChannelRepository;
use App\Repositories\MarketRepository;
use App\Traits\DateOrPeriods;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Arr;
use Modules\MarketingOverview\ClickHouse\Repositories\MarketingExpenseRepository;
use Modules\MarketingOverview\ClickHouse\Repositories\MarketingOverviewRepository;
use Modules\MarketingOverview\Services\Calculate\ByStoresService;
use Modules\MarketingOverview\Services\Calculate\ConversationIndicatorService;
use Modules\MarketingOverview\Services\Calculate\ExpenseSpendService;
use Modules\MarketingOverview\Services\Calculate\OverPeriodService;
use Modules\MarketingOverview\Services\Calculate\TotalService;
use Modules\MarketingOverview\Services\Calculate\WarehouseStatisticService;
use Modules\MarketingOverview\Services\Widgets\OverviewTotalsTableWidget;

class MarketViewService
{
    use DateOrPeriods;

    private static array $cacheMarketingOverviewByStores = [];
    public Carbon $from;
    public Carbon $to;
    public int $currencyId;
    public string $granularity;
    /** @var Market[] */
    private array $markets;
    /** @var HolidayEvent[] */
    private array $holidayEvents;
    /** @var MarketingChannel[] */
    private array $marketingChannels;
    private MarketingExpenseRepository $expenseRepository;
    private MarketingOverviewRepository $overviewRepository;
    private array $cachedSpendByMarket = [];

    public function __construct(
        MarketingExpenseRepository $marketingExpenseRepository,
        MarketingOverviewRepository $overviewRepository
    )
    {
        $this->expenseRepository = $marketingExpenseRepository;
        $this->overviewRepository = $overviewRepository;
    }

    public function setFilter(Carbon $from, Carbon $to, int $currencyId, string $granularity)
    {
        $this->from = $from;
        $this->to = $to;
        $this->currencyId = $currencyId;
        $this->granularity = $granularity;
    }

    public function getStreak(): array
    {
        $days = $this->to->diffInDays($this->from);

        return [
            'type' => $days == 0 ? 'hours' : 'days',
            'value' => $days == 0 ? 24 : $days + 1
        ];
    }

    /**
     * @param string $dateGranularity
     * @return array
     * @throws Exception
     */
    public function calcOverPeriod(string $dateGranularity): array
    {
        $periods = self::getPeriods($dateGranularity);

        $current = [
            $periods['current']['start'],
            $periods['current']['end'],
            $dateGranularity,
            $this->currencyId,
        ];

        $estimate = [
            $periods['estimate']['start'],
            $periods['estimate']['end'],
            $dateGranularity,
            $this->currencyId,
        ];

        $service = new OverPeriodService(
            Arr::get($this->overviewRepository->getOverPeriod(...$current), '0.revenue', 0),
            Arr::get($this->overviewRepository->getOverPeriod(...$estimate), '0.revenue', 0),
            Arr::get($this->expenseRepository->getExpenseOverPeriod(...$current), '0.marketing_expense', 0),
            Arr::get($this->expenseRepository->getExpenseOverPeriod(...$estimate), '0.marketing_expense', 0),
        );

        return $service->getStatistic();
    }

    public function getSpendByMarketByCurrentFilter(): array
    {
        if (empty($this->cachedSpendByMarket)) {
            $this->cachedSpendByMarket = $this->expenseRepository->getOverviewSpendByMarketQuery(
                $this->from,
                $this->to,
                $this->granularity,
                $this->currencyId
            );
        }

        return $this->cachedSpendByMarket;
    }

    /**
     * @throws Exception
     */
    public function marketingOverviewTotal(): array
    {
        list($startOfPrevPeriod, $endOfPrevPeriod) = self::getPreviousPeriodCarbon($this->from, $this->to);
        list($startOfPrevWeekDay, $endOfPrevWeekDay) = self::getPreviousWeekPeriodOfDay(Carbon::now());
        list($startOfDay, $endOfDay) = self::getPeriodOnCurrentDay();

        $service = new TotalService(
            $this->overviewRepository->getOverviewTotal($this->from, $this->to, $this->currencyId, $this->granularity),
            $this->overviewRepository->getOverviewTotal($startOfPrevPeriod, $endOfPrevPeriod, $this->currencyId, $this->granularity),
            $this->overviewRepository->getOverviewTotal($startOfPrevWeekDay, $endOfPrevWeekDay, $this->currencyId, DateGranularityInterface::HOUR_GRANULARITY),
            $this->overviewRepository->getOverviewTotal($startOfDay, $endOfDay, $this->currencyId, DateGranularityInterface::HOUR_GRANULARITY),
            $this->overviewRepository->getOverviewTotal($startOfDay, $endOfDay, $this->currencyId, DateGranularityInterface::EVERY_30_MINUTES_GRANULARITY)
        );

        return $service->getStatistic();
    }

    public function marketingOverviewSpend(): array
    {
        list($startOfDay, $endOfDay) = self::getPeriodOnCurrentDay();
        $expenseRepository = $this->expenseRepository;

        $service = new ExpenseSpendService(
            $expenseRepository->getSpend($startOfDay, $endOfDay, DateGranularityInterface::DAY_GRANULARITY, $this->currencyId),
            $expenseRepository->getSpend(Carbon::now()->subHour(), Carbon::now(), DateGranularityInterface::HOUR_GRANULARITY, $this->currencyId),
            $expenseRepository->getSpend(Carbon::now()->subMinutes(30), Carbon::now(), DateGranularityInterface::EVERY_30_MINUTES_GRANULARITY, $this->currencyId),
            $expenseRepository->getSpend(Carbon::now()->subMinutes(15), Carbon::now(), DateGranularityInterface::EVERY_15_MINUTES_GRANULARITY, $this->currencyId),
        );

        return $service->getStatistic();
    }

    public function marketingOverviewConversationIndicator(): array
    {
        $expenseRepository = $this->expenseRepository;
        $overviewRepository = $this->overviewRepository;

        $service = new ConversationIndicatorService(
            $expenseRepository->getExpenseOverPeriod(null, null, DateGranularityInterface::DAY_GRANULARITY, $this->currencyId),
            $overviewRepository->getConversationIndicatorQuery(DateGranularityInterface::DAY_GRANULARITY),
            $overviewRepository->getConversationIndicatorQuery(DateGranularityInterface::EVERY_30_MINUTES_GRANULARITY),
            $overviewRepository->getConversationIndicatorQuery(DateGranularityInterface::EVERY_45_SECONDS_GRANULARITY),
        );

        return $service->getStatistic();
    }

    public function warehouseStatistic(): array
    {
        $warehouseStatisticService = new WarehouseStatisticService(
            $this->overviewRepository->getWarehouseStatistic(
                $this->from,
                $this->to,
                $this->granularity
            )
        );

        return $warehouseStatisticService->getStatistic();
    }

    /**
     * @todo To remove
     */
    public function calcYearToDayStat(): array
    {
        return [
            "flat" => [
                "value" => 676000000,
                "estimate" => 356000000,
                "change_in_percent" => 239
            ],
            "index" => [
                "value" => 676000000,
                "estimate" => 356000000,
                "change_in_percent" => 239
            ],
            "spend" => [
                "value" => 129000000,
                "estimate" => 120000000,
                "change_in_percent" => 10
            ],
            "current_month" => [
                "value" => 32000000,
                "estimate" => 40000000,
                "change_in_percent" => 89
            ]
        ];
    }

    /**
     * @return array|HolidayEvent[]
     */
    public function getEvents(): array
    {
        if (empty($this->holidayEvents)) {
            $this->holidayEvents = HolidayEventRepository::get()->getHolidayEvent();
        }

        return $this->holidayEvents;
    }

    /**
     * @todo To remove
     */
    public function getMainIndicators(): array
    {
        return [
            [
                'index' => 1,
                'main.title' => 'Est. Revenue',
                'main.total' => number_format(rand(900000, 1200000)),
                'secondary.title' => 'Est. CMAM',
                'secondary.total' => number_format(rand(400000, 800000)),
            ],
            [
                'index' => 2,
                'main.title' => 'Est. CMAM',
                'main.total' => number_format(rand(240000, 30000)),
                'secondary.title' => 'CMAM',
                'secondary.total' => number_format(rand(200000, 400000)),
            ]
        ];
    }

    /**
     * @todo To remove
     */
    public function getSecondaryIndicators(): array
    {
        return [
            [
                'index' => 3,
                'main.title' => 'CM ratio',
                'main.total' => number_format(rand(13000, 1000) * 0.01) . '%',
                'secondary.title' => 'last hour',
                'secondary.total' => number_format(rand(13000, 1000) * 0.01) . '%',
            ],
            [
                'index' => 4,
                'main.title' => 'Est. CMAM',
                'main.total' => number_format(rand(13000, 1000) * 0.01) . '%',
                'secondary.title' => 'last hour',
                'secondary.total' => number_format(rand(13000, 1000) * 0.01) . '%',
            ]
        ];
    }

    /**
     * @throws Exception
     */
    public function getOverviewTotalsTableWidget(): OverviewTotalsTableWidget
    {
        return new OverviewTotalsTableWidget(
            $this->getMarkets(),
            $this->getMarketingChannels(),
            $this->marketingOverviewByStores(),
            $this->getSpendByMarketByCurrentFilter(),
        );
    }

    /**
     * @return array
     */
    public function getMarkets(): array
    {
        if (empty($this->markets)) {
            $channels = [];
            foreach (MarketRepository::get()->findAll() as $channel) {
                /** @var Market $channel */
                $channels[$channel->getId()] = $channel;
            }
            $this->markets = $channels;
        }
        return $this->markets;
    }

    /**
     * @return array
     */
    public function getMarketingChannels(): array
    {
        if (empty($this->marketingChannels)) {
            $channels = [];
            foreach (MarketingChannelRepository::get()->findAll() as $channel) {
                /** @var MarketingChannel $channel */
                $channels[$channel->getId()] = $channel;
            }
            $this->marketingChannels = $channels;
        }
        return $this->marketingChannels;
    }

    /**
     * @throws Exception
     */
    public function marketingOverviewByStores(): array
    {
        if (empty(self::$cacheMarketingOverviewByStores)) {
            list($startOfDay, $endOfDay) = self::getPeriodOnCurrentDay();
            list($startOfPrevPeriod, $endOfPrevPeriod) = self::getPreviousPeriodCarbon($this->from, $this->to);
            $service = new ByStoresService(
                $this->overviewRepository->getTimeGranularity($startOfDay, $endOfDay, DateGranularityInterface::HOUR_GRANULARITY),
                $this->overviewRepository->getTimeGranularity($startOfDay, $endOfDay, DateGranularityInterface::EVERY_30_MINUTES_GRANULARITY),
                $this->getSpendByMarketByCurrentFilter(),
                $this->overviewRepository->getByStores($this->from, $this->to, $this->currencyId),
                $this->overviewRepository->getByStores($startOfPrevPeriod, $endOfPrevPeriod, $this->currencyId)
            );
            self::$cacheMarketingOverviewByStores = $service->getStatistic();
        }

        return self::$cacheMarketingOverviewByStores;
    }
}
