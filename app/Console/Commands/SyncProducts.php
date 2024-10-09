<?php

namespace App\Console\Commands;

use App\Entities\ProductVariant;
use Doctrine\ORM\EntityManager;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class SyncProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync products';

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
     * @param Client $client
     * @param EntityManager $entityManager
     * @return int
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle(Client $client, EntityManager $entityManager)
    {
        $url = 'https://tights.no/pricefiles/facebook/';
        $filename = '/tmp/tights.no.csv';
        $repository = $entityManager->getRepository(ProductVariant::class);
        $client->request('GET', $url, ['sink' => $filename]);

        $file = fopen($filename, 'r+');
        $i = 0;

        while (($row = fgetcsv($file)) !== false) {
            if ($i++ === 0) {
                continue;
            }

            $row[0] = intval($row[0]);
            $productVariant = $repository->findOneBy(['remoteId' => $row[0]]);

            if ($productVariant) {
                $this->info(sprintf('Product #%d already exists, skipping', $row[0]));
                continue;
            }

            $price = explode('_', $row[2]);

            $productVariant = new ProductVariant();
            $productVariant->setRemoteId($row[0]);
            $productVariant->setName($row[1]);
            $productVariant->setLink($row[4]);
            $productVariant->setImageLink($row[6]);
            $productVariant->setWeight(rand(0, 9) + (rand(0, 99) / 100));
            $productVariant->setPrice((float)($price[0]));
            $productVariant->setCurrencyId(4);

            $entityManager->persist($productVariant);

            $this->info(sprintf('Product #%d saved.', $productVariant->getRemoteId()));
        }

        $entityManager->flush();

        return 0;
    }
}
