
<?php

class Migration1612427596 implements \App\ClickHouse\Migration
{
    public function up(\ClickHouseDB\Client $client): void
    {
        $client->write("DROP TABLE IF EXISTS order_status_history"); /// Migrations cannot be apply without this fix
        $client->write("CREATE TABLE order_status_history (order_id UInt64, status_before UInt8, status_after UInt8, updated_at DateTime) engine=MergeTree() ORDER BY (updated_at)");
    }

    public function down(\ClickHouseDB\Client $client): void
    {
        $client->write("DROP TABLE order_status_history");
    }
}
