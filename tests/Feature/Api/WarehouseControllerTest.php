<?php

namespace Tests\Feature\Api;

use Tests\TestCase;

class WarehouseControllerTest extends TestCase
{
    const ENTRY_POINT = '/api/warehouse';
    public static $name = '';

    public function testList()
    {
        return $this->get(self::ENTRY_POINT)->assertOk()->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                ]
            ]
        ]);
    }

    public function testCRUD()
    {
        $response = $this->post(self::ENTRY_POINT, [
            'name' => self::getWarehouseName()
        ])->assertCreated();

        $createdModel = json_decode($response->content(), true);
        $id = \Illuminate\Support\Arr::get($createdModel, 'data.id', 0);

        $this->get(self::ENTRY_POINT . '/' . $id)->assertJson($createdModel);

        $this->put(self::ENTRY_POINT . '/' . $id, [
            'name' => self::getWarehouseName() . '_changes'
        ])->assertOk();

        $this->delete(self::ENTRY_POINT . '/' . $id)->assertOk();
    }

    private static function getWarehouseName()
    {
        if (empty(self::$name)) {
            self::$name = 'name_' . time();
        }

        return self::$name;
    }
}
