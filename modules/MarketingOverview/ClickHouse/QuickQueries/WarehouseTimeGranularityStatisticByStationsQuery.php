<?php

namespace Modules\MarketingOverview\ClickHouse\QuickQueries;

use App\ClickHouse\QuickQueries\BaseQuickQuery;
use App\ClickHouse\QuickQueries\QueryHelper as QH;
use Carbon\Carbon;
use Exception;

/**
 * Class WarehouseTimeGranularityStatisticByStationsQuery
 * @package Modules\MarketingOverview\ClickHouse\QuickQueries
 */
class WarehouseTimeGranularityStatisticByStationsQuery extends BaseQuickQuery
{
    private Carbon $from;
    private Carbon $to;
    private string $granularity;

    public function __construct(Carbon $from, Carbon $to, string $granularity)
    {
        $this->from = $from;
        $this->to = $to;
        $this->granularity = $granularity;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    function __toString(): string
    {
        $dbName = QH::getDbName();

        $where = QH::where([
            QH::getPeriodCondition($this->from, $this->to, QH::FIELD_DATE_CREATED_AT, QH::FORMAT_DATETIME)
        ]);

        $timeAlias = QH::getDateGranularityAlias($this->granularity);

        return <<<SQL
            SELECT 
                warehouse_id,
                toHour(created_at) AS hour,
                station,
                sum(in_packing) as in_packing,
                sum(open) as open,
                sum(awaiting_stock) as awaiting_stock
            FROM {$dbName}.warehouse_statistic t1
            JOIN {$dbName}.markets t2 on t1.market_id=t2.remote_id
            {$where}
            GROUP BY warehouse_id, station,  {$timeAlias} 
            ORDER BY warehouse_id, station,  {$timeAlias}
        SQL;
    }
}
