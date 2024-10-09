<?php

namespace Modules\MarketingOverview\Services\Calculate;

use App\ClickHouse\DateGranularityInterface;
use App\ClickHouse\QuickQueries\QueryHelper;
use Carbon\Carbon;
use DateInterval;
use Exception;
use Illuminate\Support\Arr;

class ByStoresService
{
    private array $byHourByCurrentDayData;
    private array $by30MinutesByCurrentDayData;
    private array $spendData;
    private array $byStoreCurrentPeriodData;
    private array $byStorePreviousPeriodData;

    public function __construct(
        array $byHourByCurrentDayData,
        array $by30MinutesByCurrentDayData,
        array $spendData,
        array $byStoreCurrentPeriodData,
        array $byStorePreviousPeriodData
    )
    {
        $this->byHourByCurrentDayData = $byHourByCurrentDayData;
        $this->by30MinutesByCurrentDayData = $by30MinutesByCurrentDayData;
        $this->spendData = $spendData;
        $this->byStoreCurrentPeriodData = $byStoreCurrentPeriodData;
        $this->byStorePreviousPeriodData = $byStorePreviousPeriodData;
    }

    /**
     * @throws Exception
     */
    public function getStatistic(): array
    {
        $prepareHourlyData = $this->prepareTimeData($this->byHourByCurrentDayData);

        $prepareEvery30MinutesData = $this->prepareTimeData($this->by30MinutesByCurrentDayData, DateGranularityInterface::EVERY_30_MINUTES_GRANULARITY);

        $spendData = $this->spendData;

        $prepareDate = $this->prepareStoresData($this->byStoreCurrentPeriodData);

        $prepareEstimateData = $this->prepareStoresData($this->byStorePreviousPeriodData);

        $mergedData = $this->mergeStoresData($prepareDate, $prepareEstimateData);
        $mergedData = $this->mergePerHourData($mergedData, $prepareHourlyData);
        $mergedData = $this->mergeLast30MinutesTime($mergedData, $prepareEvery30MinutesData);

        return $this->mergeStoreSpendData($mergedData, $spendData);
    }


    /**
     * @param array $data
     * @param string $granularity
     * @return array
     * @throws Exception
     */
    private function prepareTimeData(array $data, string $granularity = DateGranularityInterface::HOUR_GRANULARITY): array
    {
        $result = [];

        $timeAlias = QueryHelper::getDateGranularityAlias($granularity);

        foreach ($data as $item) {
            $date = $item['date'];
            $marketId = $item['market_id'];

            if (!isset($result[$date][$marketId])) {
                if ($timeAlias === 'hour') {
                    for ($i = 0; $i < 24; $i++) {
                        $result[$date][$marketId][] = [];
                    }
                }

                if ($timeAlias === 'minutes30') {
                    $today = Carbon::now()->toDateTime();
                    $today->setTime(0, 0, 0);

                    for ($i = 0; $i < 48; $i++) {
                        $result[$date][$marketId][$today->format('Y-m-d H:i:s')] = [];
                        $today->add(new DateInterval('PT30M'));
                    }
                }
            }


            $result[$date][$marketId][$item[$timeAlias]] = [
                "revenue" => $item['revenue'],
                "cnt_orders" => $item['cnt_orders'],
                "contribution_margin" => $item['contribution_margin'],
                "marketing_expense" => $item['marketing_expense'],
                "cmam" => $item['cmam'],
                "packing_cost" => $item['packing_cost'],
                "spend" => $item['spend'],
                "active_users" => $item['active_users'],
                "add_to_cart" => $item['add_to_cart'],
                "conversion_rate" => $item['conversion_rate'],
                "spend_ratio" => $item['spend_ratio'],
                "contribution_margin_ratio" => $item['contribution_margin_ratio'],
            ];
        }

        return $result;
    }

