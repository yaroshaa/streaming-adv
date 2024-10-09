<?php

namespace Modules\Analytic\Http\Controllers;

use Carbon\Carbon;
use Modules\Analytic\ClickHouse\Repositories\AnalyticsRepository;
use Modules\Analytic\Http\Requests\EventsRequest;

/**
 * Class EventController
 * @package Modules\Analytic\Http\Controllers
 */
class EventController
{
    /**
     * @param EventsRequest $request
     * @param AnalyticsRepository $analyticsRepository
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(EventsRequest $request, AnalyticsRepository $analyticsRepository)
    {
        $events = $analyticsRepository->getEvents(
            Carbon::parse($request->get('date_from')),
            Carbon::parse($request->get('date_to')),
            $request->get('event')
        );

        return response()->json($events);
    }
}
