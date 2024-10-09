
<?php

class Migration1618261806 implements \App\ClickHouse\Migration
{
    public function up(\ClickHouseDB\Client $client): void
    {
        $client->write("DROP TABLE IF EXISTS cart_actions");
        $client->write("CREATE TABLE cart_actions (
                                market_id UInt64, 
                                ip IPv4,
                                status UInt8, 
                                created_at DateTime
                            ) engine=MergeTree() ORDER BY (created_at)");
    }

    public function down(\ClickHouseDB\Client $client): void
    {
        $client->write("DROP TABLE cart_actions");
    }
}
