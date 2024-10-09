<?php

namespace Modules\Analytic\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Analytic\Services\AnalyticService;

/**
 * Class AnalyticResource
 * @property AnalyticService $resource
 * @package Modules\Analytic\Http\Resources
 */
class AnalyticResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
//            'message' => sprintf('Calculated value: %s', $this->resource->calculate())
            'id' => $request->id,
            'name' => $request->name,
        ];
    }
}
