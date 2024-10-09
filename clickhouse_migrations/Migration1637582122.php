
<?php

class Migration1637582122 implements \App\ClickHouse\Migration
{
    public function up(\ClickHouseDB\Client $client): void
    {
        $client->write("ALTER TABLE analytics_events ADD COLUMN session_id String");
    }

    public function down(\ClickHouseDB\Client $client): void
    {
        $client->write("ALTER TABLE analytics_events DROP COLUMN session_id String");
    }
}
