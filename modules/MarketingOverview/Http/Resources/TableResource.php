<?php

namespace Modules\MarketingOverview\Http\Resources;

use App\ClickHouse\ClickHouseException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\MarketingOverview\Services\Widgets\OverviewTotalsTableWidget;

/**
 * Class TableResource
 * @property OverviewTotalsTableWidget $resource
 * @package Modules\MarketingOvervie\Http\Resources\MarketingOverview
 */
class TableResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     * @throws ClickHouseException
     */
    public function toArray($request): array
    {
        return [
            'header' => $this->resource->getHeader(),
            'data' => $this->resource->getRows()
        ];
    }

}
