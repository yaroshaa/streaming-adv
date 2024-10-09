<?php

namespace Tests\Feature;

use Tests\TestCase;

class HelpScoutWebhookTest extends TestCase
{
    public function testCheckSignature()
    {
        $this->post('/api/help-scout')
            ->assertStatus(403); /// Request without signature

        $this->postJson('/api/help-scout', [
            'foo' => 'foo'
        ], [
            'X-Helpscout-Signature' => 'GwBM94YDJIiUW7bkp1Kw3hMy0VQ='
        ])->assertStatus(403); /// Broken signature

        $this->postJson('/api/help-scout', [
            'foo' => 'bar'
        ], [
            'X-Helpscout-Signature' => 'GwBM94YDJIiUW7bkp1Kw3hMy0VQ='
        ])->assertStatus(422); /// 422 unprocessed entity -> normal with current payloads
    }

    public function testProcessFeedback()
    {
        $data = [
            'mailboxId' => 1111111111111,
            'createdAt' => '2021-03-25T12:37:09Z',
            'preview' => '🎷test preview',
            'createdBy' => [
                'first' => 'First name',
                'last' => 'Last name'
            ],
            '_embedded' => [
                'threads' => [
                    [
                        'body' => 'text body🎷🎶🏖🏖🏕🌏🌎🌍🌍🌋🏞🏚🏠🏠'
                    ]
                ]
            ],
            '_links' => [
                'web' => [
                    'href' => 'https://secure.helpscout.net/conversation/1111111111/'
                ],
            ]
        ];

        $this->postJson('/api/help-scout', $data, [
            'X-Helpscout-Signature' => 'iIIjxCCaIUdWAwI/ZCFq2j/cbq0='
        ])->assertStatus(200);
    }
}
