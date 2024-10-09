<?php

namespace App\ClickHouse;

interface DateGranularityInterface
{
    public const EVERY_45_SECONDS_GRANULARITY = 'Every 45 seconds';
    public const EVERY_30_MINUTES_GRANULARITY = 'Every 30 minutes';
    public const EVERY_15_MINUTES_GRANULARITY = 'Every 15 minutes';
    public const HOUR_GRANULARITY = 'Hourly';
    public const DAY_GRANULARITY = 'Daily';
    public const WEEK_GRANULARITY = 'Weekly';
    public const MONTH_GRANULARITY = 'Monthly';
}
