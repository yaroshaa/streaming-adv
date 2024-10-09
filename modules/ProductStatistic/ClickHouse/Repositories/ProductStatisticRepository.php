<?php
namespace Modules\ProductStatistic\ClickHouse\Repositories;

use App\ClickHouse\ClickHouseException;
use Modules\ProductStatistic\ClickHouse\QuickQueries\PricesDynamicQuery;
use Modules\ProductStatistic\ClickHouse\QuickQueries\ProductsIdsWithOrdersQuery;
use Modules\ProductStatistic\ClickHouse\QuickQueries\ProfitDynamicQuery;
use Modules\ProductStatistic\ClickHouse\QuickQueries\OrdersCountDynamicQuery;
use Modules\ProductStatistic\ClickHouse\QuickQueries\QuantitiesDynamicQuery;
use App\ClickHouse\Repository;

class ProductStatisticRepository extends Repository
{
    /**
     * @param array $filters
     * @return array
     * @throws ClickHouseException
     */
    public function getPriceDynamic(array $filters): array
    {
        return $this->quickQuery(new PricesDynamicQuery(...func_get_args()));
    }

    /**
     * @param array $filters
     * @return array
     * @throws ClickHouseException
     */
    public function getProfitDynamic(array $filters): array
    {
        return $this->quickQuery(new ProfitDynamicQuery(...func_get_args()));
    }

    /**
     * @param array $filters
     * @return array
     * @throws ClickHouseException
     */
    public function getOrdersCountDynamic(array $filters): array
    {
        return $this->quickQuery(new OrdersCountDynamicQuery(...func_get_args()));
    }

    /**
     * @param array $filters
     * @return array
     * @throws ClickHouseException
     */
    public function getQuantitiesDynamic (array $filters): array
    {
        return $this->quickQuery(new QuantitiesDynamicQuery(...func_get_args()));
    }

    /**
     * @param array $filters
     * @return array
     * @throws ClickHouseException
     */
    public function getProductsIdsWithOrders (array $filters): array
    {
        return $this->quickQuery(new ProductsIdsWithOrdersQuery(...func_get_args()));
    }
}
