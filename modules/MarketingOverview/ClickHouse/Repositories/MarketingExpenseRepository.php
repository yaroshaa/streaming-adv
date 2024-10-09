<?php

namespace Modules\MarketingOverview\ClickHouse\Repositories;

use App\ClickHouse\Repositories\BaseMarketingExpenseRepository;
use Carbon\Carbon;
use Modules\MarketingOverview\ClickHouse\QuickQueries\ExpenseOverPeriodQuery;
use Modules\MarketingOverview\ClickHouse\QuickQueries\ExpenseSpendByMarketQuery;
use Modules\MarketingOverview\ClickHouse\QuickQueries\ExpenseSpendQuery;

/**
 * Class MarketingExpenseRepository
 * @package Modules\MarketingOverview\ClickHouse\Repositories
 */
class MarketingExpenseRepository extends BaseMarketingExpenseRepository
{
    public function getOverviewSpendByMarketQuery(Carbon $from, Carbon $to, string $granularity, int $currencyId): array
    {
        return $this->quickQuery(new ExpenseSpendByMarketQuery(...func_get_args()));
    }

    public function getExpenseOverPeriod(?Carbon $from, ?Carbon $to, string $granularity, int $currencyId): array
    {
        return $this->quickQuery(new ExpenseOverPeriodQuery(...func_get_args()));
    }

    public function getSpend(Carbon $from, Carbon $to, string $granularity, int $currencyId): array
    {
        return $this->quickQuery(new ExpenseSpendQuery(...func_get_args()));
    }
}
