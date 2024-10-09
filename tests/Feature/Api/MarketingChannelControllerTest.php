<?php

namespace Tests\Feature\Api;

use Tests\TestCase;

class MarketingChannelControllerTest extends TestCase
{
    const ENTRY_POINT = '/api/marketing-channel';
    public static $name = '';

    public function testList()
    {
        return $this->get(self::ENTRY_POINT)->assertOk()->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'icon_link'
                ]
            ]
        ]);
    }

    public function testCRUD()
    {
        $response = $this->post(self::ENTRY_POINT, [
            'name' => self::getChannelName(),
            'icon_link' => '/some/foo/bar/icon.jpg'
        ])->assertCreated();

        $createdModel = json_decode($response->content(), true);
        $id = \Illuminate\Support\Arr::get($createdModel, 'data.id', 0);

        $this->get(self::ENTRY_POINT . '/' . $id)->assertJson($createdModel);

        $this->put(self::ENTRY_POINT . '/' . $id, [
            'name' => self::getChannelName() . '_changes',
            'icon_link' => '/some/foo/bar/icon.jpg'
        ])->assertOk();

        $this->delete(self::ENTRY_POINT . '/' . $id)->assertOk();
    }

    private static function getChannelName(): string
    {
        if (empty(self::$name)) {
            self::$name = 'name_' . time();
        }

        return self::$name;
    }
}
