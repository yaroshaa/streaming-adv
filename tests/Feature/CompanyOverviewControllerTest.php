<?php

namespace Tests\Feature;

use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CompanyOverviewControllerTest extends TestCase
{
    const URL_TOTALS = '/api/company-overview/totals?';
    const URL_TOTALS_BY_MARKET = '/api/company-overview/totals-by-market?';
    const URL_ORDERS = '/api/company-overview/orders?';
    const URL_STREAM_GRAPH_DATA = '/api/company-overview/stream-graph-data?';
    const URL_PIE_CHART_DATA = '/api/company-overview/pie-chart-data?';

    public static array $filterArray = [
        'currency' => [
            'id' => 3,
            'code' => 'EUR',
            'name' => 'Euro',
        ],
        'date' => [
            '2021-04-17T21:00:00.000Z',
            '2021-05-18T20:59:59.999Z'
        ]
    ];

    public function testTotals()
    {
        $this->get(self::URL_TOTALS)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->get(self::URL_TOTALS . Arr::query(self::$filterArray))->assertOk();
        $this->get(self::URL_TOTALS . Arr::query(self::$filterArray))->assertJsonStructure([
            'previous' => [
                'profit',
                'total',
                'orders_count',
                'interval'
            ],
            'current' => [
                'profit',
                'total',
                'orders_count',
                'interval'
            ]
        ]);
    }

    public function testTotalsByMarket()
    {
        $this->get(self::URL_TOTALS_BY_MARKET)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->get(self::URL_TOTALS_BY_MARKET . Arr::query(self::$filterArray))->assertOk();

        $totalItem = [
            'sign',
            'diff',
            'diff_percentage',
            'previous',
            'current'
        ];

        $this->get(self::URL_TOTALS_BY_MARKET . Arr::query(self::$filterArray))->assertJsonStructure([
            'avg_total' => $totalItem,
            'avg_profit' => $totalItem,
            'total_packed' => $totalItem,
            'time_packed' => $totalItem,
            'total_returned' => $totalItem,
            'returned_percent' => $totalItem,
            'customers' => $totalItem,
            'new_customers' => $totalItem,
            'new_customers_percent' => $totalItem,
            'orders' => $totalItem,
            'products_count' => $totalItem,
            'product_discount' => $totalItem,
            'product_discount_percent' => $totalItem,
        ]);
    }

    public function testOrders()
    {
        $this->get(self::URL_ORDERS)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->get(self::URL_ORDERS . Arr::query(self::$filterArray))->assertOk();
        $this->get(self::URL_ORDERS . Arr::query(self::$filterArray))->assertJsonStructure([
            '*' => [
                'order_id',
                'product_variant_id',
                'product_qty',
                'updated_at',
                'status_id',
                'status_name',
                'status_color',
                'customer_id',
                'customer_name',
                'customer_created_at',
                'market_id',
                'currency_id',
                'address_id',
                'address',
                'address_lat',
                'address_lng',
                'market_name',
                'market_icon_link',
                'product_weight',
                'product_profit',
                'product_price',
                'product_discount',
                'product_name',
                'product_link',
                'product_image_link',
                'products' => [
                    '*' => [
                        'order_id',
                        'product_variant_id',
                        'product_qty',
                        'updated_at',
                        'status_id',
                        'status_name',
                        'status_color',
                        'customer_id',
                        'customer_name',
                        'customer_created_at',
                        'market_id',
                        'currency_id',
                        'address_id',
                        'address',
                        'address_lat',
                        'address_lng',
                        'market_name',
                        'market_icon_link',
                        'product_weight',
                        'product_profit',
                        'product_price',
                        'product_discount',
                        'product_name',
                        'product_link',
                        'product_image_link',
                    ]
                ]
            ]
        ]);
    }

    public function testPieChartData()
    {
        $this->get(self::URL_PIE_CHART_DATA)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->get(self::URL_PIE_CHART_DATA . Arr::query(self::$filterArray))->assertOk();
        $this->get(self::URL_PIE_CHART_DATA . Arr::query(self::$filterArray))->assertJsonStructure([
            '*' => [
                'name',
                'value'
            ]
        ]);
    }

    public function testStreamGraphData()
    {
        $this->get(self::URL_STREAM_GRAPH_DATA)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->get(self::URL_STREAM_GRAPH_DATA . Arr::query(self::$filterArray))->assertOk();
        $this->get(self::URL_STREAM_GRAPH_DATA . Arr::query(self::$filterArray))->assertJsonStructure([
            '*' => [
                'date'
                // How to test?
                // {"Tights.no":41517,"date":"2021-05-11","Amazon":87400,"Ebay":65235,"comfyballs.no":48750}
            ]
        ]);
    }
}
