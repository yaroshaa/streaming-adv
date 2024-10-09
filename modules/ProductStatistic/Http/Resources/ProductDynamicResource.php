<?php

namespace Modules\ProductStatistic\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductDynamicResource extends JsonResource
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
            'price_dynamic' => ItemDynamicResource::collection($this['price_dynamic']),
            'profit_dynamic' => ItemDynamicResource::collection($this['profit_dynamic']),
            'quantities_dynamic' => ItemDynamicResource::collection($this['quantities_dynamic']),
            'order_dynamic' => ItemDynamicResource::collection($this['order_dynamic']),
        ];
    }
}
