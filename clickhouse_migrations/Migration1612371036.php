
<?php

class Migration1612371036 implements \App\ClickHouse\Migration
{
    public function up(\ClickHouseDB\Client $client): void
    {
        $client->write("DROP TABLE IF EXISTS orders_products");
        $client->write("CREATE TABLE orders_products (product_variant_id UInt64, order_id UInt64, customer_id UInt64, market_id UInt64, currency_id UInt64, product_price Float32, product_profit Float32, product_discount Float32, product_qty UInt16, product_weight Float32, updated_at DateTime) engine=MergeTree() ORDER BY (updated_at)");
    }

    public function down(\ClickHouseDB\Client $client): void
    {
        $client->write("DROP TABLE orders_products");
    }
}
