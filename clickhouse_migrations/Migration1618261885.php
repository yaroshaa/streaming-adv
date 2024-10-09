
<?php

class Migration1618261885 implements \App\ClickHouse\Migration
{
    public function up(\ClickHouseDB\Client $client): void
    {
        $client->write("DROP TABLE IF EXISTS marketing_expense");
        $client->write("CREATE TABLE marketing_expense (
                                marketing_chanel_id UInt64,
                                market_id UInt64,
                                currency_id UInt64,
                                value Float32,
                                created_at DateTime
                            ) engine=MergeTree() ORDER BY (created_at)");
    }

    public function down(\ClickHouseDB\Client $client): void
    {
        $client->write("DROP TABLE marketing_expense");
    }
}
