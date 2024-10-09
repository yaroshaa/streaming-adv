<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HolidayEventsSeeder extends Seeder
{
    const TABLE_NAME = 'holiday_events';

    private static array $holidayEvents = [
        [
            'id' => 1,
            'title' => 'Easter',
            'date' => '2021-04-04'
        ],
        [
            'id' => 2,
            'title' => 'Green Friday',
            'date' => '2021-03-15'
        ],
        [
            'id' => 3,
            'title' => 'Midsummer',
            'date' => '2021-07-26'
        ],
        [
            'id' => 4,
            'title' => 'New Year',
            'date' => '2022-01-01'
        ]
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        DB::table(self::TABLE_NAME)->truncate();
        foreach (self::$holidayEvents as $market) {
            DB::table(self::TABLE_NAME)->insert($market);
        }

        Schema::enableForeignKeyConstraints();
    }
}
