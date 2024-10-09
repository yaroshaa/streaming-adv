
<?php

class Migration1618560811 implements \App\ClickHouse\Migration
{
    public function up(\ClickHouseDB\Client $client): void
    {
        $client->write("ALTER TABLE warehouse_statistic ADD COLUMN created_at DateTime");
    }

    public function down(\ClickHouseDB\Client $client): void
    {
        $client->write("ALTER TABLE warehouse_statistic DROP COLUMN created_at");
    }
}
