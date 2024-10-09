<?php

namespace Modules\CompanyOverview\ClickHouse\Repositories;

use App\ClickHouse\ClickHouseException;
use Carbon\Carbon;
use App\ClickHouse\Repository;
use App\ClickHouse\RepositoryInterface;
use DateTimeImmutable;
use Exception;
use Modules\CompanyOverview\ClickHouse\QuickQueries\OrdersQuery;
use Modules\CompanyOverview\ClickHouse\QuickQueries\PieChartDataQuery;
use Modules\CompanyOverview\ClickHouse\QuickQueries\StreamGraphDataQuery;
use Modules\CompanyOverview\ClickHouse\QuickQueries\TotalsByMarketQuery;
use Modules\CompanyOverview\ClickHouse\QuickQueries\TotalsQuery;

class CompanyOverviewRepository extends Repository implements RepositoryInterface
{
    public function getOrdersBy(Carbon $from, Carbon $to, int $currencyId): array
    {
        return $this->quickQuery(new OrdersQuery($from, $to, $currencyId));
    }

    /**
     * @throws ClickHouseException
     * @throws Exception
     */
    public function getStreamGraphData(Carbon $from, Carbon $to, int $currencyId): array
    {
        $result = $this->quickQuery(new StreamGraphDataQuery($from, $to, $currencyId));

        $data = [];

        foreach ($result as $row) {
            $dateTime = new DateTimeImmutable($row['date']);
            $date = $dateTime->format('Y-m-d');
            $marketName = $row['market_name'] ?: 'noname';
            $data[$date][$marketName] = $row['total'];
            $data[$date]['date'] = $date;
        }

        return array_values($data);
    }

    public function getPieChartData(Carbon $from, Carbon $to, int $currencyId): array
    {
        return $this->quickQuery(new PieChartDataQuery($from, $to, $currencyId));
    }

    public function getTotals(Carbon $from, Carbon $to, int $currencyId): array
    {
        return $this->quickQuery(new TotalsQuery($from, $to, $currencyId));
    }

    public function getTotalsByMarket(Carbon $from, Carbon $to, int $currencyId): array
    {
        return $this->quickQuery(new TotalsByMarketQuery($from, $to, $currencyId));
    }
}
