<?php

namespace Modules\Analytic\Http\Controllers;

use Modules\Analytic\Services\AnalyticService;

class AnalyticController
{
    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function __invoke(): array
    {
        $dataAnalytics = AnalyticService::getSites();

        $topEvents = [];
        foreach ($dataAnalytics as  $key => $item){
            $topEvents[] = [
               'date' => collect($item)->pluck('date')->toArray(),
               'totalVisitors' => collect($item)->pluck('totalVisitors')->toArray(),
               'name' => $item['name']
            ];
        }
        return $topEvents;
    }
}
