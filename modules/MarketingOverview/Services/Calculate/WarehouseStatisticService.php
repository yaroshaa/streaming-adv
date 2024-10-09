<?php

namespace Modules\MarketingOverview\Services\Calculate;

class WarehouseStatisticService
{
    private array $data;

    /**
     * WarehouseStatisticService constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @param array $data
     * @return array
     */
    private function calcByMarkets(array $data)
    {
        $result = [];
        $marketIds = array_values(array_unique(array_column($data, 'market_id')));


        foreach ($marketIds as $marketId) {

            $marketData = array_values(array_filter($data, function ($item) use ($marketId) {
                return $item['market_id'] == $marketId;
            }));

            $inPacking = array_sum(array_column($marketData, 'in_packing'));
            $open = array_sum(array_column($marketData, 'open'));
            $awaitingStock = array_sum(array_column($marketData, 'awaiting_stock'));
            $total = array_sum([$inPacking, $open, $awaitingStock]);

            if (isset($marketData[0])) {
                $result[] = [
                    'id' => $marketId,
                    'market_name' => $marketData[0]['market_name'],
                    'in_packing' => $inPacking,
                    'open' => $open,
                    'awaiting_stock' => $awaitingStock,
                    'total' => $total
                ];
            }
        }

        return $result;
    }

    public function getStatistic(): array
    {
        $response = [];

        $warehouseIds = array_values(array_unique(array_column($this->data['statistics'], 'warehouse_id')));

        foreach ($warehouseIds as $warehouseId) {
            $warehouseData = array_values(array_filter($this->data['statistics'], function ($item) use ($warehouseId) {
                return $item['warehouse_id'] == $warehouseId;
            }));

            $inPacking = array_sum(array_column($warehouseData, 'in_packing'));
            $open = array_sum(array_column($warehouseData, 'open'));
            $awaitingStock = array_sum(array_column($warehouseData, 'awaiting_stock'));
            $total = array_sum([$inPacking, $open, $awaitingStock]);


            if (isset($warehouseData[0])) {
                $warehouseStat = [
                    'id' => $warehouseId,
                    'name' => $warehouseData[0]['warehouse_name'],
                    'in_packing' => $inPacking,
                    'open' => $open,
                    'awaiting_stock' => $awaitingStock,
                    'total' => $total,
                    'by_station' => [],
                    'by_market' => [],
                    'per_hour' => []
                ];

                $warehouseStat['by_market'] = $this->calcByMarkets($warehouseData);

                $stations = array_unique(array_column($warehouseData, 'station'));

                for ($i = 0; $i < 24; $i++) {
                    $warehouseStat['per_hour'][] = 0;
                }

                foreach ($this->data['hourlyStatistics'] as $item) {
                    if ($item['warehouse_id'] == $warehouseId) {
                        $warehouseStat['per_hour'][$item['hour']] = $item['open'];
                    }
                }

                foreach ($stations as $station) {
                    $stationData = array_values(array_filter($warehouseData, function ($item) use ($station) {
                        return $item['station'] === $station;
                    }));

                    $inPacking = array_sum(array_column($stationData, 'in_packing'));
                    $open = array_sum(array_column($stationData, 'open'));
                    $awaitingStock = array_sum(array_column($stationData, 'awaiting_stock'));
                    $total = array_sum([$inPacking, $open, $awaitingStock]);

                    $stationStat = [
                        'station' => $station,
                        'in_packing' => $inPacking,
                        'open' => $open,
                        'awaiting_stock' => $awaitingStock,
                        'total' => $total,
                        'per_hour' => [],
                        'by_market' => []
                    ];

                    for ($i = 0; $i < 24; $i++) {
                        $stationStat['per_hour'][] = 0;
                    }

                    foreach ($this->data['hourlyStatisticsByStations'] as $item) {
                        if ($item['station'] == $station && $item['warehouse_id'] == $warehouseId) {
                            $stationStat['per_hour'][$item['hour']] = $item['open'];
                        }
                    }

                    $stationStat['by_market'] = $this->calcByMarkets($stationData);
                    $warehouseStat['by_station'][] = $stationStat;
                }

                $response[] = $warehouseStat;
            }


        }

        return $response;
    }
}
