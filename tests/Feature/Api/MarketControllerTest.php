<?php

namespace Tests\Feature\Api;

use Tests\TestCase;

class MarketControllerTest extends TestCase
{
    const ENTRY_POINT = '/api/market';

    public static $name = '';

    public function testList()
    {
        return $this->get(self::ENTRY_POINT)->assertOk()->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'remote_id',
                    'icon_link',
                    'color'
                ]
            ]
        ]);
    }

    public function testCRUD()
    {
        $response = $this->post(self::ENTRY_POINT, [
            'name' => self::getMarketName(),
            'remote_id' => time(),
            'icon_link' => '/some/foo/bar/icon.jpg',
            'color' => '123',
        ])->assertCreated();

        $createdModel = json_decode($response->content(), true);
        $id = \Illuminate\Support\Arr::get($createdModel, 'data.id', 0);

        $this->get(self::ENTRY_POINT . '/' . $id)->assertJson($createdModel);

        $this->put(self::ENTRY_POINT . '/' . $id, [
            'name' => self::getMarketName() . '_changes',
            'remote_id' => time(),
            'icon_link' => '/some/foo/bar/icon.jpg',
            'color' => '123',
        ])->assertOk();

        $this->delete(self::ENTRY_POINT . '/' . $id)->assertOk();
    }

    private static function getMarketName(): string
    {
        if (empty(self::$name)) {
            self::$name = 'name_' . time();
        }

        return self::$name;
    }
}
