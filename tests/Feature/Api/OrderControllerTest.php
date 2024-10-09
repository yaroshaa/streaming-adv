<?php

namespace Tests\Feature\Api;

use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    const ENTRY_POINT = '/api/orders';

    public function testCreate()
    {
        $this->post(self::ENTRY_POINT, [
            'data' => [
                [
                    'address' => 'вулиця Банкова, 11, Київ, 01220',
                    'currency' => [
                        'name' => 'USD',
                        'code' => 'USD',
                    ],
                    'customer' => [
                        'name' => 'Liyah Swift',
                        'remote_id' => 6554,
                    ],
                    "market" => [
                        "name" => "comfyballs.no",
                        "remote_id" => 4,
                        "iconLink" => "/images/markets/comfyballs.jpeg",
                        "color" => "green"
                    ],
                    "status" => [
                        "name" => "new",
                        "remote_id" => 4,
                        "color" => "green"
                    ],
                    'packing_cost' => 126.3,
                    "products" => [
                        [
                            "remote_id" => '301498',
                            "price" => 738.95,
                            "profit" => 253.39,
                            "qty" => 2,
                            "name" => "TITAN LIFE Training Mat 180x60x1.5cm - Grey",
                            "link" => "https://www.tights.no/butikk/titan-life-training-mat-180x60x1-5cm-grey/",
                            "image_link" => "https://www.tights.no/wp-content/uploads/sites/7/2020/11/training_mat.jpg",
                            "discount" => 6.23,
                            "weight" => 2.05
                        ],
                        [
                            "remote_id" => '301497',
                            "price" => 738.95,
                            "profit" => 253.39,
                            "qty" => 2,
                            "name" => "TITAN LIFE Training Mat 180x60x1.5cm - Grey",
                            "link" => "https://www.tights.no/butikk/titan-life-training-mat-180x60x1-5cm-grey/",
                            "image_link" => "https://www.tights.no/wp-content/uploads/sites/7/2020/11/training_mat.jpg",
                            "discount" => 6.23,
                            "weight" => 2.05
                        ],
                    ],
                    "warehouse" => [
                        "name" => "Vestby"
                    ],
                    "order_id" => time(),
                    "remote_id" => time(),
                    "updated_at" => "2021-05-24T18:54:29.353Z"
                ]
            ]
        ])->assertOk();
    }
}
