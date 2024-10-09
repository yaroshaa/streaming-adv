<?php

namespace App\Console\Commands\Fake;

use App\ClickHouse\ClickHouseException;
use App\ClickHouse\Repositories\BaseWarehouseStatisticRepository;
use App\Entities\Market;
use App\Entities\Warehouse;
use Carbon\Carbon;
use EntityManager;
use Exception;
use Faker\Factory;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class WarehouseStatisticCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fake:warehouse-statistic {--count=100} {--date=now} {--warehouse=} {--market=} {--I|infinity}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate fake data of warehouse statistic';

    /**
     * Execute the console command.
     *
     * @param BaseWarehouseStatisticRepository $repository
     * @return int
     * @throws ClickHouseException
     * @throws Exception
     */
    public function handle(BaseWarehouseStatisticRepository $repository): int
    {
        $warehouses = $this->getData(Warehouse::class)->findAll();
        $markets = $this->getData(Market::class)->findAll();
        $faker = Factory::create();
        $count = $this->option('count');
        do {
            $repository->insert([
                'warehouse_id' => $this->option('warehouse') ?? Arr::random($warehouses)->getId(),
                'in_packing' => $faker->numberBetween(10, 500),
                'open' => $faker->numberBetween(10, 500),
                'awaiting_stock' => $faker->numberBetween(10, 500),
                'station' => $faker->numberBetween(1, 15),
                'market_id' => $this->option('market') ?? Arr::random($markets)->getRemoteId(),
                'created_at' => Carbon::parse($this->option('date'))
            ]);
        } while (--$count || $this->option('infinity'));

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
