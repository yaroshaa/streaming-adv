<?php

namespace Modules\MarketingOverview\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class IndicatorResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'index' => $this->resource['index'] ?? 0,
            'main' => [
                'title' => $this->resource['main.title'] ?? '',
                'total' => $this->resource['main.total'] ?? 0,
            ],
            'secondary' => [
                'title' => $this->resource['secondary.title'] ?? '',
                'total' => $this->resource['secondary.total'] ?? 0,
            ],
        ];
    }
}
