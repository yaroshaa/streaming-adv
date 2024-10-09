
<?php

class Migration1613658104 implements \App\ClickHouse\Migration
{
    public function up(\ClickHouseDB\Client $client): void
    {
        $client->write("DROP TABLE IF EXISTS feedbacks");
        $client->write("CREATE TABLE feedbacks (source_id UInt64, market_id UInt64, name String, message String, created_at DateTime) engine=MergeTree() ORDER BY (created_at)");
    }

    public function down(\ClickHouseDB\Client $client): void
    {
        $client->write("DROP TABLE feedbacks");
    }
}
