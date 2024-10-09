<?php

namespace Modules\Api\Http\Controllers;

use App\Entities\Source;
use App\Repositories\SourceRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Api\Http\Requests\SourceStoreRequest;
use Modules\Api\Http\Requests\SourceUpdateRequest;
use Modules\Api\Http\Resources\SourceResource;
use Symfony\Component\HttpFoundation\Response;

class SourceController
{
    private EntityManager $entityManager;
    private SourceRepository $repository;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Source::class);
    }

    public function index(): AnonymousResourceCollection
    {
        return SourceResource::collection($this->repository->findAll());
    }

    public function show(Source $source): SourceResource
    {
        return new SourceResource($source);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function store(SourceStoreRequest $request): JsonResponse
    {
        $source = new Source();
        $source->setRemoteId($request->input('remote_id'));
        $source->setName($request->input('name'));
        $source->setIconLink($request->input('icon_link'));

        $this->entityManager->persist($source);
        $this->entityManager->flush();

        return (new SourceResource($source))->response()->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function update(SourceUpdateRequest $request, Source $source): SourceResource
    {
        $source->setRemoteId($request->input('remote_id'));
        $source->setName($request->input('name'));
        $source->setIconLink($request->input('icon_link'));

        $this->entityManager->persist($source);
        $this->entityManager->flush();

        return new SourceResource($source);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function destroy(Source $source): JsonResponse
    {
        $this->entityManager->remove($source);
        $this->entityManager->flush();

        return response()->json(['deleted']);
    }
}