    /**
     * Prepare data
     * @param array $data
     * @return array
     */
    private function prepareStoresData(array $data): array
    {
        $prepareResult = [];

        foreach ($data as $marketingOverviewByStore) {
            $key = array_search($marketingOverviewByStore['market_id'], array_column($prepareResult, 'id'));

            if (!$key && $key !== 0) {
                $prepareResult[] = [
                    'id' => $marketingOverviewByStore['market_id'],
                    'name' => $marketingOverviewByStore['market_name'],
                    'icon_link' => $marketingOverviewByStore['market_icon_link'],
                    'color' => $marketingOverviewByStore['market_color'],
                    'revenue' => [
                        'per_day' => [$marketingOverviewByStore['revenue']],
                        'total' => $marketingOverviewByStore['revenue'],
                        'estimate' => 0
                    ],
                    'orders' => [
                        'per_day' => [$marketingOverviewByStore['cnt_orders']],
                        'total' => $marketingOverviewByStore['cnt_orders'],
                        'estimate' => 0
                    ],
                    'contribution_margin' => [
                        'per_day' => [$marketingOverviewByStore['contribution_margin']],
                        'total' => $marketingOverviewByStore['contribution_margin'],
                        'estimate' => 0
                    ],
                    'spend' => [
                        'per_day' => [$marketingOverviewByStore['spend']],
                        'total' => $marketingOverviewByStore['spend'],
                        'estimate' => 0
                    ],
                    'conversion_rate' => [
                        'per_day' => [$marketingOverviewByStore['conversion_rate']],
                        'total' => $marketingOverviewByStore['conversion_rate'],
                        'estimate' => 0
                    ],
                    'profit' => [
                        'per_day' => [$marketingOverviewByStore['contribution_margin']],
                        'total' => $marketingOverviewByStore['contribution_margin'],
                        'estimate' => 0
                    ],
                    'spend_ratio' => [
                        'per_day' => [$marketingOverviewByStore['spend_ratio']],
                        'total' => $marketingOverviewByStore['spend_ratio'],
                        'estimate' => 0
                    ],
                    'contribution_margin_ratio' => [
                        'per_day' => [$marketingOverviewByStore['contribution_margin_ratio']],
                        'total' => $marketingOverviewByStore['contribution_margin_ratio'],
                        'estimate' => 0
                    ],
                    'cmam' => [
                        'per_day' => [$marketingOverviewByStore['cmam']],
                        'total' => $marketingOverviewByStore['cmam'],
                        'estimate' => 0
                    ],
                    'packing_cost' => $marketingOverviewByStore['packing_cost'],
                    'aov' => $marketingOverviewByStore['revenue'] / $marketingOverviewByStore['cnt_orders'],
                    'active_users' => (int) $marketingOverviewByStore['active_users'],
                    'add_to_cart' => (int) $marketingOverviewByStore['add_to_cart'],
                ];
            } else {
                $totalRevenue = array_sum($prepareResult[$key]['revenue']['per_day']);
                $totalOrders = array_sum($prepareResult[$key]['orders']['per_day']);

                $prepareResult[$key]['revenue']['per_day'][] = $marketingOverviewByStore['revenue'];
                $prepareResult[$key]['revenue']['total'] = $totalRevenue;

                $prepareResult[$key]['orders']['per_day'][] = $marketingOverviewByStore['cnt_orders'];
                $prepareResult[$key]['orders']['total'] = $totalOrders;

                $prepareResult[$key]['contribution_margin']['per_day'][] = $marketingOverviewByStore['contribution_margin'];
                $prepareResult[$key]['contribution_margin']['total'] = array_sum($prepareResult[$key]['contribution_margin']['per_day']);

                $prepareResult[$key]['spend']['per_day'][] = $marketingOverviewByStore['spend'];
                $prepareResult[$key]['spend']['total'] = array_sum($prepareResult[$key]['spend']['per_day']);

                $prepareResult[$key]['conversion_rate']['per_day'][] = $marketingOverviewByStore['conversion_rate'];
                $prepareResult[$key]['conversion_rate']['total'] = array_sum($prepareResult[$key]['conversion_rate']['per_day']);

                $prepareResult[$key]['profit']['per_day'][] = $marketingOverviewByStore['contribution_margin'];
                $prepareResult[$key]['profit']['total'] = array_sum($prepareResult[$key]['profit']['per_day']);

                $prepareResult[$key]['spend_ratio']['per_day'][] = $marketingOverviewByStore['spend_ratio'];
                $prepareResult[$key]['spend_ratio']['total'] = array_sum($prepareResult[$key]['spend_ratio']['per_day']);

                $prepareResult[$key]['contribution_margin_ratio']['per_day'][] = $marketingOverviewByStore['contribution_margin_ratio'];
                $prepareResult[$key]['contribution_margin_ratio']['total'] = array_sum($prepareResult[$key]['contribution_margin_ratio']['per_day']);

                $prepareResult[$key]['cmam']['per_day'][] = $marketingOverviewByStore['cmam'];
                $prepareResult[$key]['cmam']['total'] = array_sum($prepareResult[$key]['cmam']['per_day']);

                $prepareResult[$key]['packing_cost'] += $marketingOverviewByStore['packing_cost'];
                $prepareResult[$key]['packing_cost'] = $totalRevenue / $totalOrders;
            }
        }

        return $prepareResult;
    }

