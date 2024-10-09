<?php


namespace App\ClickHouse;


use ClickHouseDB\Client;

interface Migration
{
    /**
     * @param Client $client
     * @return void
     */
    public function up(Client $client): void;

    /**
     * @param Client $client
     * @return void
     */
    public function down(Client $client): void;
}