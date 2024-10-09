<?php

namespace Modules\MarketingOverview\ClickHouse\QuickQueries;

use App\ClickHouse\Models\OrderProduct;
use App\ClickHouse\QuickQueries\BaseQuickQuery;
use App\ClickHouse\QuickQueries\QueryHelper;
use App\ClickHouse\QuickQueries\QueryHelper as QH;
use Carbon\Carbon;
use Exception;

class RevenueOverPeriodQuery extends BaseQuickQuery
{
    private Carbon $from;
    private Carbon $to;
    private string $granularity;
    private int $currencyId;

    public function __construct(Carbon $from, Carbon $to, string $granularity, int $currencyId)
    {
        $this->from = $from;
        $this->to = $to;
        $this->granularity = $granularity;
        $this->currencyId = $currencyId;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    function __toString(): string
    {
        $tableName = $this->clickhouseConfig->getTableName(OrderProduct::class);
        $dbName = QueryHelper::getDbName();
        $where = QueryHelper::where([
            QH::getPeriodCondition($this->from, $this->to)
        ]);

        $granularity = QueryHelper::getDateGranularity($this->granularity);

        return <<<SQL
            SELECT {$granularity}                                    as date,
                   round(sum(revenue), 2)                                                                  as revenue
            FROM (
                  WITH dictGet('{$dbName}.exchange_rates', 'rate', tuple(currency_id, toUInt64({$this->currencyId}), toDate(updated_at))) as rate
                  SELECT *,
                         dictGet('{$dbName}.orders', 'order_status_id', tuple(order_id)) as status_id,
                         product_price * rate  as revenue
                  FROM {$tableName}
                  $where
            )
            GROUP BY date
            ORDER BY date DESC
SQL;
    }
}
