<?php

namespace App\Console\Commands\Fake;

use App\ClickHouse\ClickHouseException;
use App\ClickHouse\Models\CartAction;
use App\ClickHouse\Repositories\BaseCartActionRepository;
use App\Entities\Market;
use Carbon\Carbon;
use EntityManager;
use Exception;
use Faker\Factory;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class CartActionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fake:cart-action {--count=100} {--date=now} {--market=} {--I|infinity}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate fake data of cart action';

    /**
     * Execute the console command.
     *
     * @param BaseCartActionRepository $repository
     * @return int
     * @throws ClickHouseException
     */
    public function handle(BaseCartActionRepository $repository):int
    {
        $markets = $this->getData(Market::class)->findAll();
        $faker = Factory::create();
        $count = $this->option('count');
        do {
            $repository->insert([
                'market_id' => $this->option('market') ?? Arr::random($markets)->getRemoteId(),
                'status' => Arr::random([CartAction::STATUS_ADD_TO_CART, CartAction::STATUS_REMOVE_FROM_CART]),
                'ip' => $faker->ipv4,
                'created_at' => Carbon::parse($this->option('date'))
            ]);
        } while(--$count || $this->option('infinity'));

        return 0;
    }

    /**
     * @throws Exception
     */
    private function getData($entity)
    {
        if (empty($data = EntityManager::getRepository($entity))) {
            throw new Exception(sprintf('Empty data of %s entity', $entity));
        }

        return $data;
    }
}
