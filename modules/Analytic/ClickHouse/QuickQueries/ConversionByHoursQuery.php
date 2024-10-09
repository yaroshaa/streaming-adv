<?php

namespace Modules\Analytic\ClickHouse\QuickQueries;

use App\ClickHouse\ClickHouseException;
use App\ClickHouse\Models\AnalyticsEvents;
use App\ClickHouse\QuickQueries\BaseQuickQuery;
use App\ClickHouse\QuickQueries\QueryHelper;
use Carbon\Carbon;

/**
 * Class ConversionByHoursQuery
 * @package Modules\CompanyOverview\ClickHouse\QuickQueries
 */
class ConversionByHoursQuery extends BaseQuickQuery
{
    private $siteId = null;

    /**
     * ConversionRateQuery constructor.
     * @param null $siteId
     */
    public function __construct($siteId = null)
    {
        $this->siteId = $siteId;
    }

    /**
     * @return string
     * @throws ClickHouseException
     */
    function __toString(): string
    {
        $tableName = $this->clickhouseConfig->getTableName(AnalyticsEvents::class);

        $from = new Carbon();
        $to = clone $from;

        $from->subHours(24);

        $whereConditions = [
            QueryHelper::getPeriodCondition($from, $to, QueryHelper::FIELD_DATE_CREATED_AT),
            sprintf("name='%s'", AnalyticsEvents::EVENT_PAGEVIEW)
        ];

        if ($this->siteId) {
            $whereConditions[] = sprintf('site_id=%s', $this->siteId);
        }

        $havingConditions = [
            sprintf("quantity > 1")
        ];

        $where = QueryHelper::where($whereConditions);
        $havingConditions = QueryHelper::having($havingConditions);

        return <<<SQL
        
            SELECT tc.date, 
                   tc.total as totalVisitors, 
                   tc1.total as totalConversions, 
                   (tc1.total/tc.total) * 100 as conversionRate,
                   hour
            FROM (
                SELECT SUM(quantity) total, date, hour
                FROM (
                    SELECT session_id, count(*) as quantity, toDate(created_at) as date, toHour(created_at) as hour
                    FROM {$tableName}
                    {$where}
                    GROUP BY session_id, toDate(created_at), toHour(created_at)
                ) t1
                GROUP BY date, hour
            ) tc
            LEFT JOIN (
                SELECT SUM(quantity) total, date, hour
                FROM (
                    SELECT session_id, count(*) as quantity, toDate(created_at) as date, toHour(created_at) as hour
                    FROM {$tableName}
                    {$where}
                    GROUP BY session_id, toDate(created_at), toHour(created_at)
                    {$havingConditions}
                ) t1
                GROUP BY date, hour
            ) tc1 USING date, hour
            ORDER BY date, hour
        SQL;

    }
}
