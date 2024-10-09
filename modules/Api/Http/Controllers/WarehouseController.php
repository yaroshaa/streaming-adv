<?php

namespace Modules\Api\Http\Controllers;

use App\Entities\Warehouse;
use App\Repositories\WarehouseRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Api\Http\Requests\WarehouseStoreRequest;
use Modules\Api\Http\Requests\WarehouseUpdateRequest;
use Modules\Api\Http\Resources\WarehouseResource;
use Symfony\Component\HttpFoundation\Response;

class WarehouseController
{
    private EntityManager $entityManager;
    private WarehouseRepository $repository;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Warehouse::class);
    }

    public function index(): AnonymousResourceCollection
    {
        return WarehouseResource::collection($this->repository->findAll());
    }

    public function show(Warehouse $warehouse): WarehouseResource
    {
        return new WarehouseResource($warehouse);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function store(WarehouseStoreRequest $request): JsonResponse
    {
        $warehouse = new Warehouse();
        $warehouse->setName($request->input('name'));

        $this->entityManager->persist($warehouse);
        $this->entityManager->flush();

        return (new WarehouseResource($warehouse))->response()->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function update(WarehouseUpdateRequest $request, Warehouse $warehouse): WarehouseResource
    {
        $warehouse->setName($request->input('name'));
        $this->entityManager->persist($warehouse);
        $this->entityManager->flush();

        return new WarehouseResource($warehouse);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function destroy(Warehouse $warehouse): JsonResponse
    {
        $this->entityManager->remove($warehouse);
        $this->entityManager->flush();

        return response()->json(['deleted']);
    }
}
