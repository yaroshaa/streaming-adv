
<?php

class Migration1637141256 implements \App\ClickHouse\Migration
{
    public function up(\ClickHouseDB\Client $client): void
    {
        $client->write("DROP TABLE IF EXISTS analytics_events");
        $client->write("CREATE TABLE analytics_events (event_id String, site_id UInt64, name String, created_at DateTime) engine=MergeTree() ORDER BY (created_at)");
    }

    public function down(\ClickHouseDB\Client $client): void
    {
        $client->write("DROP TABLE analytics_events");
    }
}
