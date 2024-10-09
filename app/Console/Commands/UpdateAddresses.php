<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;

class UpdateAddresses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'address:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(Client $client)
    {
        $url = 'https://maps.googleapis.com/maps/api/geocode/json';
        $params = [
            'address' => preg_replace('/(\w+)\s/', '$1+', 'Olaf Schous vei 18 0572 Oslo'),
            'key' => 'AIzaSyDTZPZNM6zmomH6ONB8YHKeoY-vV2RgYZw'
        ];

        $response = $client->get($url, ['query' => $params]);

        $body = json_decode($response->getBody()->getContents(), 1);

        return 0;
    }
}
