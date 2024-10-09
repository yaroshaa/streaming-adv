<?php

namespace Modules\Api\Http\Controllers;

use App\Entities\MarketingChannel;
use App\Repositories\MarketingChannelRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Api\Http\Requests\MarketingChannelStoreRequest;
use Modules\Api\Http\Requests\MarketingChannelUpdateRequest;
use Modules\Api\Http\Resources\MarketingChannelResource;
use Symfony\Component\HttpFoundation\Response;

class MarketingChannelController
{
    private EntityManager $entityManager;
    private MarketingChannelRepository $repository;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(MarketingChannel::class);
    }

    public function index(): AnonymousResourceCollection
    {
        return MarketingChannelResource::collection($this->repository->findAll());
    }

    public function show(MarketingChannel $channel): MarketingChannelResource
    {
        return new MarketingChannelResource($channel);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function store(MarketingChannelStoreRequest $request): JsonResponse
    {
        $channel = new MarketingChannel();
        $channel->setName($request->input('name'));
        $channel->setIconLink($request->input('icon_link'));

        $this->entityManager->persist($channel);
        $this->entityManager->flush();

        return (new MarketingChannelResource($channel))->response()->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function update(MarketingChannelUpdateRequest $request, MarketingChannel $channel): MarketingChannelResource
    {
        $channel->setName($request->input('name'));
        $channel->setIconLink($request->input('icon_link'));
        $this->entityManager->persist($channel);
        $this->entityManager->flush();

        return new MarketingChannelResource($channel);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function destroy(MarketingChannel $channel): JsonResponse
    {
        $this->entityManager->remove($channel);
        $this->entityManager->flush();

        return response()->json(['deleted']);
    }
}
