<?php

namespace Modules\MarketingOverview\Services\Sync;

use Exception;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Log;

abstract class BaseSyncService
{
    public int $marketId;
    public int $currencyId;
    public int $marketingChannelId;
    public string $timezone;

    protected array $config;
    protected Logger $logger;

    /**
     * @throws Exception
     */
    public function __construct(array $config)
    {
        $this->logger = Log::channel('marketing-overview');
        $this->config = $config;

        if (empty($this->config['market_remote_id'])) {
            throw new Exception("Market remote id not exist");
        }

        if (empty($this->config['currency_id'])) {
            throw new Exception("Currency id not exist");
        }

        if (empty($this->config['marketing_channel_id'])) {
            throw new Exception("Marketing channel id not exist");
        }

        $this->marketId = $config['market_remote_id'];
        $this->currencyId = $config['currency_id'];
        $this->marketingChannelId = $config['marketing_channel_id'];

        $this->timezone = $config['timezone'] ?? 'UTC';

        $this->init();
    }

    public function init()
    {

    }

    public function getChannelId(): int
    {
        return $this->marketingChannelId;
    }

    public function getMarketId(): int
    {
        return $this->marketId;
    }

    public function getCurrencyId(): int
    {
        return $this->currencyId;
    }

    public function timezone(): string
    {
        return $this->timezone;
    }
}
