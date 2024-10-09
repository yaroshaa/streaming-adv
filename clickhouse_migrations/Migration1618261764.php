
<?php

class Migration1618261764 implements \App\ClickHouse\Migration
{
    public function up(\ClickHouseDB\Client $client): void
    {
        $client->write("DROP TABLE IF EXISTS active_users");
        $client->write("CREATE TABLE active_users (
                                market_id UInt64, 
                                ip IPv4,
                                status UInt8, 
                                created_at DateTime
                            ) engine=MergeTree() ORDER BY (created_at)");
    }

    public function down(\ClickHouseDB\Client $client): void
    {
        $client->write("DROP TABLE active_users");
    }
}
