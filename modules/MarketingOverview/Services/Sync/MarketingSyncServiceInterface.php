<?php

namespace Modules\MarketingOverview\Services\Sync;

use Carbon\Carbon;

/**
 * Interface MarketingSyncServiceInterface
 * @package Modules\MarketingOverview\Services\Sync
 */
interface MarketingSyncServiceInterface
{
    /**
     * MarketingSyncServiceInterface constructor.
     * @param array $config
     */
    public function __construct(array $config);

    /**
     * Usage in logs on current time
     * @return string
     */
    public static function name(): string;

    /**
     * For getting config by that implemented class
     * @return string
     */
    public static function alias(): string;

    /**
     * Period(from) of taking data
     * @return Carbon
     */
    public function periodFrom(): Carbon;

    /**
     * Interval in seconds of periodic launching current sync
     * @return int
     */
    public static function getIntervalOnSeconds(): int;

    /**
     * Method must return total value by processing period
     * @return float
     */
    public function getAmount(): float;

    /**
     * Inner channel id
     * @return int
     */
    public function getChannelId(): int;

    /**
     * Inner remote market id
     * @return int
     */
    public function getMarketId(): int;

    /**
     * Inner currency id
     * @return int
     */
    public function getCurrencyId(): int;

    /**
     * Remote timezone
     * @return string
     */
    public function timezone(): string;

    /**
     * For shortly description in logs
     * @return string
     */
    public function __toString(): string;
}
