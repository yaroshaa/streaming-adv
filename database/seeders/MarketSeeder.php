<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MarketSeeder extends Seeder
{

    const TABLE_NAME = 'markets';

    public static array $markets = [
        [
            "id"=> 1,
            "name"=> "Tights.no",
            'remote_id' => 1,
            "color"=> "#ffb843",
            'icon_link' => '/images/markets/tights.no.png'
        ],
        [
            "id"=> 2,
            "name"=> "Amazon",
            'remote_id' => 2,
            "color"=> "#fc9e01",
            'icon_link' => '/images/markets/amazon.jpg'
        ],
        [
            "id"=> 3,
            "name"=> "Ebay",
            'remote_id' => 3,
            "color"=> "#c7c7c7",
            'icon_link' => '/images/markets/Ebay.ico'
        ],
        [
            "id"=> 4,
            "name"=> "comfyballs.no",
            'remote_id' => 4,
            "color"=> "#681713",
            'icon_link' => '/images/markets/comfyballs.jpeg'
        ],
        [
            "id"=> 5,
            "name"=> "comfyballs.com",
            'remote_id' => 5,
            "color"=> "#681713",
            'icon_link' => '/images/markets/comfyballs.jpeg'
        ],
        [
            "id"=> 6,
            "name"=> "comfyballs.se",
            'remote_id' => 6,
            "color"=> "#681713",
            'icon_link' => '/images/markets/comfyballs.jpeg'
        ],
        [
            "id"=> 7,
            "name"=> "Soma.no",
            'remote_id' => 7,
            "color"=> "#6ea150",
            'icon_link' => '/images/markets/soma.no.png'
        ],
        [
            "id"=> 8,
            "name"=> "weightless.no",
            'remote_id' => 8,
            "color"=> "#dc5ecf",
            'icon_link'=> '/images/markets/weightless.no.png'
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

        DB::table(self::TABLE_NAME)->truncate();
        foreach (self::$markets as $market) {
            DB::table(self::TABLE_NAME)->insert($market);
        }

        Schema::enableForeignKeyConstraints();
    }
}
