<?php

namespace Modules\Api\Http\Controllers;

use App\ClickHouse\Repositories\BaseActiveUserRepository;
use App\ClickHouse\Repositories\BaseCartActionRepository;
use App\ClickHouse\Repositories\BaseWarehouseStatisticRepository;
use App\Entities\Market;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\JsonResponse;
use Modules\Api\Http\Requests\ActiveUserStoreRequest;
use Modules\Api\Http\Requests\CartActionStoreRequest;
use Modules\Api\Http\Requests\WarehouseStatisticsStoreRequest;
use Modules\Api\Http\Resources\SuccessResource;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class StreamAnalyticsController
 * @package Modules\Api\Http\Controllers
 */
class StreamAnalyticsController
{
    /**
     * @var EntityManager
     */
    private EntityManager $entityManager;

    /**
     * StreamAnalyticsController constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Store cart action.
     *
     * @param CartActionStoreRequest $request
     * @param BaseCartActionRepository $repository
     * @return JsonResponse
     * @throws \App\ClickHouse\ClickHouseException
     */
    public function cartAction(CartActionStoreRequest $request, BaseCartActionRepository $repository): JsonResponse
    {
        /**
         * @var $market Market
         */
        $remoteId = $request->input('remote_id');
        $market = $this->entityManager->getRepository(Market::class)->findOneByRemoteId($remoteId);

        $repository->insert([
            'market_id' => $market->getId(),
            'status' => $request->input('status'),
            'ip' => $request->ip(),
            'created_at' => new \DateTime()
        ]);

        return response()->json(new SuccessResource('Success cart action'), Response::HTTP_ACCEPTED);
    }

    /**
     * Store active user.
     *
     * @param ActiveUserStoreRequest $request
     * @param BaseActiveUserRepository $repository
     * @return JsonResponse
     * @throws \App\ClickHouse\ClickHouseException
     */
    public function activeUser(ActiveUserStoreRequest $request, BaseActiveUserRepository $repository)
    {
        /**
         * @var $market Market
         */
        $remoteId = $request->input('remote_id');
        $market = $this->entityManager->getRepository(Market::class)->findOneByRemoteId($remoteId);

        $repository->insert([
            'market_id' => $market->getId(),
            'status' => $request->input('status'),
            'ip' => $request->ip(),
            'created_at' => new \DateTime()
        ]);

        return response()->json(new SuccessResource('Success active user store'), Response::HTTP_CREATED);
    }

    /**
     * Store warehouse statistic.
     *
     * @param WarehouseStatisticsStoreRequest $request
     * @param BaseWarehouseStatisticRepository $repository
     * @return JsonResponse
     * @throws \App\ClickHouse\ClickHouseException
     */
    public function warehouseStatistic(WarehouseStatisticsStoreRequest $request, BaseWarehouseStatisticRepository $repository)
    {
        $repository->insert([
            'warehouse_id' => $request->input('warehouse_id'),
            'in_packing' => $request->input('in_packing'),
            'open' => $request->input('open'),
            'awaiting_stock' => $request->input('awaiting_stock'),
            'station' => $request->input('station'),
            'market_id' => $request->input('market_id'),
            'created_at' => new \DateTime()
        ]);

        return response()->json(new SuccessResource('Success warehouse statistics store'), Response::HTTP_CREATED);
    }
}
