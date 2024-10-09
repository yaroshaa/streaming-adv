<?php

namespace Modules\Analytic\ClickHouse\QuickQueries;

use App\ClickHouse\ClickHouseException;
use App\ClickHouse\Models\AnalyticsEvents;
use App\ClickHouse\QuickQueries\BaseQuickQuery;
use App\ClickHouse\QuickQueries\QueryHelper;
use Carbon\Carbon;

/**
 * Class ConversionRateQuery
 * @package Modules\CompanyOverview\ClickHouse\QuickQueries
 */
class ConversionByMinutesQuery extends BaseQuickQuery
{
    private $siteId = null;
    private int $lastMinutes = 0;

    /**
     * ConversionRateQuery constructor.
     * @param null $siteId
     * @param int $lastMinutes
     */
    public function __construct($siteId = null, $lastMinutes = 15)
    {
        $this->siteId = $siteId;
        $this->lastMinutes = $lastMinutes;
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

        $from->subMinutes($this->lastMinutes);

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
                    (tc1.total/tc.total) * 100 as conversionRate
            FROM (
                SELECT SUM(quantity) total, date
                FROM (
                    SELECT session_id, 
                        count(*) as quantity, 
                        toDate(created_at) as date
                    FROM {$tableName}
                    {$where}
                    GROUP BY session_id, toDate(created_at), toMinute(created_at)
                ) t1
                GROUP BY date
            ) tc
            LEFT JOIN (
                SELECT SUM(quantity) total, date
                FROM (
                    SELECT session_id, 
                        count(*) as quantity, 
                        toDate(created_at) as date,
                        toMinute(created_at) as minute
                    FROM {$tableName}
                    {$where}
                    GROUP BY session_id, toDate(created_at), toMinute(created_at)
                    {$havingConditions}
                ) t1
                GROUP BY date
            ) tc1 USING date 
            ORDER BY date
        SQL;

    }
}
