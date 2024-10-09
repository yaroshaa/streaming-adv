<?php

namespace Modules\ModuleName\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\ModuleName\Services\ModuleNameService;

/**
 * Class ModuleNameResource
 * @property ModuleNameService $resource
 * @package Modules\ModuleName\Http\Resources
 */
class ModuleNameResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'message' => sprintf('Calculated value: %s', $this->resource->calculate())
        ];
    }
}
