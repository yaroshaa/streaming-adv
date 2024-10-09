<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MarketingChannelsSeeder extends Seeder
{
    const TABLE_NAME = 'marketing_channels';

    private static array $marketingChannels = [
        [
            'id' => 1,
            'name' => 'Facebook',
            'icon_link' => '/images/marketing_channels/facebook.png'
        ],
        [
            'id' => 2,
            'name' => 'Google',
            'icon_link' => '/images/marketing_channels/google.png'
        ],
        [
            'id' => 3,
            'name' => 'Adform',
            'icon_link' => '/images/marketing_channels/adform.png'
        ],
        [
            'id' => 4,
            'name' => 'Snapchat',
            'icon_link' => '/images/marketing_channels/snapchat.png'
        ],
        [
            'id' => 5,
            'name' => 'Affiliate',
            'icon_link' => '/images/marketing_channels/affiliate.png'
        ],
        [
            'id' => 6,
            'name' => 'Offline',
            'icon_link' => '/images/marketing_channels/offline.png'
        ],
        [
            'id' => 7,
            'name' => 'Other',
            'icon_link' => '/images/marketing_channels/other.png'
        ],
        [
            'id' => 8,
            'name' => 'Performission',
            'icon_link' => '/images/marketing_channels/other.png'
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
        foreach (self::$marketingChannels as $market) {
            DB::table(self::TABLE_NAME)->insert($market);
        }

        Schema::enableForeignKeyConstraints();
    }
}
