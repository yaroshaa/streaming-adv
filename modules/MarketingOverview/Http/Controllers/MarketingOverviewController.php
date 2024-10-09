<?php

namespace Modules\MarketingOverview\Http\Controllers;

use Carbon\Carbon;
use Modules\MarketingOverview\Http\Requests\MarketingOverviewRequest;
use Modules\MarketingOverview\Http\Resources\MarketingOverviewResource;
use Modules\MarketingOverview\Services\MarketViewService;

/**
 * Class MarketingOverviewController
 * @package App\Http\Controllers
 */
class MarketingOverviewController
{
    /**
     * @param MarketingOverviewRequest $request
     * @param MarketViewService $service
     * @return MarketingOverviewResource
     */
    public function getData(
        MarketingOverviewRequest $request,
        MarketViewService $service
    ): MarketingOverviewResource
    {
        $service->setFilter(
            Carbon::parse($request->input('date.0')),
            Carbon::parse($request->input('date.1')),
            $request->input('currency.id'),
            $request->input('date_granularity')
        );
        return (new MarketingOverviewResource($service));
    }
}
