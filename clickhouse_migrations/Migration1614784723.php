
<?php

class Migration1614784723 implements \App\ClickHouse\Migration
{
    public function up(\ClickHouseDB\Client $client): void
    {
        $client->write("ALTER TABLE feedbacks  ADD COLUMN unique_id String ,  ADD COLUMN url String ");
    }

    public function down(\ClickHouseDB\Client $client): void
    {
        $client->write("ALTER TABLE feedbacks  DROP COLUMN unique_id ,  DROP COLUMN url ");
    }
}
