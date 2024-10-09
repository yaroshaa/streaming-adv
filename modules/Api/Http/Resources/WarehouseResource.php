<?php

namespace Modules\Api\Http\Resources;

use App\Entities\Warehouse;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class WarehouseResource
 * @property Warehouse $resource
 * @package Modules\Http\Resources\Api
 */
class WarehouseResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->getId(),
            'name' => $this->resource->getName()
        ];
    }
}
