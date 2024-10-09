
<?php

class Migration1618261708 implements \App\ClickHouse\Migration
{
    public function up(\ClickHouseDB\Client $client): void
    {
        $client->write("DROP TABLE IF EXISTS user_history_statistics");
        $client->write("CREATE TABLE user_history_statistics (
                                market_id UInt64, 
                                ip IPv4,
                                status UInt8, 
                                created_at DateTime
                            ) engine=MergeTree() ORDER BY (created_at)");
    }

    public function down(\ClickHouseDB\Client $client): void
    {
        $client->write("DROP TABLE user_history_statistics");
    }
}
