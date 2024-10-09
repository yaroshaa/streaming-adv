<?php

namespace Modules\KpiOverview\ClickHouse\Repositories;

use App\ClickHouse\Repository;
use Carbon\Carbon;
use Modules\KpiOverview\ClickHouse\QuickQueries\TotalsQuery;

class KpiOverviewRepository extends Repository
{
    public function getTotals(Carbon $from, Carbon $to, int $currencyId, string $granularity): array
    {
        return $this->quickQuery(new TotalsQuery($from, $to, $currencyId, $granularity));
    }
}
