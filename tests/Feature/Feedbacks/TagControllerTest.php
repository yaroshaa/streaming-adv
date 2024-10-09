<?php

namespace Tests\Feature\Feedbacks;

use Tests\TestCase;

class TagControllerTest extends TestCase
{
    const ENTRY_POINT = '/api/settings/tag';
    public static $name = '';

    public function testList()
    {
        return $this->get(self::ENTRY_POINT)->assertOk()->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'color',
                    'keywords' => [
                        '*' => [

                        ]
                    ]
                ]
            ]
        ]);
    }

    public function testCRUD()
    {
        $response = $this->post(self::ENTRY_POINT, [
            'name' => self::getTagName(),
            'color' => 'red',
            'keywords' => [
                'foo',
                'bar'
            ]
        ])->assertCreated();

        $createdModel = json_decode($response->content(), true);
        $id = $createdModel['id'];


        $this->put(self::ENTRY_POINT . '/' . $id, [
            'id' => $id,
            'name' => self::getTagName() . '_changes',
            'color' => 'electric-green',
            'keywords' => [
                'zoo',
                'boo'
            ]
        ])->assertOk();

        $this->delete(self::ENTRY_POINT . '/' . $id)->assertOk();
    }

    private static function getTagName(): string
    {
        if (empty(self::$name)) {
            self::$name = 'name_' . time();
        }

        return self::$name;
    }
}
