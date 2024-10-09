<?php

namespace Modules\Feedbacks\Http\Controllers;

use App\Entities\Tag;
use App\Repositories\TagRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Feedbacks\Http\Requests\TagStoreRequest;
use Modules\Feedbacks\Http\Requests\TagUpdateRequest;
use Modules\Feedbacks\Http\Resources\TagResource;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Response;

class TagController
{
    private TagRepository $repository;
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Tag::class);
    }
    /**
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return TagResource::collection($this->repository->findAll());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param TagStoreRequest $request
     * @return JsonResponse
     * @throws ORMException
     */
    public function store(TagStoreRequest $request): JsonResponse
    {
        $tag = new Tag();
        $tag->setName($request->input('name'));
        $tag->setColor($request->input('color'));
        $tag->setKeywords($request->input('keywords'));

        $this->entityManager->persist($tag);
        $this->entityManager->flush();
        return response()->json(new TagResource($tag), Response::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param TagUpdateRequest $request
     * @param Tag $tag
     * @return TagResource
     * @throws ORMException
     */
    public function update(TagUpdateRequest $request, Tag $tag): TagResource
    {
        $tag->setName($request->input('name'));
        $tag->setColor($request->input('color'));
        $tag->setKeywords($request->input('keywords'));

        $this->entityManager->persist($tag);
        $this->entityManager->flush();
        return new TagResource($tag);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Tag $tag
     * @return TagResource
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function destroy(Tag $tag): TagResource
    {
        $deletedResource = new TagResource(clone $tag);
        $this->entityManager->remove($tag);
        $this->entityManager->flush();
        return $deletedResource;
    }
}
