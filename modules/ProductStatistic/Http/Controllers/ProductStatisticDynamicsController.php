<?php

namespace Modules\ProductStatistic\Http\Controllers;

use App\ClickHouse\ClickHouseException;
use Illuminate\Http\JsonResponse;
use Modules\ProductStatistic\ClickHouse\Repositories\ProductStatisticRepository;
use App\Http\Controllers\Controller;
use Modules\ProductStatistic\Http\Requests\ProductDynamicRequest;
use Modules\ProductStatistic\Http\Resources\ProductDynamicResource;

class ProductStatisticDynamicsController extends Controller
{
    /**
     * @param ProductStatisticRepository $repository
     * @param ProductDynamicRequest $request
     * @return JsonResponse
     * @throws ClickHouseException
     */
    public function __invoke(ProductStatisticRepository $repository, ProductDynamicRequest $request) : JsonResponse
    {
        $data['price_dynamic'] = $repository->getPriceDynamic($this->getFiltersFromArray($request));
        $data['profit_dynamic'] = $repository->getProfitDynamic($this->getFiltersFromArray($request));
        $data['quantities_dynamic'] = $repository->getQuantitiesDynamic($this->getFiltersFromArray($request));
        $data['order_dynamic'] = $repository->getOrdersCountDynamic($this->getFiltersFromArray($request));

        return response()->json(['data' => new ProductDynamicResource($data)]);
    }
}
