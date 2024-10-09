<?php

namespace Modules\Orders\ClickHouse\Repositories;

use App\ClickHouse\Repositories\BaseOrderRepository;
use Carbon\Carbon;
use Modules\Orders\ClickHouse\QuickQueries\OrderFeedQuery;

class OrderRepository extends BaseOrderRepository
{
    public function findBy(
        Carbon $from,
        int $currencyId,
        ?string $percentile,
        ?string $productVariantId,
        array $statusIds = [],
        array $marketIds = [],
        array $weight = []
    ): array
    {
        return $this->quickQuery(new OrderFeedQuery(...func_get_args()));
    }
}
