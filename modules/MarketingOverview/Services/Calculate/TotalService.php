<?php

namespace Modules\MarketingOverview\Services\Calculate;

use App\ClickHouse\DateGranularityInterface;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use Exception;
use Illuminate\Support\Facades\Config;

class TotalService
{
    public array $totalsByCurrentPeriod;
    public array $totalsByPreviousPeriod;
    public array $totalsByPreviousWeekDayHourGranularity;
    public array $totalsByTodayHourGranularity;
    public array $totalsByToday30MinutesGranularity;

    public function __construct(
        array $totalsByCurrentPeriod,
        array $totalsByPreviousPeriod,
        array $totalsByPreviousWeekDayHourGranularity,
        array $totalsByTodayHourGranularity,
        array $totalsByToday30MinutesGranularity
    )
    {
        $this->totalsByCurrentPeriod = $totalsByCurrentPeriod;
        $this->totalsByPreviousPeriod = $totalsByPreviousPeriod;
        $this->totalsByPreviousWeekDayHourGranularity = $totalsByPreviousWeekDayHourGranularity;
        $this->totalsByTodayHourGranularity = $totalsByTodayHourGranularity;
        $this->totalsByToday30MinutesGranularity = $totalsByToday30MinutesGranularity;
    }

    /**
     * @throws Exception
     */
    public function getStatistic(): array
    {
        $prepareData = $this->prepareTotalsData($this->totalsByCurrentPeriod);

        $prepareEstimateData = $this->prepareTotalsData($this->totalsByPreviousPeriod);

        $prepareEstimateWeekDay = $this->prepareTotalsData(
            $this->totalsByPreviousWeekDayHourGranularity,
            DateGranularityInterface::HOUR_GRANULARITY
        );

        $hourlyData = $this->prepareTotalsData(
            $this->totalsByTodayHourGranularity,
            DateGranularityInterface::HOUR_GRANULARITY,
        );

        $every30Minutes = $this->prepareTotalsData(
            $this->totalsByToday30MinutesGranularity,
            DateGranularityInterface::EVERY_30_MINUTES_GRANULARITY,
        );

        $mergeData = [
            'estimateData' => $prepareEstimateData,
            'hourlyData' => $hourlyData,
            'every30Minutes' => $every30Minutes,
            'prepareEstimateWeekDay' => $prepareEstimateWeekDay
        ];

        return $this->mergeTotalData($prepareData, $mergeData);
    }

