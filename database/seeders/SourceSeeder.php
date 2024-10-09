<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SourceSeeder extends Seeder
{
    const SOURCE_TABLE_NAME = 'sources';

    public static array $sources = [
        [
            'id' => 1,
            'name' => 'Facebook',
            'remote_id' => 1,
            'icon_link' => '/images/sources/facebook.png'
        ], [
            'id' => 2,
            'name' => 'Site',
            'remote_id' => 3,
            'icon_link' => '/images/sources/site.png'
        ], [
            'id' => 3,
            'name' => 'Direct',
            'remote_id' => 2,
            'icon_link' => '/images/sources/direct.png'
        ], [
            'id' => 4,
            'name' => 'Offline',
            'remote_id' => 4,
            'icon_link' => '/images/sources/offline.png'
        ], [
            'id' => 5,
            'name' => 'Help Scout',
            'remote_id' => 5,
            'icon_link' => '/images/sources/helpscout.svg'
        ],
    ];


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        DB::table(self::SOURCE_TABLE_NAME)->truncate();
        foreach (self::$sources as $source) {
            DB::table(self::SOURCE_TABLE_NAME)->insert($source);
        }

        Schema::enableForeignKeyConstraints();
    }
}
