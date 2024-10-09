<?php

namespace Modules\MarketingOverview\ClickHouse\QuickQueries;

use App\ClickHouse\QuickQueries\BaseQuickQuery;
use App\ClickHouse\QuickQueries\QueryHelper as QH;
use Carbon\Carbon;
use Exception;

class WarehouseStatisticQuery extends BaseQuickQuery
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

        $granularity = QH::getDateGranularity($this->granularity, QH::FIELD_DATE_CREATED_AT);

        $where = QH::where([
            QH::getPeriodCondition($this->from, $this->to, QH::FIELD_DATE_CREATED_AT, QH::FORMAT_DATETIME)
        ]);

        return <<<SQL
WITH dictGet('{$dbName}.warehouses', 'name', warehouse_id) as warehouse_name

                    SELECT
                        {$granularity} AS date,
                        warehouse_id,
                        warehouse_name,
                        market_id,
                        any(t2.name) as market_name,
                        station,
                        sum(in_packing) as in_packing,
                        sum(open) as open,
                        sum(awaiting_stock) as awaiting_stock
                    FROM {$dbName}.warehouse_statistic t1
                    JOIN {$dbName}.markets t2 on t1.market_id=t2.remote_id
                    {$where}
                    GROUP BY date, warehouse_id, market_id, station
                    ORDER BY date, warehouse_name, market_name, station
SQL;
    }
}
