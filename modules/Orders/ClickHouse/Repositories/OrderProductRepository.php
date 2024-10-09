<?php

namespace Modules\Orders\ClickHouse\Repositories;

use App\ClickHouse\Repositories\BaseOrderProductRepository;
use Modules\Orders\ClickHouse\QuickQueries\FindProductsByOrderIdQuery;
use Modules\Orders\ClickHouse\QuickQueries\TopSellingProductsQuery;

class OrderProductRepository extends BaseOrderProductRepository
{
    public function getTopSellingProducts(
        int $currencyId,
        ?string $percentile,
        ?string $productVariantId,
        array $statusIds = [],
        array $marketIds = [],
        array $weight = []
    ): array
    {
        return $this->quickQuery(new TopSellingProductsQuery(...func_get_args()));
    }

    /**
     * @param string $orderId
     * @return array
     */
    public function findByOrderId(string $orderId = ''): array
    {
        return $this->quickQuery(new FindProductsByOrderIdQuery($orderId));
    }
}
