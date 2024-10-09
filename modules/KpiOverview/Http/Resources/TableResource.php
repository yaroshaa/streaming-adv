<?php


namespace Modules\KpiOverview\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class TableResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'data' => TableItem::collection($this->resource)
        ];
    }
}
