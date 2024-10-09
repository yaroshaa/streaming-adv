<?php

namespace Modules\Api\Http\Controllers;

use App\Entities\Market;
use App\Repositories\MarketRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Api\Http\Requests\MarketStoreRequest;
use Modules\Api\Http\Requests\MarketUpdateRequest;
use Modules\Api\Http\Resources\MarketResource;
use Symfony\Component\HttpFoundation\Response;

class MarketController
{
    private EntityManager $entityManager;
    private MarketRepository $repository;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Market::class);
    }

    public function index(): AnonymousResourceCollection
    {
        return MarketResource::collection($this->repository->findAll());
    }

    public function show(Market $market): MarketResource
    {
        return new MarketResource($market);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function store(MarketStoreRequest $request): JsonResponse
    {
        $market = new Market();
        $market->setRemoteId($request->input('remote_id'));
        $market->setName($request->input('name'));
        $market->setIconLink($request->input('icon_link'));
        $market->setColor($request->input('color'));

        $this->entityManager->persist($market);
        $this->entityManager->flush();

        return (new MarketResource($market))->response()->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function update(MarketUpdateRequest $request, Market $market): MarketResource
    {
        $market->setRemoteId($request->input('remote_id'));
        $market->setName($request->input('name'));
        $market->setIconLink($request->input('icon_link'));
        $market->setColor($request->input('color'));

        $this->entityManager->persist($market);
        $this->entityManager->flush();

        return new MarketResource($market);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function destroy(Market $market): JsonResponse
    {
        $this->entityManager->remove($market);
        $this->entityManager->flush();

        return response()->json(['deleted']);
    }
}
