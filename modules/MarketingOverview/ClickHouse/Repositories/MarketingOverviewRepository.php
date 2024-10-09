<?php

namespace Modules\MarketingOverview\ClickHouse\Repositories;

use App\ClickHouse\DateGranularityInterface;
use App\ClickHouse\Repository;
use App\ClickHouse\RepositoryInterface;
use Carbon\Carbon;
use Modules\MarketingOverview\ClickHouse\QuickQueries\ByStoresQuery;
use Modules\MarketingOverview\ClickHouse\QuickQueries\ConversationIndicatorsQuery;
use Modules\MarketingOverview\ClickHouse\QuickQueries\RevenueOverPeriodQuery;
use Modules\MarketingOverview\ClickHouse\QuickQueries\StoresTimeGranularity;
use Modules\MarketingOverview\ClickHouse\QuickQueries\TotalQuery;
use Modules\MarketingOverview\ClickHouse\QuickQueries\WarehouseStatisticQuery;
use Modules\MarketingOverview\ClickHouse\QuickQueries\WarehouseTimeGranularityStatisticByStationsQuery;
use Modules\MarketingOverview\ClickHouse\QuickQueries\WarehouseTimeGranularityStatisticQuery;

class MarketingOverviewRepository extends Repository implements RepositoryInterface
{
    public function getOverviewTotal(Carbon $from, Carbon $to, int $currencyId, string $granularity): array
    {
        return $this->quickQuery(new TotalQuery(...func_get_args()));
    }

    public function getWarehouseStatistic(Carbon $from, Carbon $to, string $granularity): array
    {
        $statistics = $this->quickQuery(new WarehouseStatisticQuery(...func_get_args()));

        $hourlyStatistics = $this->quickQuery(new WarehouseTimeGranularityStatisticQuery(
            $from,
            $to,
            DateGranularityInterface::HOUR_GRANULARITY)
        );

        $hourlyStatisticsByStations = $this->quickQuery(new WarehouseTimeGranularityStatisticByStationsQuery(
                $from,
                $to,
                DateGranularityInterface::HOUR_GRANULARITY)
        );

        return  [
            'statistics' => $statistics,
            'hourlyStatistics' => $hourlyStatistics,
            'hourlyStatisticsByStations' => $hourlyStatisticsByStations
        ];
    }

    public function getConversationIndicatorQuery(string $granularity): array
    {
        return $this->quickQuery(new ConversationIndicatorsQuery($granularity));
    }

    public function getOverPeriod(Carbon $from, Carbon $to, string $granularity, int $currencyId): array
    {
        return $this->quickQuery(new RevenueOverPeriodQuery(...func_get_args()));
    }

    public function getTimeGranularity(Carbon $from, Carbon $to, string $granularity): array
    {
        return $this->quickQuery(new StoresTimeGranularity(...func_get_args()));
    }

    public function getByStores(Carbon $from, Carbon $to, int $currencyId):array
    {
        return $this->quickQuery(new ByStoresQuery(...func_get_args()));
    }
}
