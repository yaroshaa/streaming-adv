<?php

namespace App\Console\Commands\Fake;

use App\ClickHouse\ClickHouseException;
use App\ClickHouse\Models\ActiveUser;
use App\ClickHouse\Repositories\BaseActiveUserRepository;
use App\Entities\Market;
use Carbon\Carbon;
use EntityManager;
use Exception;
use Faker\Factory;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class ActiveUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fake:active-user {--count=100} {--date=now} {--market=} {--I|infinity}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate fake data of active user';

    /**
     * Execute the console command.
     *
     * @param BaseActiveUserRepository $repository
     * @return int
     * @throws ClickHouseException
     */
    public function handle(BaseActiveUserRepository $repository):int
    {
        $markets = $this->getData(Market::class)->findAll();
        $faker = Factory::create();
        $count = $this->option('count');
        do {
            $repository->insert([
                'market_id' => $this->option('market') ?? Arr::random($markets)->getRemoteId(),
                'status' => Arr::random([ActiveUser::STATUS_ACTIVE, ActiveUser::STATUS_NOT_ACTIVE]),
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
