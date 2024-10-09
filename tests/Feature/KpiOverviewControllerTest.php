<?php

namespace Tests\Feature;

use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class KpiOverviewControllerTest extends TestCase
{
    public function testWithoutParams()
    {
        $this->get('/api/kpi-overview/totals')
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testWithParams()
    {
        $this->get('/api/kpi-overview/totals?' . Arr::query([
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
        $this->get('/api/kpi-overview/totals?' . Arr::query([
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
            'data' => [
                '*' => [
                    'date',
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
            ]
        ]);
    }
}
