<?php

namespace Modules\Api\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class SuccessResource
 * @property SuccessResource $resource
 * @package Modules\Http\Resources\Api
 */
class SuccessResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'message' => $this->resource
        ];
    }
}
