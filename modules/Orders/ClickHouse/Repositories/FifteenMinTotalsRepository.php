<?php

namespace Modules\Orders\ClickHouse\Repositories;

use App\ClickHouse\Repositories\BaseFifteenMinTotalsRepository;
use Carbon\Carbon;
use Modules\Orders\ClickHouse\QuickQueries\FifteenMinTotalsQuery;
use Modules\Orders\ClickHouse\QuickQueries\HistoryTotalsQuery;

class FifteenMinTotalsRepository extends BaseFifteenMinTotalsRepository
{
    public function getTotals(
        Carbon $from,
        int $currencyId,
        ?string $percentile,
        ?string $productVariantId,
        array $statusIds = [],
        array $marketIds = [],
        array $weight = []
    ): array
    {
        return $this->quickQuery(new FifteenMinTotalsQuery(...func_get_args()));
    }

    public function getHistoryTotals(
        int $currencyId,
        ?string $percentile,
        ?string $productVariantId,
        array $statusIds = [],
        array $marketIds = [],
        array $weight = []
    ): array
    {
        return $this->quickQuery(new HistoryTotalsQuery(...func_get_args()));
    }
}
