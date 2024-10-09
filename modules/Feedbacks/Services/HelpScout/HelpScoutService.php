<?php

namespace Modules\Feedbacks\Services\HelpScout;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;

/**
 * Class HelpScoutService
 * @package Modules\Feedbacks\Services\HelpScout
 */
class HelpScoutService
{
    private array $config;

    /**
     * HelpScoutService constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @param int $id
     * @return int
     */
    public function getMarketByMailboxId(int $id): int
    {
        return (int)Arr::get($this->config, 'webhooks.' . $id, 0);
    }

    /**
     * @return int
     */
    public function getSourceId(): int
    {
        return (int)Arr::get($this->config, 'remote_source_id', 0);
    }

    public function getLogger(): LoggerInterface
    {
        return Log::channel('helpscout-webhook');
    }
}
