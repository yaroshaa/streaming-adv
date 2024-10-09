<?php

namespace Modules\MarketingOverview\Services\Sync;

use Modules\MarketingOverview\Services\Sync\Snapchat\RequestBag;
use Carbon\Carbon;
use Exception;

class SnapchatAdsMarketingSyncService extends BaseSyncService implements MarketingSyncServiceInterface
{
    public RequestBag $requestBag;

    /**
     * @return string
     */
    public static function name(): string
    {
        return 'Snapchat';
    }

    /**
     * @return string
     */
    public static function alias(): string
    {
        return 'snapchat';
    }

    public function periodFrom(): Carbon
    {
        return Carbon::now($this->timezone())->startOfDay();
    }

    /**
     * @throws Exception
     */
    public function init()
    {
        if (empty($this->config['refresh_token'])) {
            throw new Exception("Refresh token not exist");
        }

        if (empty($this->config['client_id'])) {
            throw new Exception("Client id not exist");
        }

        if (empty($this->config['client_secret'])) {
            throw new Exception("Client secret not exist");
        }

        if (empty($this->config['ad_account_id'])) {
            throw new Exception("Client secret not exist");
        }

        $this->requestBag = new RequestBag($this->config, $this->timezone);
    }

    public function getAmount(): float
    {
        $spend = 0;

        foreach ($this->requestBag->getCampaignIds() as $id) {
            $spendArray = $this->requestBag->getCampaignSpend($id);
            $spend += array_sum($spendArray);
        }

        //  Micro-currency conversion: 1.00 local currency unit = 1000000 Micro-currency.
        return round($spend / 1000000, 2);
    }

    public function __toString(): string
    {
        return json_encode([
            'Name' => self::name(),
            'Marketing chanel id' => $this->marketingChannelId,
            'Market id' => $this->marketId,
            'Currency id' => $this->currencyId,
            'Client id' => $this->config['client_id']
        ]);
    }

    public static function getIntervalOnSeconds(): int
    {
        return 60 * 15;
    }
}
