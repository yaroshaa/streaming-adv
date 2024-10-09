<?php

namespace Tests\Feature\HolidayEvent;

use Illuminate\Support\Arr;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class HolidayEventControllerTest extends TestCase
{
    const ENTRY_POINT = '/api/settings/holiday-event';
    public static string $title = '';

    public function testList(): TestResponse
    {
        return $this->get(self::ENTRY_POINT)->assertOk()->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'date',
                    'days_before'
                ]
            ]
        ]);
    }

    public function testCRUD()
    {
        $response = $this->post(self::ENTRY_POINT, [
            'title' => self::getTitle(),
            'date' => '2021-05-18T21:00:00.000Z',
            'days_before' => 33,
        ])->assertCreated();

        $createdModel = json_decode($response->content(), true);
        $id = Arr::get($createdModel, 'data.id', 0);

        $this->put(self::ENTRY_POINT . '/' . $id, [
            'id' => $id,
            'title' => self::getTitle() . '_changes',
            'date' => '2021-06-18T21:00:00.000Z',
            'days_before' => 22
        ])->assertOk();

        $this->delete(self::ENTRY_POINT . '/' . $id)->assertOk();
    }

    private static function getTitle(): string
    {
        if (empty(self::$title)) {
            self::$title = 'title_' . time();
        }

        return self::$title;
    }
}
