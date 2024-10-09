<?php

namespace Modules\StoreToday\ClickHouse\QuickQueries;

use App\ClickHouse\ClickHouseException;
use App\ClickHouse\Models\OrderProduct;
use App\ClickHouse\QuickQueries\BaseQuickQuery;
use App\ClickHouse\QuickQueries\QueryHelper;
use App\ClickHouse\QuickQueries\QueryHelper as QH;
use Carbon\Carbon;
use Exception;

class IndicatorQuery extends BaseQuickQuery
{
    private Carbon $from;
    private Carbon $to;
    private array $marketIds;
    private int $currencyId;

    public function __construct(Carbon $from, Carbon $to, array $marketIds, int $currencyId)
    {
        $this->from = $from;
        $this->to = $to;
        $this->marketIds = $marketIds;
        $this->currencyId = $currencyId;
    }

    /**
     * @throws ClickHouseException
     * @throws Exception
     */
    public function __toString(): string
    {
        $dbname = QueryHelper::getDbName();

        $tableName = $this->clickhouseConfig->getTableName(OrderProduct::class);

        $where = QueryHelper::where([
            QueryHelper::getPeriodCondition($this->from, $this->to),
            QH::skipEmpty($this->marketIds, fn($value) => QH::in('market_id', $value)),
        ]);

        return <<<SQL
            WITH dictGet('{$dbname}.customers', 'created_at', customer_id)                                 as customer_created_at
            SELECT toDate(updated_at)                                                                      as date,
                   toHour(updated_at)                                                                      as hour,
                   round(avg(total), 2)                                                                    as avg_total,
                   round(avg(profit), 2)                                                                   as avg_profit,
                   countIf(status_id = 1)                                                                  as total_packed,
                   countIf(status_id = 2)                                                                  as total_returned,
                   total_returned / count()                                                                as returned_percent,
                   count(DISTINCT customer_id)                                                             as customers,
                   count(DISTINCT if(toDate(updated_at) = toDate(customer_created_at), customer_id, null)) as new_customers,
                   new_customers / customers                                                               as new_customers_percent,
                   count(DISTINCT order_id)                                                                as orders,
                   count(DISTINCT product_variant_id)                                                      as products_count,
                   round(sum(discount), 2)                                                                 as product_discount,
                   round(product_discount / sum(profit), 2)                                                as product_discount_percent
            FROM (
                  WITH dictGet('{$dbname}.exchange_rates', 'rate', tuple(currency_id, toUInt64({$this->currencyId}), toDate(updated_at))) as rate
                  SELECT *,
                         dictGet('{$dbname}.orders', 'order_status_id', tuple(order_id)) as status_id,
                         product_price * rate  as total,
                         product_profit * rate as profit,
                         product_discount * rate as discount
                  FROM {$tableName}
                  $where
                 )
            GROUP BY date, hour
            ORDER BY hour
SQL;
    }
}
