<?php

namespace Modules\MarketingOverview\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class EventsDateResource
 * @package Modules\MarketingOverview\Http\Resources
 */
class HolidayEventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        $now = Carbon::now()->startOfDay()->toDateTime();
        $date = $this->resource->getDate();
        $days_before = $date->diff($now)->days;

        return  [
            'id' => $this->resource->getId(),
            "title" => $this->resource->getTitle(),
            "date" => Carbon::parse($date)->toDateTimeLocalString(),
            "days_before" => $days_before,
        ];
    }
}
