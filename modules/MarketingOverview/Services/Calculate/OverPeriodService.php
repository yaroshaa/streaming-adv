<?php

namespace Modules\MarketingOverview\Services\Calculate;

class OverPeriodService
{
    private float $revenue;
    private float $estimateRevenue;
    private float $marketingExpense;
    private float $estimateMarketingExpense;

    public function __construct(
        float $revenue,
        float $estimateRevenue,
        float $marketingExpense,
        float $estimateMarketingExpense
    )
    {
        $this->revenue = $revenue;
        $this->estimateRevenue = $estimateRevenue;
        $this->marketingExpense = $marketingExpense;
        $this->estimateMarketingExpense = $estimateMarketingExpense;
    }

    public function getStatistic(): array
    {
        $percentageChange = $this->revenue != 0
            ? round((($this->revenue - $this->estimateRevenue) / $this->revenue) * 100, 2)
            : 0;

        $cmam = $this->revenue - $this->marketingExpense;
        $estimateCMAM = ($this->estimateRevenue - $this->estimateMarketingExpense);

        if ($estimateCMAM > 0) {
            $percentageChangeCMAM = $cmam != 0
                ? round((($cmam - $estimateCMAM) / $cmam) * 100, 2)
                : 0;
        } else {
            $percentageChangeCMAM = 100;
        }

        return [
            "revenue" => [
                "value" => $this->revenue,
                "estimate" => $this->estimateRevenue,
                "percentage_change" => $percentageChange
            ],
            "cmam" => [
                "value" => $cmam,
                "estimate" => $estimateCMAM > 0 ? $estimateCMAM : -1,
                "percentage_change" => $percentageChangeCMAM
            ]
        ];
    }
}
