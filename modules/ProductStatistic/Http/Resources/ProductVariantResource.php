<?php

namespace Modules\ProductStatistic\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantResource extends JsonResource
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
            'id'            => (int) $this->getId(),
            'name'          => (string) $this->getName(),
            'weight'        => $this->getWeight(),
            'remote_id'     => (string) $this->getRemoteId(),
            'link'          => $this->getLink(),
            'image_link'    => $this->getImageLink(),
            'price'         => $this->hasPrice() ? $this->getPrice() : 0.00,
            'currency_id'   => $this->hasCurrencyId() ? $this->getCurrencyId() : config('currency.default.id')
        ];
    }
}
