<?php

namespace Modules\Analytic\Services;

use App\ClickHouse\Models\AnalyticsEvents;
use App\Entities\AnalyticsSite;
use App\Services\AnalyticsService;
use Carbon\Carbon;
use Doctrine\ORM\EntityManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Modules\Analytic\ClickHouse\Repositories\AnalyticsRepository;

class AnalyticService
{
    private array $filter;

    public function setFilter(array $filter)
    {
        $this->filter = $filter;
    }

    public function calculate(): int
    {
        return $this->filter['number'] * config('analytic.number');
    }

    public function getEvents()
    {

    }

    /**
     * @param EntityManager $entityManager
     * @return array
     */
    public static function getSites(EntityManager $entityManager): array
    {
        $repo = $entityManager->getRepository(AnalyticsSite::class);

        return $repo->findAll();
    }

    /**
     * @param EntityManager $entityManager
     * @param AnalyticsRepository $analyticsRepository
     * @return array
     */
    public static function getLast15MinutesConversionRate(EntityManager $entityManager, AnalyticsRepository $analyticsRepository)
    {
        $sites = AnalyticService::getSites($entityManager);
        $conversationSerialize = [];

        foreach ($sites as $site) {
            $conversations = $analyticsRepository->getConversationsByMinutes(
                $site->getId(),
                15
            );

            $conversionRate = collect($conversations)->pluck('conversionRate')->get(0) ?? 0;

            $conversationSerialize[] = [
                'last15MinutesConversionRate' => round($conversionRate, 2),
                'name' => $site->getName(),
                'id' => $site->getId()
            ];
        }

        return $conversationSerialize;
    }
}
