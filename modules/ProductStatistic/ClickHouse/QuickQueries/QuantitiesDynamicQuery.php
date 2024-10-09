<?php

namespace Modules\ProductStatistic\ClickHouse\QuickQueries;

use App\ClickHouse\Models\OrderProduct;
use App\ClickHouse\QuickQueries\BaseQuickQuery;
use App\ClickHouse\Services\OrderStatQueryFilter;
use Exception;

class QuantitiesDynamicQuery extends BaseQuickQuery
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

        return <<<SQL
            SELECT sum(product_qty) as value,
                   toDate(updated_at)                    as date
            FROM {$tableName}
                  where 1
                     {$andWhere}
            GROUP BY date
            ORDER BY date ASC
            SQL;
    }
}
