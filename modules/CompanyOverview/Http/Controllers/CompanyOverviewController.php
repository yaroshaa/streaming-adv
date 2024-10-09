<?php

namespace Modules\CompanyOverview\Http\Controllers;

use App\ClickHouse\ClickHouseException;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Modules\CompanyOverview\ClickHouse\Repositories\CompanyOverviewRepository;
use Modules\CompanyOverview\Http\Requests\CompanyOverviewRequest;
use Modules\CompanyOverview\Services\CompanyOverviewService;

class CompanyOverviewController
{
    private CompanyOverviewRepository $repository;

    public function __construct(CompanyOverviewRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param CompanyOverviewRequest $request
     * @return JsonResponse
     */
    public function getPieChartData(CompanyOverviewRequest $request): JsonResponse
    {
        return response()->json($this->repository->getPieChartData(...$this->getArgumentsByRequest($request)));
    }

    /**
     * @throws ClickHouseException
     */
    public function getStreamGraphData(CompanyOverviewRequest $request): JsonResponse
    {
        return response()->json($this->repository->getStreamGraphData(...$this->getArgumentsByRequest($request)));
    }

    public function getTotals(
        CompanyOverviewRequest $request,
        CompanyOverviewService $companyOverviewService
    ): JsonResponse
    {
        return response()->json($companyOverviewService->modifyTotals($this->repository->getTotals(
            ...$this->getArgumentsByRequest($request)
        )));
    }

    public function getTotalsByMarket(
        CompanyOverviewRequest $request,
        CompanyOverviewService $companyOverviewService
    ): JsonResponse
    {
        return response()->json($companyOverviewService->modifyTotalsByMarket($this->repository->getTotalsByMarket(
            ...$this->getArgumentsByRequest($request)
        ), [
            'avg_total',
            'avg_profit',
            'total_packed',
            'time_packed',
            'total_returned',
            'returned_percent',
            'customers',
            'new_customers',
            'new_customers_percent',
            'orders',
            'products_count',
            'product_discount',
            'product_discount_percent'
        ]));
    }

    /**
     * @param CompanyOverviewRequest $request
     * @return JsonResponse
     */
    public function getOrders(CompanyOverviewRequest $request): JsonResponse
    {
        $orders = $this->repository->getOrdersBy(...$this->getArgumentsByRequest($request));

        $result = [];
        foreach ($orders as $order) {
            $result[$order['order_id']] = $order;
            if (!array_key_exists('products', $result[$order['order_id']])) {
                $result[$order['order_id']]['products'] = [];
            }

            $result[$order['order_id']]['products'][] = $order;
        }

        return response()->json(array_values($result));
    }

    /**
     * @param CompanyOverviewRequest $request
     * @return array
     */
    private function getArgumentsByRequest(CompanyOverviewRequest $request): array
    {
        return [
            Carbon::parse($request->input('date.0')),
            Carbon::parse($request->input('date.1')),
            $request->input('currency.id')
        ];
    }
}
