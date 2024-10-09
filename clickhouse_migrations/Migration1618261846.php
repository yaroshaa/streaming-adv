
<?php

class Migration1618261846 implements \App\ClickHouse\Migration
{
    public function up(\ClickHouseDB\Client $client): void
    {
        $client->write("DROP TABLE IF EXISTS warehouse_statistic");
        $client->write("CREATE TABLE warehouse_statistic (
                                warehouse_id UInt64,
                                in_packing UInt16,
                                open UInt16,
                                awaiting_stock UInt16
                            ) engine=MergeTree() ORDER BY (warehouse_id)");
    }

    public function down(\ClickHouseDB\Client $client): void
    {
        $client->write("DROP TABLE warehouse_statistic");
    }
}
