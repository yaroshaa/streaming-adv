<?php

namespace Modules\Api\Http\Resources;

use App\Entities\MarketingChannel;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class MarketingChannelResource
 * @property MarketingChannel $resource
 * @package Modules\Http\Resources\Api
 */
class MarketingChannelResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->getId(),
            'name' => $this->resource->getName(),
            'icon_link' => $this->resource->getIconLink(),
        ];
    }
}
