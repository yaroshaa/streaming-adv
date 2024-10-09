<?php

namespace App\Http\Controllers;


use App\ClickHouse\Models\AnalyticsEvents;
use App\Entities\AnalyticsSite;
use App\Http\Requests\AnalyticsRequest;
use App\Services\AnalyticsService;
use Doctrine\ORM\EntityManager;
use Modules\Analytic\ClickHouse\Repositories\AnalyticsRepository;
use Modules\Analytic\Events\AnalyticEvent;
use Modules\Analytic\Services\AnalyticService;

/**
 * Class AnalyticsController
 * @package App\Http\Controllers
 */
class AnalyticsController extends Controller
{
    /**
     * @param AnalyticsRequest $request
     * @param EntityManager $entityManager
     * @param AnalyticsService $analyticsService
     * @param AnalyticsRepository $analyticsRepository
     * @param $event
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\ClickHouse\ClickHouseException
     */
    public function track(AnalyticsRequest $request, EntityManager $entityManager, AnalyticsService $analyticsService, AnalyticsRepository $analyticsRepository,  $event)
    {
        $properties = $request->get('data');
        $key = $request->get('key');

        $sessionId = $request->session()->get('session_id');

        if (!$sessionId) {
            $sessionId = AnalyticsEvents::guidv4();
            $request->session()->put('session_id', $sessionId);
        }
        /**
         * @var AnalyticsSite $site
         */
        $site = $entityManager->getRepository(AnalyticsSite::class)->findOneBy(['key' => $key]);

        if (!$site) {
            return response()->json([
                'error' => [
                    'message' => 'key is not valid'
                ]
            ]);
        }

        $analyticsService->track($site, $event, $properties, null, $sessionId);


        $data = AnalyticService::getLast15MinutesConversionRate($entityManager, $analyticsRepository);
        AnalyticEvent::dispatch($data);

        return response()->json([]);
    }
}
