<?php

namespace Modules\Api\Http\Resources;

use App\Entities\Source;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class SourceResource
 * @property Source $resource
 * @package Modules\Http\Resources\Api
 */
class SourceResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->getId(),
            'name' => $this->resource->getName(),
            'remote_id' => $this->resource->getRemoteId(),
            'icon_link' => $this->resource->getIconLink()
        ];
    }
}
