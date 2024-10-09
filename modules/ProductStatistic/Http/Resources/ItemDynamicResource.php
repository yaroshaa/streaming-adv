<?php

namespace Modules\ProductStatistic\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemDynamicResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'value' => round((float) $this['value'],2),
            'date'  => (string) $this['date'],
        ];
    }
}
