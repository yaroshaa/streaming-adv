<?php

namespace Tests\Feature;

use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class StoreTodayControllerTest extends TestCase
{
    public function testWithoutParams()
    {
        $this->get('/api/store-today/data')
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testWithParams()
    {
        $this->get('/api/store-today/data?' . Arr::query([
                'date' => [
                    '2021-05-18T00:00',
                    '2021-05-18T23:59'
                ],
                'currency' => [
                    'id' => 3,
                    'code' => 'EUR',
                    'name' => 'Euro',
                ],
                'date_granularity' => 'Daily'
            ]))->assertStatus(Response::HTTP_OK);
    }

    public function testStructure()
    {
        $this->get('/api/store-today/data?' . Arr::query([
                'date' => [
                    '2021-05-18T00:00',
                    '2021-05-18T23:59'
                ],
                'currency' => [
                    'id' => 3,
                    'code' => 'EUR',
                    'name' => 'Euro',
                ],
                'date_granularity' => 'Daily'
            ]))->assertJsonStructure([
            'kpiData' => [
                'date',
                'avg_total',
                'avg_profit',
                'total_profit',
                'total_packed',
                'total_returned',
                'returned_percent',
                'customers',
                'new_customers',
                'new_customers_percent',
                'orders',
                'products_count',
                'product_discount',
                'product_discount_percent',
            ],
            'srcData' => [
                '*' => [
                    'avg_total',
                    'avg_profit',
                    'total_packed',
                    'total_returned',
                    'returned_percent',
                    'customers',
                    'new_customers',
                    'new_customers_percent',
                    'orders',
                    'products_count',
                    'product_discount',
                    'product_discount_percent',
                ]
            ],
            'profit' => [
                'singleDay' => [
                    'value',
                    'forecast'
                ],
                'lastDays' => [
                    'count_days',
                    'value',
                    'forecast'
                ],
            ],
            'revenue' => [
                'singleDay' => [
                    'value',
                    'forecast'
                ],
                'lastDays' => [
                    'count_days',
                    'value',
                    'forecast'
                ],
            ],
            'currency_symbol'
        ]);
    }
}
