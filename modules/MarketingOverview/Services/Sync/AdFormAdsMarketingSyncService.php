<?php


namespace Modules\MarketingOverview\Services\Sync;

use Exception;
use Modules\MarketingOverview\Services\Sync\AdForm\RequestBag;
use Carbon\Carbon;

class AdFormAdsMarketingSyncService extends BaseSyncService implements MarketingSyncServiceInterface
{
    public RequestBag $requestBag;

    public static function name(): string
    {
        return 'AdForm';
    }

    public static function alias(): string
    {
        return 'adform';
    }

    /**
     * @throws Exception
     */
    public function init()
    {
        if (empty($this->config['client_id'])) {
            throw new Exception("Client id not exist");
        }

        if (empty($this->config['client_secret'])) {
            throw new Exception("Client secret not exist");
        }

        if (empty($this->config['advertiser_id'])) {
            throw new Exception("Advertiser id not exist");
        }

        $this->requestBag = new RequestBag($this->config);
        $this->requestBag->setLog($this->logger);
    }

    public function periodFrom(): Carbon
    {
        return Carbon::now($this->timezone())->startOfDay();
    }

    public static function getIntervalOnSeconds(): int
    {
        return 60 * 15;
    }

    /**
     * @throws Exception
     */
    public function getAmount(): float
    {
        return $this->requestBag->getAmount();
    }

    public function __toString(): string
    {
        return json_encode([
            'Name' => self::name(),
            'Marketing chanel id' => $this->marketingChannelId,
            'Market id' => $this->marketId,
            'Currency id' => $this->currencyId,
            'Client id' => $this->config['client_id'],
            'Advertiser id' => $this->config['advertiser_id']
        ]);
    }
}