    /**
     * Merge data with estimate data.
     * @param array $data
     * @param array $estimateData
     * @return array
     */
    private function mergeStoresData(array $data, array $estimateData): array
    {
        $concatenateData = [];

        foreach ($data as $store) {
            $key = array_search($store['id'], array_column($estimateData, 'id'));

            if ($key || $key === 0) {
                $store['orders']['estimate'] = $estimateData[$key]['orders']['total'];
                $store['revenue']['estimate'] = $estimateData[$key]['revenue']['total'];
                $store['contribution_margin']['estimate'] = $estimateData[$key]['contribution_margin']['total'];
                $store['spend']['estimate'] = $estimateData[$key]['spend']['total'];
                $store['conversion_rate']['estimate'] = $estimateData[$key]['conversion_rate']['total'];
                $store['profit']['estimate'] = $estimateData[$key]['profit']['total'];
            }

            $concatenateData[] = $store;
        }

        return $concatenateData;
    }

    private function mergePerHourData(array $data, array $timeData): array
    {
        $today = Carbon::now()->toDateTime();
        $date = $today->format('Y-m-d');
        $concatenateData = [];

        foreach ($data as $store) {
            if(isset($timeData[$date]) && array_key_exists($store['id'], $timeData[$date])) {
                foreach ($timeData[$date][$store['id']] as $timeDataItem) {
                    if (isset($timeDataItem['conversion_rate'])) {
                        $store['conversion_rate']['per_hour'][] = $timeDataItem['conversion_rate'];
                        $store['contribution_margin']['per_hour'][] = $timeDataItem['contribution_margin'];
                        $store['spend_ratio']['per_hour'][] = $timeDataItem['spend_ratio'];
                        $store['revenue']['per_hour'][] = $timeDataItem['revenue'];
                    } else {
                        $store['conversion_rate']['per_hour'][] = 0;
                        $store['contribution_margin']['per_hour'][] = 0;
                        $store['spend_ratio']['per_hour'][] = 0;
                        $store['revenue']['per_hour'][] = 0;
                    }
                }
            }

            $concatenateData[] = $store;
        }

        return $concatenateData;
    }

    /**
     * @param array $data
     * @param array $timeData
     * @return array
     * @throws Exception
     */
    private function mergeLast30MinutesTime(array $data, array $timeData): array
    {
        $currentTime = Carbon::now()->toDateTime();
        $date = $currentTime->format('Y-m-d');
        $concatenateData = [];
        $hour = $currentTime->format('G');
        $minute = floor(intval($currentTime->format('i')) / 30) === 1 ? 30 : 0;

        $currentTime->setTime($hour, $minute, 0);
        $prevTime = clone $currentTime;
        $prevTime->sub(new DateInterval('PT30M'));

        foreach ($data as $store) {
            $last30minConversionRate = Arr::get($timeData,
                $date.'.'.$store['id'].'.'.$currentTime->format('Y-m-d H:i:s').'.conversion_rate', 0);
            $prev30minConversionRate = Arr::get($timeData,
                $date.'.'.$store['id'].'.'.$prevTime->format('Y-m-d H:i:s').'.conversion_rate', 0);
            $store['conversion_rate']['last_30_minutes'] = $last30minConversionRate - $prev30minConversionRate;
            $concatenateData[] = $store;
        }

        return $concatenateData;
    }

    /**
     * @param array $data
     * @param array $spendData
     * @return array
     */
    private function mergeStoreSpendData(array $data, array $spendData): array
    {
        $mergeData = [];
        foreach ($data as $key => $store) {
            $spend = array_filter($spendData, function ($item) use ($store) {
                return $store['id'] === $item['market_id'];
            });

            $mergedStore = $store;

            $mergedStore['spend']['marketing_channels'] = [];
            foreach ($spend as $itemSpend) {
                $keySpend = array_search($itemSpend['marketing_chanel_id'], array_column($mergedStore['spend']['marketing_channels'], 'marketing_chanel_id'));
                if (!$keySpend && $keySpend !== 0) {
                    $mergedStore['spend']['marketing_channels'][] = [
                        'marketing_chanel_id' => $itemSpend['marketing_chanel_id'],
                        'marketing_chanel_name' => $itemSpend['marketing_chanel_name'],
                        'total' => $itemSpend['spend']
                    ];
                } else {
                    $mergedStore['spend']['marketing_channels'][$keySpend]['total'] += $itemSpend['spend'];
                }
            }

            $mergeData[] = $mergedStore;
        }

        return $mergeData;
    }
}
