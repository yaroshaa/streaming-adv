<?php

namespace Modules\Analytic\Http\Controllers;

use Carbon\Carbon;
use Doctrine\ORM\EntityManager;
use Modules\Analytic\ClickHouse\Repositories\AnalyticsRepository;
use Modules\Analytic\Http\Requests\ConversationRateRequest;
use Modules\Analytic\Http\Requests\EventsRequest;
use Modules\Analytic\Services\AnalyticService;

/**
 * Class ConversationController
 * @package Modules\Analytic\Http\Controllers
 */
class ConversationController
{
    /**
     * @param EventsRequest $request
     * @param AnalyticsRepository $analyticsRepository
     * @param EntityManager $entityManager
     * @return \Illuminate\Http\JsonResponse
     */
    public function byDates(EventsRequest $request, AnalyticsRepository $analyticsRepository, EntityManager $entityManager)
    {
        $sites = AnalyticService::getSites($entityManager);

        $conversationSerialize = [];

        foreach ($sites as $site ) {
            $conversations = $analyticsRepository->getConversations(
                Carbon::parse($request->get('date_from')),
                Carbon::parse($request->get('date_to')),
                $site->getId()
            );

            $conversationSerialize[] = [
                'date' => collect($conversations)->pluck('date')->toArray(),
                'totalConversions' => collect($conversations)->pluck('totalConversions')->toArray(),
                'conversionRate' => collect($conversations)->pluck('conversionRate')->toArray(),
                'name' => $site->getName(),
                'id' => $site->getId()
            ];
        }

        return response()->json($conversationSerialize);
    }

    /**
     * @param AnalyticsRepository $analyticsRepository
     * @param EntityManager $entityManager
     * @return \Illuminate\Http\JsonResponse
     */
    public function byHours(AnalyticsRepository $analyticsRepository, EntityManager $entityManager)
    {
        $sites = AnalyticService::getSites($entityManager);

        $conversationSerialize = [];




        foreach ($sites as $key => $site) {
            $prepareData = [];
            $carbon = new Carbon();
            $carbon->subHours(24);

            $conversations = $analyticsRepository->getConversationsByHours(
                $site->getId()
            );


            for($i = 1; $i <= 25; $i++) {

                if ($i === 25) {
                    $prepareData[] = [
                        "date" => $carbon->format("Y-m-d"),
                        "totalVisitors" => 0,
                        "totalConversions" => 0,
                        "conversionRate" => 0,
                        "hour" => $carbon->format('H:00')
                    ];
                    continue;
                }

                $conversationDate = $carbon->format('Y-m-d');
                $conversationHour = $carbon->format('H');

                $hasConversation = false;

                foreach ($conversations as $conversation) {

                    if ($conversationDate == $conversation['date'] && (int)$conversationHour == $conversation['hour']) {

                        $prepareData[] = [
                            'date' => $conversation['date'],
                            'totalVisitors' => $conversation['totalVisitors'],
                            'totalConversions' => $conversation['totalConversions'],
                            'conversionRate' => round($conversation['conversionRate'], 2),
                            'hour' => $conversation['hour'] . ':00'
                        ];

                        $hasConversation = true;
                    }
                }

                if (!$hasConversation) {
                    $prepareData[] = [
                        "date" => $carbon->format("Y-m-d"),
                        "totalVisitors" => 0,
                        "totalConversions" => 0,
                        "conversionRate" => 0,
                        "hour" => $carbon->format('H:00')
                    ];
                }

                $carbon->addHour(1);
            }


            $conversationSerialize[] = [
                'date' => collect($prepareData)->pluck('date')->toArray(),
                'hour' => collect($prepareData)->pluck('hour')->toArray(),
                'totalConversions' => collect($prepareData)->pluck('totalConversions')->toArray(),
                'conversionRate' => collect($prepareData)->pluck('conversionRate')->toArray(),
                'name' => $site->getName(),
                'id' => $site->getId()
            ];
        }


        return response()->json($conversationSerialize);
    }

    /**
     * @param EntityManager $entityManager
     * @param AnalyticsRepository $analyticsRepository
     * @return \Illuminate\Http\JsonResponse
     */
    public function byMinutes(EntityManager $entityManager, AnalyticsRepository $analyticsRepository)
    {
        $data = AnalyticService::getLast15MinutesConversionRate($entityManager, $analyticsRepository);
        return response()->json($data);
    }
}
