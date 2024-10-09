<?php

namespace Tests\Feature;

use Tests\TestCase;

class FeedbackControllerTest extends TestCase
{
    public function testIndex()
    {
        $this->get('/api/feedbacks')->assertStatus(200);
    }

    public function testInsert()
    {
        $data = [
            'data' => [
                [
                    "created_at" => "2021-03-19T09:40:22.145Z",
                    "market_id" => "2",
                    "message" => "TITAN LIFE Training Mat 180x60x1.5cm - Grey",
                    "name" => "Huw Hibbert" . time(),
                    "url" => "https://google.com",
                    "source_id" => "2"
                ],
                [
                    "market_id" => "3",
                    "message" => "TITAN LIFE Training Mat 180x60x1.5cm - Grey",
                    "created_at" => "2021-03-19T09:45:22.145Z",
                    "name" => "Huw Hibbert" . time(),
                    "source_id" => "4"
                ],
                [
                    "market_id" => "4",
                    "message" => str_repeat("TITAN LIFE Training Mat 180x60x1.5cm - Grey", 30),
                    "created_at" => "2021-03-19T09:45:22.145Z",
                    "name" => "Huw Hibbert" . time(),
                    "source_id" => "4"
                ],
            ]
        ];

        $this->post('/api/feedbacks', $data)->assertStatus(200);
    }
}
