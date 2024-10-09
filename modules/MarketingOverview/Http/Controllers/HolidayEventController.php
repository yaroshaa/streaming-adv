<?php

namespace Modules\MarketingOverview\Http\Controllers;

use App\Entities\HolidayEvent;
use App\Repositories\HolidayEventRepository;
use Carbon\Carbon;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\MarketingOverview\Http\Requests\HolidayEventStoreRequest;
use Modules\MarketingOverview\Http\Requests\HolidayEventUpdateRequest;
use Modules\MarketingOverview\Http\Resources\HolidayEventResource;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Response;

class HolidayEventController
{
    private HolidayEventRepository $repository;
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(HolidayEvent::class);
    }
    /**
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return HolidayEventResource::collection($this->repository->findAll());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param HolidayEventStoreRequest $request
     * @return JsonResponse
     * @throws ORMException
     */
    public function store(HolidayEventStoreRequest $request): JsonResponse
    {
        $holidayEvent = new HolidayEvent();
        $holidayEvent->setTitle($request->input('title'));
        $holidayEvent->setDate(Carbon::parse($request->input('date'))->toDateTime());
        $this->entityManager->persist($holidayEvent);
        $this->entityManager->flush();

        return (new HolidayEventResource($holidayEvent))->response()->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param HolidayEventUpdateRequest $request
     * @param HolidayEvent $holiday_event
     * @return HolidayEventResource
     * @throws ORMException
     */
    public function update(HolidayEventUpdateRequest $request, HolidayEvent $holiday_event): HolidayEventResource
    {
        $holiday_event->setTitle($request->input('title'));
        $holiday_event->setDate(Carbon::parse($request->input('date'))->toDateTime());
        $this->entityManager->persist($holiday_event);
        $this->entityManager->flush();

        return new HolidayEventResource($holiday_event);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param HolidayEvent $holiday_event
     * @return HolidayEventResource
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function destroy(HolidayEvent $holiday_event): HolidayEventResource
    {
        $deletedResource = new HolidayEventResource(clone $holiday_event);
        $this->entityManager->remove($holiday_event);
        $this->entityManager->flush();

        return $deletedResource;
    }
}
