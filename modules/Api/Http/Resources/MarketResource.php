<?php

namespace Modules\Api\Http\Resources;

use App\Entities\Market;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class MarketResource
 * @property Market $resource
 * @package Modules\Http\Resources\Api
 */
class MarketResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->getId(),
            'name' => $this->resource->getName(),
            'remote_id' => $this->resource->getRemoteId(),
            'icon_link' => $this->resource->getIconLink(),
            'color' => $this->resource->getColor()
        ];
    }
}
