<?php

namespace Modules\ProductStatistic\ClickHouse\QuickQueries;

use App\ClickHouse\Models\OrderProduct;
use App\ClickHouse\QuickQueries\BaseQuickQuery;
use App\ClickHouse\Services\OrderStatQueryFilter;
use Exception;

class ProductsIdsWithOrdersQuery extends BaseQuickQuery
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

        $tableName = $this->clickhouseConfig->getTableName(OrderProduct::class);

        $andWhere = $filter->getWhereString(
            $filter->getDateQuery()
        );

        return <<<SQL
            SELECT DISTINCT product_variant_id
            FROM {$tableName}
                  where 1
                     {$andWhere}
            SQL;
    }
}