    /**
     * @param array $data
     * @param string $dateGranularity
     * @return array
     * @throws Exception
     */
    private function prepareTotalsData(array $data, string $dateGranularity = DateGranularityInterface::DAY_GRANULARITY): array
    {
        $prepareResult = [
            'revenue' => [
                'estimate' => 0,
                'value' => 0,
                'per_day' => [],
                'per_hour' => []
            ],
            'cmam' => [
                'estimate' => 0,
                'value' => 0,
                'per_day' => [],
                'per_hour' => []
            ],
            'cm_ratio' => [
                'value' => 0,
                'last_30_minutes' => 0,
                'last_hour' => 0,
                'per_day' => [],
                'per_hour' => [],
                "threshold_value" => [
                    'value' => Config::get('threshold-values.marketing_overview.cm_ratio.value'),
                    'position' => Config::get('threshold-values.marketing_overview.cm_ratio.position'),
                ],
            ],
            'spend_ratio' => [
                'value' => 0,
                'last_30_minutes' => 0,
                'last_hour' => 0,
                'per_day' => [],
                'per_hour' => [],
                "threshold_value" => [
                    'value' => Config::get('threshold-values.marketing_overview.spend_ratio.value'),
                    'position' => Config::get('threshold-values.marketing_overview.spend_ratio.position'),
                ],
            ],
            'profit' => [
                'estimate' => 0,
                'value' => 0,
                'per_hour' => []
            ]
        ];

        if ($dateGranularity === DateGranularityInterface::HOUR_GRANULARITY) {
            for ($i = 0; $i < 24; $i++) {
                if (isset($data[$i])) {

                    if ($data[$i]['date'] === $i) {
                        $prepareResult['revenue']['per_hour'][] = $data[$i]['revenue'];
                        $prepareResult['cmam']['per_hour'][] = $data[$i]['cmam'];
                        $prepareResult['cm_ratio']['per_hour'][] = $data[$i]['contribution_margin_ratio'];
                        $prepareResult['spend_ratio']['per_hour'][] = $data[$i]['spend_ratio'];
                        $prepareResult['profit']['per_hour'][] = $data[$i]['contribution_margin'];
                    }

                    $prepareResult['revenue']['value'] = array_sum($prepareResult['revenue']['per_hour']);
                    $prepareResult['cmam']['value'] = array_sum($prepareResult['cmam']['per_hour']);
                    $prepareResult['cm_ratio']['value'] = array_sum($prepareResult['cm_ratio']['per_hour']);
                    $prepareResult['spend_ratio']['value'] = array_sum($prepareResult['spend_ratio']['per_hour']);
                    $prepareResult['profit']['value'] = array_sum($prepareResult['profit']['per_hour']);
                } else {
                    $prepareResult['revenue']['per_hour'][] = 0;
                    $prepareResult['cmam']['per_hour'][] = 0;
                    $prepareResult['cm_ratio']['per_hour'][] = 0;
                    $prepareResult['spend_ratio']['per_hour'][] = 0;
                    $prepareResult['profit']['per_hour'][] = 0;
                }
            }
        } elseif ($dateGranularity === DateGranularityInterface::EVERY_30_MINUTES_GRANULARITY) {
            $dateStart = Carbon::now()->toDateTime();
            $dateStart->setTime(0, 0, 0);
            $dateEnd = clone $dateStart;
            $dateEnd->add(new DateInterval('P1D'));

            $interval = new DateInterval('PT30M');
            $period = new DatePeriod($dateStart, $interval, $dateEnd);

            foreach ($period as $date) {
                $dateStr = $date->format('Y-m-d H:i:s');

                $key = array_search($dateStr, array_column($data, 'date'));

                $prepareResult['revenue']['per_30minutes'][] = $key ? $data[$key]['revenue'] : 0;
                $prepareResult['revenue']['value'] = array_sum($prepareResult['revenue']['per_30minutes']);

                $prepareResult['cmam']['per_30minutes'][] = $key ? $data[$key]['cmam'] : 0;
                $prepareResult['cmam']['value'] = array_sum($prepareResult['cmam']['per_30minutes']);

                $prepareResult['cm_ratio']['per_30minutes'][] = $key ? $data[$key]['contribution_margin_ratio'] : 0;
                $prepareResult['cm_ratio']['value'] = array_sum($prepareResult['cm_ratio']['per_30minutes']);

                $prepareResult['spend_ratio']['per_30minutes'][] = $key ? $data[$key]['spend_ratio'] : 0;
                $prepareResult['spend_ratio']['value'] = array_sum($prepareResult['spend_ratio']['per_30minutes']);

                $prepareResult['profit']['per_30minutes'][] = $key ? $data[$key]['contribution_margin'] : 0;
                $prepareResult['profit']['value'] = array_sum($prepareResult['profit']['per_30minutes']);
            }
        } else {
            foreach ($data as $item) {

                $prepareResult['revenue']['per_day'][] = $item['revenue'];
                $prepareResult['revenue']['value'] = array_sum($prepareResult['revenue']['per_day']);

                $prepareResult['cmam']['per_day'][] = $item['cmam'];
                $prepareResult['cmam']['value'] = array_sum($prepareResult['cmam']['per_day']);

                $prepareResult['cm_ratio']['per_date'][] = [
                    'value' => $item['contribution_margin_ratio'],
                    'date' => $item['date']
                ];
                $prepareResult['cm_ratio']['per_day'][] = $item['contribution_margin_ratio'];
                $prepareResult['cm_ratio']['value'] = array_sum($prepareResult['cm_ratio']['per_day']);

                $prepareResult['spend_ratio']['per_date'][] = [
                    'value' => $item['spend_ratio'],
                    'date' => $item['date']
                ];
                $prepareResult['spend_ratio']['per_day'][] = $item['spend_ratio'];
                $prepareResult['spend_ratio']['value'] = array_sum($prepareResult['spend_ratio']['per_day']);

                $prepareResult['profit']['per_day'][] = $item['contribution_margin'];
                $prepareResult['profit']['value'] = array_sum($prepareResult['profit']['per_day']);
            }
        }


        return $prepareResult;
    }

    /**
     * @param array $data
     * @param array $mergeData
     * @return array
     */
    private function mergeTotalData(array $data, array $mergeData): array
    {
        if (isset($mergeData['estimateData'])) {
            $data['revenue']['estimate'] = $mergeData['estimateData']['revenue']['value'];
            $data['cmam']['estimate'] = $mergeData['estimateData']['cmam']['value'];
            $data['profit']['estimate'] = $mergeData['estimateData']['profit']['value'];
        }

        if (isset($mergeData['hourlyData'])) {
            $data['revenue']['per_hour'] = $mergeData['hourlyData']['revenue']['per_hour'];
            $data['revenue']['per_hour_estimate_prev_week_day'] = $mergeData['prepareEstimateWeekDay']['revenue']['per_hour'];
            $data['cmam']['per_hour'] = $mergeData['hourlyData']['cmam']['per_hour'];
            $data['cm_ratio']['per_hour'] = $mergeData['hourlyData']['cm_ratio']['per_hour'];
            $data['spend_ratio']['per_hour'] = $mergeData['hourlyData']['spend_ratio']['per_hour'];
            $data['profit']['per_hour'] = $mergeData['hourlyData']['profit']['per_hour'];
        }

        if (isset($mergeData['every30Minutes'])) {
            $data['revenue']['per_30minutes'] = $mergeData['every30Minutes']['revenue']['per_30minutes'];
            $data['cmam']['per_30minutes'] = $mergeData['every30Minutes']['cmam']['per_30minutes'];
            $data['cm_ratio']['per_30minutes'] = $mergeData['every30Minutes']['cm_ratio']['per_30minutes'];
            $data['spend_ratio']['per_30minutes'] = $mergeData['every30Minutes']['spend_ratio']['per_30minutes'];
            $data['profit']['per_30minutes'] = $mergeData['every30Minutes']['profit']['per_30minutes'];
        }

        return $data;
    }
}
