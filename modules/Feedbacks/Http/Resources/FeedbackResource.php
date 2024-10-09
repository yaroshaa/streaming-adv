<?php

namespace Modules\Feedbacks\Http\Resources;

use App\Repositories\MarketRepository;
use App\Repositories\SourceRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class FeedbackResource
 * @package Modules\Feedbacks\Http\Resources
 */
class FeedbackResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        $market = MarketRepository::findByRemoteId($this->resource['market_id']);
        $source = SourceRepository::findByRemoteId($this->resource['source_id']);

        return [
            'unique_id' => $this->resource['unique_id'],
            'name' => $this->resource['name'],
            'message' => $this->resource['message'],
            'created_at' => Carbon::parse($this->resource['created_at'])->toDateTimeLocalString(),
            'market_name' => optional($market)->getName(),
            'source_name' => optional($source)->getName(),
            'market_icon_link' => optional($market)->getIconLink(),
            'source_icon_link' => optional($source)->getIconLink(),
            'url' => $this->resource['url'] ?? '',
        ];
    }
}
