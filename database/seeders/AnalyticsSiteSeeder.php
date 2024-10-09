<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Class AnalyticsSiteSeeder
 * @package Database\Seeders
 */
class AnalyticsSiteSeeder extends Seeder
{

    const TABLE_NAME = 'analytics_sites';

    public static array $sites = [
        [
            'id' => 1,
            'name' => 'example.com',
            'key' => '123-456-789',
        ],
        [
            'id' => 2,
            'name' => 'next-example.com',
            'key' => '123-456-799',
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

        foreach (self::$sites as $site) {
            DB::table(self::TABLE_NAME)->insertOrIgnore($site);
        }

        Schema::enableForeignKeyConstraints();
    }
}
