<?php

namespace Modules\ProductStatistic\ClickHouse\QuickQueries;

use App\ClickHouse\Models\OrderProduct;
use App\ClickHouse\QuickQueries\BaseQuickQuery;
use App\ClickHouse\Services\OrderStatQueryFilter;
use Exception;

class PricesDynamicQuery extends BaseQuickQuery
{
    private array $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }
    /**
     * @return string
     * @throws Exception
     */
    public function __toString(): string
    {
        $filter = new OrderStatQueryFilter($this->data);

        $andWhere = $filter->getWhereString(
            $filter->getProductIdQuery(),
            $filter->getDateQuery(),
            $filter->getMarketQuery(),
        );

        $tableName = $this->clickhouseConfig->getTableName(OrderProduct::class);
        $dbname = config('clickhouse.dbname');

        return <<<SQL
            SELECT round(sum(sum) / count(sum)) as value,
                   toDate(updated_at)           as date
            FROM (
                  WITH dictGet('{$dbname}.exchange_rates', 'rate',
                               tuple(currency_id, toUInt64({$filter->getCurrencyId()}), toDate(updated_at))) as rate
                  SELECT product_price * rate as sum,
                         updated_at
                  FROM {$tableName}
                  where 1
                    {$andWhere}
                     )
            GROUP BY date
            ORDER BY date ASC
            SQL;
    }
}
