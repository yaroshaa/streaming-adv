<?php

namespace Modules\Analytic\ClickHouse\Repositories;

use App\ClickHouse\Repository;
use Carbon\Carbon;
use Modules\Analytic\ClickHouse\QuickQueries\ConversionByHoursQuery;
use Modules\Analytic\ClickHouse\QuickQueries\ConversionByMinutesQuery;
use Modules\Analytic\ClickHouse\QuickQueries\ConversionQuery;
use Modules\Analytic\ClickHouse\QuickQueries\ConversionRateQuery;
use Modules\Analytic\ClickHouse\QuickQueries\EventPropertiesQuery;
use Modules\Analytic\ClickHouse\QuickQueries\EventsQuery;

/**
 * Class AnalyticsRepository
 * @package Modules\Analytic\ClickHouse\Repositories
 */
class AnalyticsRepository extends Repository
{
    /**
     * @param Carbon $from
     * @param Carbon $to
     * @param string|null $event
     * @return array
     */
    public function getEvents(Carbon $from, Carbon $to, $event): array
    {
        return $this->quickQuery(new EventsQuery($from, $to, $event));
    }

    /**
     * @param Carbon $from
     * @param Carbon $to
     * @param $event
     * @return array
     */
    public function getEventProperties(Carbon $from, Carbon $to, $event)
    {
        return $this->quickQuery(new EventPropertiesQuery($from, $to, $event));
    }

    /**
     * @param Carbon $from
     * @param Carbon $to
     * @param null $siteId
     * @return array
     */
    public function getConversations(Carbon $from, Carbon $to, $siteId = null)
    {
        return $this->quickQuery(new ConversionQuery($from, $to, $siteId));
    }

    /**
     * @param int $siteId
     * @return array
     */
    public function getConversationsByHours(int $siteId)
    {
        return $this->quickQuery(new ConversionByHoursQuery($siteId));
    }

    /**
     * @param int $siteId
     * @param int $lastMinutes
     * @return array
     */
    public function getConversationsByMinutes(int $siteId, int $lastMinutes)
    {
        return $this->quickQuery(new ConversionByMinutesQuery($siteId, $lastMinutes));
    }


}
