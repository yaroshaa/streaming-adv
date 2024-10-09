<?php

namespace Modules\StoreToday\ClickHouse\Repositories;

use App\ClickHouse\Repository;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Arr;
use Modules\StoreToday\ClickHouse\QuickQueries\IndicatorQuery;
use Modules\StoreToday\ClickHouse\QuickQueries\KpiQuery;
use Modules\StoreToday\ClickHouse\QuickQueries\ProfitQuery;

class StoreTodayRepository extends Repository
{
    public function getKpi(Carbon $from, Carbon $to, array $marketIds, int $currencyId, string $granularity): array
    {
        return $this->quickQuery(new KpiQuery($from, $to, $marketIds, $currencyId, $granularity));
    }

    /**
     * @param Carbon $from
     * @param Carbon $to
     * @param array $marketIds
     * @param int $currencyId
     * @return array
     * @throws Exception
     */
    public function getIndicatorData(Carbon $from, Carbon $to, array $marketIds, int $currencyId): array
    {
        $srcData = $this->quickQuery(new IndicatorQuery(
            $from,
            $to,
            $marketIds,
            $currencyId
        ));

        $date = new \DateTime($from->toDateTimeLocalString());

        $data = [];

        for ($i = 0; $i < 24; $i++) {

            $key = array_search($i, array_column($srcData, 'hour'));

            if (count($srcData) > 0 && $key !== false) {
                $changedData = $srcData[$key];
                $changedData['date'] = $date->format('Y-m-d H:i');
                $data[] = $changedData;
            } else {
                $data[] = [
                    'avg_profit' => 0,
                    'avg_total' => 0,
                    'customers' => 0,
                    'date' => $date->format('Y-m-d H:i'),
                    'hour' =>  $i,
                    'new_customers' => 0,
                    'new_customers_percent' => 0,
                    'orders' => 0,
                    'product_discount' => 0,
                    'product_discount_percent' => 0,
                    'products_count' => 0,
                    'returned_percent' => 0,
                    'total_packed' => 0,
                    'total_returned' => 0,
                ];
            }

            $date->add(new \DateInterval('PT1H'));
        }

        return $data;
    }

    /**
     * @param Carbon $from
     * @param Carbon $to
     * @param int $currencyId
     * @return array
     */
    public function getProfit(Carbon $from, Carbon $to, int $currencyId): array
    {
        return Arr::first($this->quickQuery(new ProfitQuery($from, $to, $currencyId)));
    }
}
