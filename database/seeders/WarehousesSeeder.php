<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Class WarehousesSeeder
 *
 * @package Database\Seeders
 */
class WarehousesSeeder extends Seeder
{
    const TABLE_NAME = 'warehouses';

    private static array $warehouses = [
        [
            'id' => 1,
            'name' => 'Vestby'
        ],
        [
            'id' => 2,
            'name' => 'BORÅS'
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
        foreach (self::$warehouses as $market) {
            DB::table(self::TABLE_NAME)->insert($market);
        }

        Schema::enableForeignKeyConstraints();
    }
}
