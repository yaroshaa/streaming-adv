<?php

namespace Modules\Orders\ClickHouse\QuickQueries;

use App\ClickHouse\ClickHouseException;
use App\ClickHouse\Models\OrderProduct;
use App\ClickHouse\QuickQueries\BaseQuickQuery;
use App\ClickHouse\QuickQueries\QueryHelper;

class FindProductsByOrderIdQuery extends BaseQuickQuery
{
    private string $orderId;

    public function __construct(string $orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * @inheritDoc
     * @throws ClickHouseException
     */
    function __toString(): string
    {
        $where = QueryHelper::where(QueryHelper::keyValueCondition([
            'order_id' => $this->orderId
        ]));
        $dbName = QueryHelper::getDbName();

        $tableName = $this->clickhouseConfig->getTableName(OrderProduct::class);

        return <<<SQL
            SELECT order_id,
                   product_variant_id,
                   product_qty,
                   updated_at,
                   customer_id,
                   market_id,
                   currency_id,
                   dictGet('{$dbName}.product_variants', 'name', tuple(product_variant_id))       as product_name,
                   round(product_weight, 2)   as product_weight,
                   round(product_profit, 2)   as product_profit,
                   round(product_price, 2)    as product_price,
                   product_discount,
                   updated_at
            FROM {$tableName}
            {$where}
SQL;
    }
}
