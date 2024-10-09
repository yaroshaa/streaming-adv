<?php

namespace Tests\Feature;

use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class OrdersControllerTest extends TestCase
{
    const ORDER_INSERT = '/api/orders';
    const ORDER_GET = '/api/orders?';
    const TOP_SELLING_PRODUCTS = '/api/top-selling-products?';
    const FIFTEEN_MIN_TOTALS = '/api/fifteen-min-totals?';
    public static $filterArray = [
        'limit' => 20,
        'currency' => [
            'id' => 3,
            'code' => 'EUR',
            'name' => 'Euro',
        ],
        'percentile' => '',
        'weight' => '',
        'from' => '2021-05-20T21:00:00.000+00:00'
    ];

    public function testInsert()
    {

    }

    public function testList()
    {
        $this->get(self::ORDER_GET)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->get(self::ORDER_GET . Arr::query(self::$filterArray))->assertOk();
        $this->get(self::ORDER_GET . Arr::query(self::$filterArray))->assertJsonStructure([
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

    public function testTopSellingProducts()
    {
        $this->get(self::TOP_SELLING_PRODUCTS)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->get(self::TOP_SELLING_PRODUCTS . Arr::query(self::$filterArray))->assertOk();
        $this->get(self::TOP_SELLING_PRODUCTS . Arr::query(self::$filterArray))->assertJsonStructure([
            '*' => [
                'product_variant_id',
                'product_name',
                'product_weight',
                'product_link',
                'product_image_link',
                'market_name',
                'market_icon_link',
                'product_qty',
                'product_price',
                'product_discount',
                'product_profit',
                'status_id',
            ]
        ]);
    }

    public function testFifteenMinTotals()
    {
        $this->get(self::FIFTEEN_MIN_TOTALS)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->get(self::FIFTEEN_MIN_TOTALS . Arr::query(self::$filterArray))->assertOk();
        $this->get(self::FIFTEEN_MIN_TOTALS . Arr::query(self::$filterArray))->assertJsonStructure([
            'totals' => [
                '*' => [
                    'profit',
                    'total',
                    'orders_count',
                    'interval'
                ]
            ],
            'history' => [
                '*' => [
                    'profit',
                    'total',
                    'orders_count',
                    'date'
                ]
            ]
        ]);
    }
}
