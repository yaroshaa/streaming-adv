<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CurrencySeeder extends Seeder
{

    const TABLE_NAME = 'currencies';

    public static array $markets = [
        [
            'id' => 1,
            'code' => 'EUR',
            'name' => 'EUR',
        ],
        [
            'id' => 2,
            'code' => 'NOK',
            'name' => 'Norsk krone',
        ],
        [
            'id' => 3,
            'code' => 'USD',
            'name' => 'US Dollar',
        ],
        [
            'id' => 4,
            'code' => 'DKK',
            'name' => 'Dansk krone',
        ],
        [
            'id' => 5,
            'code' => 'GBP',
            'name' => 'GBP',
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
        //DB::table(self::TABLE_NAME)->truncate();
        foreach (self::$markets as $market) {
            DB::table(self::TABLE_NAME)->insertOrIgnore($market);
        }

        Schema::enableForeignKeyConstraints();
    }
}
