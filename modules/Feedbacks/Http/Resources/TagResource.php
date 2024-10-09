<?php

namespace Modules\Feedbacks\Http\Resources;

use App\Entities\Tag;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class TagResource
 * @property Tag $resource
 * @package Modules\Feedbacks\Http\Resources
 */
class TagResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->resource->getId(),
            'name' => $this->resource->getName(),
            'color' => $this->resource->getColor(),
            'keywords' => $this->resource->getKeywords()
        ];
    }
}
