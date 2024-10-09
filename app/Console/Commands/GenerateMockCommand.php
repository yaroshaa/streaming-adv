<?php

namespace App\Console\Commands;

use ClickHouseDB\Client;
use Illuminate\Console\Command;

class GenerateMockCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:mock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate mock data';

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
        $now = new \DateTimeImmutable();
        $products = [];
        $orders = [];
        $ordersInsert = [];
        for ($i = 0; $i < 10000; $i++) {
            $price = rand(100, 1000);
            $orderId = rand(1, 9999999);
            $order = [
                'order_id' => $orderId,
                'updated_at' => $now->modify(sprintf('- %d days', rand(0, 365 * 1))),
                'status_id' => rand(1, 7),
                'customer_id' => rand(1, 10),
                'market_id' => rand(1, 4),
                'currency_id' => rand(1, 4),
                'address_id' => rand(1, 3)
            ];

            if (array_key_exists($orderId, $orders)) {
                continue;
            }

            $orders[$orderId] = $order;
            $ordersInsert[] = $order;
            $discount = array_rand([0, 0, 0, 0, 0, 1, 1, 1, 1, 3, 3, 2, 5, 7, 10,]);

            for ($k = 0; $k < rand(1, 3); $k++) {
                $products[] = [
                    'order_id' => $orderId,
                    'product_variant_id' => rand(1, 3175),
                    'product_price' => $price + (rand(0, 99) / 100),
                    'product_qty' => rand(1, 3),
                    'product_profit' => $price * (rand(5, 20) / 100),
                    'product_weight' => rand(1, 100) + (rand(0, 99) / 100),
                    'product_discount' => $price * ($discount / 100),
                ];
            }

            if (count($products) > 1000) {
                $client->insertAssocBulk('orders_stat_v3', $ordersInsert);
                $client->insertAssocBulk('orders_products_v3', $products);
                $this->info(sprintf('%d rows inserted', count($products)));
                $products = [];
                $ordersInsert = [];
            }
        }

        return 0;
    }
}
