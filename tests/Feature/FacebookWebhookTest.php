<?php

namespace Tests\Feature;

use Tests\TestCase;

class FacebookWebhookTest extends TestCase
{
    public function testRegisterWebhook()
    {
        $this->json('GET', '/api/facebook', [
            'hub_challenge' => 457774047,
            'hub_mode' => 'subscribe',
            'hub_verify_token' => 1
        ])->assertSeeText(457774047);
    }

    public function testProcessFeedback()
    {
        $data = [
            'object' => 'page',
            'entry' => [
                [
                    'id' => 537794966626992,
                    'time' => 1616086634,
                    'changes' => [
                        [
                            "value" => [
                                "from" => [
                                    'id' => '1111111111111',
                                    'name' => 'FB Test name',
                                ],
                                "post" => [
                                    "status_type" => "added_photos",
                                    "is_published" => true,
                                    "updated_time" => "2021-03-18T16:57:11+0000",
                                    "permalink_url" => "https://www.facebook.com/brovitochka/photos/a.537795019960320/538285423244613/",
                                    "promotion_status" => "ineligible",
                                    "id" => "537794966626992_538285423244613"
                                ],
                                "message" => "FB test",
                                "post_id" => "537794966626992_538285423244613",
                                "comment_id" => "538285423244613_982312698841881",
                                "created_time" => 1616086631,
                                "item" => "comment",
                                "parent_id" => "537794966626992_538285423244613",
                                "verb" => "add"
                            ],
                            "field" => "feed"
                        ]
                    ]
                ]
            ]
        ];

        $this->post('/api/facebook', $data)->assertStatus(200);
    }
}
