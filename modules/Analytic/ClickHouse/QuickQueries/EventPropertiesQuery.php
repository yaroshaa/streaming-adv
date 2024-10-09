<?php

namespace Modules\Analytic\ClickHouse\QuickQueries;

use App\ClickHouse\ClickHouseException;
use App\ClickHouse\Models\AnalyticsEventProperties;
use App\ClickHouse\Models\AnalyticsEvents;
use App\ClickHouse\QuickQueries\BaseQuickQuery;
use App\ClickHouse\QuickQueries\QueryHelper;
use Carbon\Carbon;

/**
 * Class EventPropertiesQuery
 * @package Modules\Analytic\ClickHouse\QuickQueries
 */
class EventPropertiesQuery extends BaseQuickQuery
{
    private Carbon $from;
    private Carbon $to;
    private $event;

    /**
     * EventsQuery constructor.
     * @param Carbon $from
     * @param Carbon $to
     * @param string|null $event
     */
    public function __construct(Carbon $from, Carbon $to, string $event = null)
    {
        $this->from = $from;
        $this->to = $to;
        $this->event = $event;
    }

    /**
     * @return string
     * @throws ClickHouseException
     */
    function __toString(): string
    {
        $tableName = $this->clickhouseConfig->getTableName(AnalyticsEvents::class);
        $eventPropertiesTableName = $this->clickhouseConfig->getTableName(AnalyticsEventProperties::class);

        $conditions = [
            QueryHelper::getPeriodCondition($this->from, $this->to, QueryHelper::FIELD_DATE_CREATED_AT)
        ];

        if ($this->event) {
            $conditions[] = sprintf("name='%s'", $this->event);
        }


        $where = QueryHelper::where($conditions);

        return <<<SQL
            SELECT {$tableName}.name as event_name, {$eventPropertiesTableName}.*
            FROM {$tableName}
            LEFT JOIN {$eventPropertiesTableName} USING event_id
            {$where}
        SQL;

    }


}
