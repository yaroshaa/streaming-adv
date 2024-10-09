
<?php

class Migration1637141266 implements \App\ClickHouse\Migration
{
    public function up(\ClickHouseDB\Client $client): void
    {
        $client->write("DROP TABLE IF EXISTS analytics_event_properties");
        $client->write("CREATE TABLE analytics_event_properties (property_id String, event_id String, name String, value String, created_at DateTime) engine=MergeTree() ORDER BY (created_at)");
    }

    public function down(\ClickHouseDB\Client $client): void
    {
        $client->write("DROP TABLE analytics_event_properties");
    }
}
