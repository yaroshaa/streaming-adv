<?php

namespace Modules\StoreToday\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Modules\StoreToday\ClickHouse\Repositories\StoreTodayRepository;
use Modules\StoreToday\Http\Requests\StoreTodayRequest;
use Modules\StoreToday\Services\ProfitService;
use NumberFormatter;

class StoreTodayController
{
    /**
     * Get data.
     *
     * @param StoreTodayRequest $request
     * @param StoreTodayRepository $repository
     * @return JsonResponse
     * @throws Exception
     */
    public function getData(StoreTodayRequest $request, StoreTodayRepository $repository): JsonResponse
    {
        $from = Carbon::parse($request->input('date.0'));
        $to = Carbon::parse($request->input('date.1'));
        $marketIds = $request->input('market.*.remote_id', []);
        $currencyId = $request->input('currency.id');
        $granularity = $request->input('date_granularity');

        $kpiDataItems = $repository->getKpi(
            $from,
            $to,
            $marketIds,
            $currencyId,
            $granularity
        );

        $indicatorData = $repository->getIndicatorData(
            $from,
            $to,
            $marketIds,
            $currencyId
        );

        $kpiData = !empty($kpiDataItems) ? $kpiDataItems[0] : [
            'date' => 0,
            'avg_total' => 0,
            'avg_profit' => 0,
            'total_profit' => 0,
            'total_packed' => 0,
            'total_returned' => 0,
            'returned_percent' => 0,
            'customers' => 0,
            'new_customers' => 0,
            'new_customers_percent' => 0,
            'orders' => 0,
            'products_count' => 0,
            'product_discount' => 0,
            'product_discount_percent' => 0,
        ];

        $profitService = new ProfitService($repository, $from, $to, $currencyId);

        $profit = $profitService->getProfit();
        $profitForecast = $profitService->getProfitForecast();

        $profitFor30Days = $profitService->get30DaysProfit();
        $profitForecastFor30Days = $profitService->get30DaysProfitForecast();

        $formatter = new NumberFormatter("en-US@currency=" . $request->input('currency.code'), NumberFormatter::CURRENCY);

        /** @todo remade to resources maybe */
        return response()->json([
            'kpiData' => $kpiData,
            'srcData' => $indicatorData,
            'profit' => [
                'singleDay' => [
                    'value' => $profit ? $profit['total_profit'] : 0,
                    'forecast' => $profitForecast ? $profitForecast['total_profit'] : 0
                ],
                'lastDays' => [
                    'count_days' => 30,
                    'value' => $profitFor30Days ? $profitFor30Days['total_profit'] : 0,
                    'forecast' => $profitForecastFor30Days ? $profitForecastFor30Days['total_profit'] : 0
                ]
            ],
            'revenue' => [
                'singleDay' => [
                    'value' => $profit ? $profit['revenue'] : 0,
                    'forecast' => $profitForecast ? $profitForecast['revenue'] : 0
                ],
                'lastDays' => [
                    'count_days' => 30,
                    'value' => $profitFor30Days ? $profitFor30Days['revenue'] : 0,
                    'forecast' => $profitForecastFor30Days ? $profitForecastFor30Days['revenue'] : 0
                ]
            ],
            'currency_symbol' => $formatter->getSymbol(NumberFormatter::CURRENCY_SYMBOL)
        ]);
    }
}
