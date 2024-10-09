<?php

namespace App\Console\Commands\Fake;

use App\ClickHouse\ClickHouseException;
use App\ClickHouse\Repositories\BaseMarketingExpenseRepository;
use App\Entities\Currency;
use App\Entities\Market;
use App\Entities\MarketingChannel;
use Carbon\Carbon;
use EntityManager;
use Exception;
use Faker\Factory;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class MarketingExpenseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fake:marketing-expense {--count=100} {--date=now} {--channel=} {--market=} {--currency=} {--I|infinity}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate fake data of Marketing expense';

    /**
     * Execute the console command.
     *
     * @param BaseMarketingExpenseRepository $repository
     * @return int
     * @throws ClickHouseException
     * @throws Exception
     */
    public function handle(BaseMarketingExpenseRepository $repository):int
    {
        $marketingChannels = $this->getData(MarketingChannel::class)->findAll();
        $currencies = $this->getData(Currency::class)->findAll();
        $markets = $this->getData(Market::class)->findAll();
        $faker = Factory::create();
        $count = $this->option('count');
        do {
            $repository->insert([
                'marketing_chanel_id' => $this->option('channel') ??  Arr::random($marketingChannels)->getId(),
                'market_id' => $this->option('market') ?? Arr::random($markets)->getRemoteId(),
                'currency_id' => $this->option('currency') ?? Arr::random($currencies)->getId(),
                'value' => $faker->randomFloat(2, 0, 20),
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
