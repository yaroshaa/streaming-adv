<?php

namespace Modules\MarketingOverview\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

/**
 * Class MarketingStoreGraphResource
 * @todo this example
 * @package Modules\MarketingOverview\Http\Resources
 */
class StoreGraphResource extends JsonResource
{
    const ROW_REVENUE = 'revenue.per_day';
    const ROW_ORDERS = 'orders.per_day';
    const ROW_CONTRIBUTION_MARGIN = 'contribution_margin.per_day';
    const ROW_SPEND = 'spend.per_day';
    const ROW_CONVERSATION_RATE = 'conversion_rate.per_day';
    const ROW_PROFIT = 'profit.per_day';
    const ROW_SPEND_RATIO = 'spend_ratio.per_day';
    const ROW_CONTRIBUTION_MARGIN_RATIO = 'contribution_margin_ratio.per_day';
    const ROW_CMAM = 'cmam.per_day';

    public array $fields = [
        self::ROW_REVENUE,
        self::ROW_ORDERS,
        self::ROW_CONTRIBUTION_MARGIN,
        self::ROW_SPEND,
        self::ROW_CONVERSATION_RATE,
        self::ROW_PROFIT,
        self::ROW_SPEND_RATIO,
        self::ROW_CONTRIBUTION_MARGIN_RATIO,
        self::ROW_CMAM,
    ];

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        $data = [];

        foreach ($this->resource as $store) {
            foreach ($this->fields as $field) {
                switch ($field) {
                    case self::ROW_SPEND_RATIO:
                        $row = Arr::get($store, $field, []);
                        break;
                    default;
                        $row = array_map(
                            fn($value) => number_format($value, 2),
                            Arr::get($store, $field, [])
                        );
                        break;
                }

                $key = explode('.', $field)[0];
                $data[$key][] = $row;
            }
        }

        return $data;
    }
}
