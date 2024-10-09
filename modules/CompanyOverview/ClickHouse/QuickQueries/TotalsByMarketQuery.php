<?php

namespace Modules\CompanyOverview\ClickHouse\QuickQueries;

use App\ClickHouse\ClickHouseException;
use App\ClickHouse\Models\OrderProduct;
use App\ClickHouse\Models\OrderStatusHistory;
use App\ClickHouse\QuickQueries\BaseQuickQuery;
use App\ClickHouse\QuickQueries\QueryHelper;
use Carbon\Carbon;

class TotalsByMarketQuery extends BaseQuickQuery
{
    private Carbon $from;
    private Carbon $to;
    private int $currencyId;

    public function __construct(Carbon $from, Carbon $to, int $currencyId)
    {
        $this->from = $from;
        $this->to = $to;
        $this->currencyId = $currencyId;
    }

    /**
     * @inheritDoc
     * @throws ClickHouseException
     */
    function __toString(): string
    {
        $tableName = $this->clickhouseConfig->getTableName(OrderProduct::class);
        $statusHistoryTable = $this->clickhouseConfig->getTableName(OrderStatusHistory::class);

        $dbname = QueryHelper::getDbName();

        $where = QueryHelper::where([
            QueryHelper::getPeriodCondition($this->from, $this->to),
        ]);

        $previousWhere = QueryHelper::where([
            QueryHelper::getPeriodConditionForPreviousInterval($this->from, $this->to),
        ]);

        $sqlTemplate = <<< SQL
            WITH dictGet('{$dbname}.customers', 'created_at', customer_id) as customer_created_at
            SELECT ':interval'                                                                             as interval,
                   round(avg(total), 2)                                                                    as avg_total,
                   round(avg(profit), 2)                                                                   as avg_profit,
                   countIf(status_id = 4)                                                                  as total_packed,
                   countIf(status_id = 2)                                                                  as total_returned,
                   total_returned / count()                                                                as returned_percent,
                   count(DISTINCT customer_id)                                                             as customers,
                   count(DISTINCT if(toDate(updated_at) = toDate(customer_created_at), customer_id, null)) as new_customers,
                   new_customers / customers                                                               as new_customers_percent,
                   count(order_id)                                                                         as orders,
                   count(DISTINCT product_variant_id)                                                      as products_count,
                   round(sum(discount), 2)                                                                 as product_discount,
                   round(product_discount / sum(profit), 2)                                                as product_discount_percent,
                   avg(time_packed)                                                                        as time_packed
            FROM (
                     WITH dictGet('{$dbname}.exchange_rates', 'rate', tuple(currency_id, toUInt64({$this->currencyId}), toDate(updated_at))) as rate
                     SELECT *,
                            dictGet('{$dbname}.orders', 'order_status_id', tuple(order_id)) as status_id,
                            product_price * rate                                   as total,
                            product_profit * rate                                  as profit,
                            product_discount * rate                                as discount
                     FROM {$tableName}
                     :where
                     ) as main
            ANY LEFT JOIN (
                SELECT avg(diff) as time_packed, market_id
                FROM (
                      SELECT bfr.order_id                                     as order_id,
                             bfr.status_after                                 as status,
                             bfr.updated_at                                   as start_time,
                             aftr.updated_at                                  as end_time,
                             dateDiff('second', start_time, end_time)         as diff,
                             dictGet('{$dbname}.orders', 'market_id', tuple(order_id)) as market_id
                      FROM {$statusHistoryTable} bfr
                               ANY LEFT JOIN {$statusHistoryTable} aftr
                                         ON bfr.order_id = aftr.order_id
                                         AND bfr.status_after = aftr.status_before
                      WHERE end_time > start_time
                         )
                WHERE status = 5
                GROUP BY market_id
            ) as joined USING market_id
SQL;

        $current = str_replace([':interval', ':where'], ['current', $where], $sqlTemplate);
        $previous = str_replace([':interval', ':where'], ['previous', $previousWhere], $sqlTemplate);

        return <<< SQL
            {$current}
            UNION ALL
            {$previous}
SQL;
    }
}
