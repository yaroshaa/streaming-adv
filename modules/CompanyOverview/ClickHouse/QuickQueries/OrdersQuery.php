<?php

namespace Modules\CompanyOverview\ClickHouse\QuickQueries;

use App\ClickHouse\ClickHouseException;
use App\ClickHouse\Models\OrderProduct;
use App\ClickHouse\QuickQueries\BaseQuickQuery;
use App\ClickHouse\QuickQueries\QueryHelper;
use Carbon\Carbon;

class OrdersQuery extends BaseQuickQuery
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
     * @throws ClickHouseException
     */
    function __toString(): string
    {
        $tableName = $this->clickhouseConfig->getTableName(OrderProduct::class);
        $dbname = QueryHelper::getDbName();

        $where = QueryHelper::where([
            QueryHelper::getPeriodCondition($this->from, $this->to),
        ]);

        return <<<SQL
            WITH dictGet('{$dbname}.exchange_rates', 'rate',
                         tuple(currency_id, toUInt64({$this->currencyId}), toDate(updated_at))) as rate
            SELECT order_id,
                   product_variant_id,
                   product_qty,
                   updated_at,
                   dictGet('{$dbname}.orders', 'order_status_id', tuple(order_id))    as status_id,
                   dictGet('{$dbname}.order_statuses', 'name', status_id)      as status_name,
                   dictGet('{$dbname}.order_statuses', 'color', status_id)     as status_color,
                   customer_id,
                   dictGet('{$dbname}.customers', 'name', customer_id)   as customer_name,
                   dictGet('{$dbname}.customers', 'created_at', customer_id)   as customer_created_at,
                   market_id,
                   currency_id,
                   dictGet('{$dbname}.orders', 'address_id', tuple(order_id))   as address_id,
                   dictGet('{$dbname}.addresses', 'address', address_id) as address,
                   dictGet('{$dbname}.addresses', 'lat', address_id)     as address_lat,
                   dictGet('{$dbname}.addresses', 'lng', address_id)     as address_lng,
                   dictGet('{$dbname}.markets', 'name', market_id)       as market_name,
                   dictGet('{$dbname}.markets', 'icon_link', market_id)  as market_icon_link,
                   round(product_weight, 2)                            as product_weight,
                   round(product_profit * rate, 2)                     as product_profit,
                   round(product_price * rate, 2)                      as product_price,
                   product_discount,
                   dictGet('{$dbname}.product_variants', 'name', tuple(product_variant_id))       as product_name,
                   dictGet('{$dbname}.product_variants', 'link', tuple(product_variant_id))       as product_link,
                   dictGet('{$dbname}.product_variants', 'image_link', tuple(product_variant_id)) as product_image_link
            FROM {$tableName}
            $where
            LIMIT 100
SQL;
    }
}
