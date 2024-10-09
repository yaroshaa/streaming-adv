<?php


namespace App\ClickHouse\Views;


use App\ClickHouse\ClickHouseException;
use App\ClickHouse\Models\OrderProduct;
use App\ClickHouse\Queries\BaseQuery;
use App\ClickHouse\View;

class OrdersTotalsToday extends BaseQuery implements View
{
    /**
     * @return string
     * @throws ClickHouseException
     */
    public function getQuery(): string
    {
        $table = $this->clickhouseConfig->getTableName(OrderProduct::class);
        $name = self::getName();

        return "
            CREATE VIEW {$name} AS (
                SELECT order_id,
                       currency_id,
                       sum(product_price)  as total_price,
                       sum(product_profit) as total_profit,
                       sum(product_weight) as total_weight
                FROM {$table}
                WHERE updated_at >= toStartOfDay(today())
                GROUP BY order_id, currency_id
            )
        ";
    }

    /**
     * @return string
     */
    public static function getName(): string
    {
        return 'order_totals_today';
    }

}
