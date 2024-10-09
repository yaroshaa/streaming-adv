<?php

namespace Modules\MarketingOverview\Services\Calculate;

class ExpenseSpendService
{
    const SPEND_15_MINUTES_GRANULARITY_INDEX = 4;

    private array $spendOfDayGranularity;
    private array $spendOfHourGranularity;
    private array $spendOfEvery30MinutesGranularity;
    private array $spendOfEvery15MinutesGranularity;

    public function __construct(
        array $spendOfDayGranularity,
        array $spendOfHourGranularity,
        array $spendOfEvery30MinutesGranularity,
        array $spendOfEvery15MinutesGranularity
    )
    {
        $this->spendOfDayGranularity = $spendOfDayGranularity;
        $this->spendOfHourGranularity = $spendOfHourGranularity;
        $this->spendOfEvery30MinutesGranularity = $spendOfEvery30MinutesGranularity;
        $this->spendOfEvery15MinutesGranularity = $spendOfEvery15MinutesGranularity;
    }

    public function getStatistic(): array
    {
        $spend = [
            'marketing_channels' => [],
            'total' => 0
        ];

        $total = 0;

        $todayData = $this->spendOfDayGranularity;
        $hourlyData = $this->spendOfHourGranularity;
        $every30MinutesData = $this->spendOfEvery30MinutesGranularity;
        $every15MinutesData = $this->spendOfEvery15MinutesGranularity;

        foreach ($todayData as $data) {
            $id = $data['marketing_chanel_id'];

            $todayKey = array_search($id, array_column($spend['marketing_channels'], 'marketing_chanel_id'));
            $hourlyKey = array_search($id, array_column($hourlyData, 'marketing_chanel_id'));
            $every30MinutesKey = array_search($id, array_column($every30MinutesData, 'marketing_chanel_id'));
            $every15MinutesKey = array_search($id, array_column($every15MinutesData, 'marketing_chanel_id'));

            if ($todayKey === false) {
                $indicators = [
                    'id' => $id,
                    'name' => $data['marketing_chanel_name'],
                    'icon_link' => $data['marketing_chanel_icon_link'],
                    'last_hour' => 0,
                    'last_30_minutes' => 0,
                    'last_15_minutes' => 0,
                    'last_15_minutes_index' => 0,
                    'today' => $data['spend']
                ];

                if ($hourlyKey !== false) {
                    $indicators['last_hour'] = $hourlyData[$hourlyKey]['spend'];
                }

                if ($every30MinutesKey !== false) {
                    $indicators['last_30_minutes'] = $every30MinutesData[$every30MinutesKey]['spend'];
                }

                if ($every15MinutesKey !== false) {
                    $indicators['last_15_minutes'] = $every15MinutesData[$every15MinutesKey]['spend'];
                    $indicators['last_15_minutes_index'] = $every15MinutesData[$every15MinutesKey]['spend'] * self::SPEND_15_MINUTES_GRANULARITY_INDEX;
                }

                $spend['marketing_channels'][] = $indicators;
            }

            $total += $data['spend'];
        }

        $spend['total'] = $total;

        return $spend;
    }
}
