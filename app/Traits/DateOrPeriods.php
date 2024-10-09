<?php

namespace App\Traits;

use App\ClickHouse\DateGranularityInterface;
use Carbon\Carbon;
use Exception;

trait DateOrPeriods
{
    /**
     * @param Carbon $from
     * @param Carbon $to
     * @return Carbon[]
     */
    public static function getPreviousPeriodCarbon(Carbon $from, Carbon $to): array
    {
        return [$from->clone()->subDays($from->diffInDays($to)), $from];
    }

    /**
     * @param Carbon $date
     * @return Carbon[]
     */
    public static function getPreviousWeekPeriodOfDay(Carbon $date): array
    {
        return [$date->clone()->startOfDay()->subWeek(), $date->clone()->subWeek()->endOfDay()];
    }

    /**
     * @return Carbon[]
     */
    public static function getPeriodOnCurrentDay(): array
    {
        return [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()];
    }

    /**
     * @throws Exception
     */
    public static function getPeriods(string $dateGranularity): array
    {
        $dateStart = Carbon::now()->startOfDay();

        $dateEnd = clone $dateStart;

        $estimateDateStart = Carbon::now()->startOfDay();

        if ($dateGranularity === DateGranularityInterface::DAY_GRANULARITY) {
            $dateEnd->addDay();
            $estimateDateStart->subDay();
        } elseif ($dateGranularity === DateGranularityInterface::WEEK_GRANULARITY) {
            $dateEnd->addWeek();
            $estimateDateStart->subWeek();
        } elseif ($dateGranularity === DateGranularityInterface::MONTH_GRANULARITY) {
            $dateEnd->addMonth();
            $estimateDateStart->subMonth();
        } else {
            throw new Exception('Date Granularity should be  Daily | Weekly | Monthly');
        }

        return [
            'current' => [
                'start' => $dateStart,
                'end' => $dateEnd
            ],
            'estimate' => [
                'start' => $estimateDateStart,
                'end' => $dateStart
            ]
        ];
    }
}
