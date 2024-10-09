<?php

namespace Modules\KpiOverview\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\KpiOverview\ClickHouse\Repositories\KpiOverviewRepository;
use Modules\KpiOverview\Http\Requests\KpiOverviewRequest;
use Modules\KpiOverview\Http\Resources\TableResource;

class KpiOverviewController
{
    /**
     * @param KpiOverviewRequest $request
     * @param KpiOverviewRepository $repository
     * @return JsonResource
     */
    public function getTotals(KpiOverviewRequest $request, KpiOverviewRepository $repository): JsonResource
    {
        return new TableResource($repository->getTotals(
            Carbon::parse($request->input('date.0')),
            Carbon::parse($request->input('date.1')),
            $request->input('currency.id'),
            $request->input('date_granularity')
        ));
    }
}
