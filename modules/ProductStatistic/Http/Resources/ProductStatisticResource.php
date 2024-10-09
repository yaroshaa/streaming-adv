<?php

namespace Modules\ProductStatistic\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductStatisticResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'product_id' => (int) $this['id'],
            'remote_id'  => (string) $this['remoteId'],
            'name'       => (string) $this['name'],
            'price'      => $this['price'],
            'weight'     => $this['weight'],
            'no_orders'  => $this['no_orders'],
        ];
    }
}
