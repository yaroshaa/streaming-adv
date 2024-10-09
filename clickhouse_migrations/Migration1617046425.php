
<?php

class Migration1617046425 implements \App\ClickHouse\Migration
{
    public function up(\ClickHouseDB\Client $client): void
    {
        $client->write("ALTER TABLE orders_products  MODIFY COLUMN product_variant_id String ");
        $client->write("ALTER TABLE order_status_history  MODIFY COLUMN order_id String ");
        $client->write("ALTER TABLE orders_products  MODIFY COLUMN order_id String ");
    }

    public function down(\ClickHouseDB\Client $client): void
    {
        $client->write("ALTER TABLE orders_products  MODIFY COLUMN product_variant_id UInt64 ");
        $client->write("ALTER TABLE order_status_history  MODIFY COLUMN order_id UInt64 ");
        $client->write("ALTER TABLE orders_products  MODIFY COLUMN order_id UInt64 ");
    }
}
