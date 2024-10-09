<?php

class Migration1619684573 implements \App\ClickHouse\Migration
{
    public function up(\ClickHouseDB\Client $client): void
    {
        $client->write("ALTER TABLE warehouse_statistic ADD COLUMN station UInt64");
        $client->write("ALTER TABLE warehouse_statistic ADD COLUMN market_id UInt64");
    }

    public function down(\ClickHouseDB\Client $client): void
    {
        $client->write("ALTER TABLE warehouse_statistic DROP COLUMN station");
        $client->write("ALTER TABLE warehouse_statistic DROP COLUMN market_id");
    }
}
