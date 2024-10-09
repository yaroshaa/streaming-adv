<?php


namespace Modules\KpiOverview\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class TableItem extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'date' => $this['date'],
            'avg_total' => (float)$this['avg_total'],
            'avg_profit' => (float)$this['avg_profit'],
            'total_packed' => $this['total_packed'],
            'total_returned' => $this['total_returned'],
            'returned_percent' => (float)$this['returned_percent'],
            'customers' => $this['customers'],
            'new_customers' => $this['new_customers'],
            'new_customers_percent' => (int)$this['new_customers_percent'],
            'orders' => $this['orders'],
            'products_count' => $this['products_count'],
            'product_discount' => (float)$this['product_discount'],
            'product_discount_percent' => (float)$this['product_discount_percent'],
        ];
    }
}
