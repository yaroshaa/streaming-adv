<?php

namespace Modules\Orders\Http\Controllers;

use App\ClickHouse\ClickHouseException;
use Modules\Orders\Http\Requests\OrderInsertRequest;
use App\Services\OrdersService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Modules\Orders\ClickHouse\Repositories\FifteenMinTotalsRepository;
use Modules\Orders\ClickHouse\Repositories\OrderProductRepository;
use Modules\Orders\ClickHouse\Repositories\OrderRepository;
use Modules\Orders\Http\Requests\OrderAdditionalRequest;
use Modules\Orders\Http\Requests\OrderListRequest;

class OrdersController
{
    /**
     * @OA\Post(
     *     path="/api/orders",
     *     summary="Create order",
     *     description="Create new order",
     *     operationId="addOrder",
     *     tags={"Order"},
     *     security={{"token":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Pass user credentials",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/newOrderData"))
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Wrong credentials response",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid")
     *         )
     *     )
     * )
     */
    public function insert(OrderInsertRequest $request, OrdersService $ordersService): JsonResponse
    {
        try {
            foreach ($request->input('data') as $array) {
                $ordersService->save($array);
            }
        } catch (ClickHouseException $clickHouseException) {
            $message = sprintf('Order insertion failed: "%s"', $clickHouseException->getMessage());
            Log::error($message);

            return response()->json(['error' => $message]);
        } catch (Exception $exception) {
            Log::critical('Order API exception: ' . $exception->getMessage() . $exception->getTraceAsString());
            return response()->json(['error' => $exception->getMessage()], 500);
        }

        return response()->json(['result' => 'ok']);
    }

    /**
     * @OA\Get(
     *     path="/api/orders",
     *     operationId="getOrders",
     *     tags={"Order"},
     *     summary="Get list of orders",
     *     description="Returns list of orders",
     *     security={{"token":{}}},
     *     @OA\Parameter(
     *         name="filter",
     *         in="query",
     *         required=true,
     *         style="deepObject",
     *         @OA\Schema(
     *            type="string",
     *            @OA\Property(
     *                property="currency",
     *                type="string"
     *            ),
     *            @OA\Property(
     *                property="from",
     *                type="string",
     *                format="date-time",
     *            ),
     *            example={"currency":{"id": 1},"from"="2021-03-21T22:00:00.000+00:00"}
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(
     *             @OA\Items(
     *                 type="array",
     *                 @OA\Items(
     *                     ref="#/components/schemas/Order"
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(response=400, description="Bad request"),
     * )
     *
     * Returns list of orders
     * @param OrderListRequest $request
     * @param OrderRepository $repository
     * @return string
     */
    public function list(OrderListRequest $request, OrderRepository $repository): JsonResponse
    {

        $orders = $repository->findBy(
            Carbon::parse($request->input('from')),
            $request->input('currency.id'),
            $request->input('percentile'),
            $request->input('product.remoteId'),
            $request->input('orderStatus.*.remote_id', []),
            $request->input('market.*.remote_id', []),
            (array)$request->input('weight', []),
        );

        $result = [];
        foreach ($orders as $order) {
            if (!isset($result[$order['order_id']])) {
                $result[$order['order_id']] = $order;
                $result[$order['order_id']]['products'] = [];
            }

            $result[$order['order_id']]['products'][] = $order;
        }

        return response()->json(array_values($result));
    }

    public function topSellingProducts(OrderAdditionalRequest $request, OrderProductRepository $repository): JsonResponse
    {
        try {
            return response()->json($repository->getTopSellingProducts(
                $request->input('currency.id'),
                $request->input('percentile'),
                $request->input('product.remoteId'),
                $request->input('orderStatus.*.remote_id', []),
                $request->input('market.*.remote_id', []),
                (array)$request->input('weight', []),
            ));
        } catch (ClickHouseException $clickHouseException) {
            return response()->json(['error' => 'Order API exception: ' . $clickHouseException->getMessage()]);
        }
    }

    public function fifteenMinTotals(OrderAdditionalRequest $request, FifteenMinTotalsRepository $repository): JsonResponse
    {
        try {
            return response()->json([
                'totals' => $repository->getTotals(
                    Carbon::now()->subMinutes(15),
                    $request->input('currency.id'),
                    $request->input('percentile'),
                    $request->input('product.remoteId'),
                    $request->input('orderStatus.*.remote_id', []),
                    $request->input('market.*.remote_id', []),
                    (array)$request->input('weight', []),
                ),
                'history' => $repository->getHistoryTotals(
                    $request->input('currency.id'),
                    $request->input('percentile'),
                    $request->input('product.remoteId'),
                    $request->input('orderStatus.*.remote_id', []),
                    $request->input('market.*.remote_id', []),
                    (array)$request->input('weight', []),
                )
            ]);
        } catch (ClickHouseException $clickHouseException) {
            return response()->json(['error' => 'Order API exception: ' . $clickHouseException->getMessage()]);
        }
    }
}
