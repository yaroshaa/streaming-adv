<?php

namespace Modules\MarketingOverview\Http\Resources;

use App\ClickHouse\ClickHouseException;
use App\ClickHouse\DateGranularityInterface;
use Modules\MarketingOverview\Services\MarketViewService;
use Exception;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class MarketingOverviewResource
 * @property MarketViewService $resource
 * @package App\Http\Resources\MarketingOverview
 */
class MarketingOverviewResource extends JsonResource
{
    /**
     * @throws ClickHouseException
     * @throws Exception
     */
    public function toArray($request): array
    {
        return [
            "date_start" => $this->resource->from->toISOString(),
            "date_end" => $this->resource->to->toISOString(),
            "streak" => $this->resource->getStreak(),
            "break_even" => 600432,
            "over_period" => [
                "day" => $this->resource->calcOverPeriod(DateGranularityInterface::DAY_GRANULARITY),
                "week" => $this->resource->calcOverPeriod(DateGranularityInterface::WEEK_GRANULARITY),
                "month" => $this->resource->calcOverPeriod(DateGranularityInterface::MONTH_GRANULARITY)
            ],
            "stores" => $this->resource->marketingOverviewByStores(),
            "totals" => $this->resource->marketingOverviewTotal(),
            "spend" => $this->resource->marketingOverviewSpend(),
            "conversion_indicators" => $this->resource->marketingOverviewConversationIndicator(),
            "warehouses" => $this->resource->warehouseStatistic(),
            "year_to_day" => $this->resource->calcYearToDayStat(),
            "overview_table" => new TableResource($this->resource->getOverviewTotalsTableWidget()),
            "events" => HolidayEventResource::collection($this->resource->getEvents()),
            // Indicators
            'indicators' => [
                IndicatorResource::collection($this->resource->getMainIndicators()),
                IndicatorResource::collection($this->resource->getSecondaryIndicators())
            ]
        ];
    }
}
