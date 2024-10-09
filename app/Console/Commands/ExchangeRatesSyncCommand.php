<?php

namespace App\Console\Commands;

use App\Entities\Currency;
use App\Entities\ExchangeRate;
use App\Repositories\ExchangeRatesRepository;
use DateTime;
use Doctrine\ORM\EntityManager;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class ExchangeRatesSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exchange:rates:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync exchange rates from https://exchangeratesapi.io/';

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
     * @param Client $client
     * @param EntityManager $entityManager
     * @return int
     * @throws \Doctrine\DBAL\Exception
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \ReflectionException
     */
    public function handle(Client $client, EntityManager $entityManager)
    {
        /** @var ExchangeRatesRepository $exchangeRateRepository */
        $exchangeRateRepository = $entityManager->getRepository(ExchangeRate::class);
        /** @var Currency[] $currencies */
        $currencies = $entityManager->getRepository(Currency::class)->findAll();

        $today = new DateTime('today');


        foreach ($currencies as $from) {
            $body = $client->get('https://api.exchangeratesapi.io/latest?access_key=' . Config::get('exchangerates.api_key') .'&base=' . $from->getCode())->getBody()->getContents();
            $result = json_decode($body, 1);

            if (! array_key_exists('rates', $result)) {
                $this->error(sprintf('API error for the currency %s', $from->getCode()));
            }

            if (isset($result['rates'])) {
                foreach ($currencies as $to) {
                    if ($to->getId() !== $from->getId() && !array_key_exists($to->getCode(), $result['rates'])) {
                        $this->error(sprintf('Exchange rate %s/%s not loaded', $from->getCode(), $to->getCode()));
                        continue;
                    }

                    $criteria = [
                        'from' => $from,
                        'to' => $to,
                        'createdAt' => $today
                    ];

                    /** @var ExchangeRate $exchangeRate */
                    $exchangeRate = $exchangeRateRepository->findOneByOrCreate($criteria, $criteria, false);
                    $exchangeRate->setRate($from->getId() === $to->getId()
                        ? floatval(1)
                        : $result['rates'][$to->getCode()]
                    );

                    $entityManager->persist($exchangeRate);

                    $this->info(sprintf('Exchange rate %s/%s updated to %f', $exchangeRate->getFrom()->getCode(), $exchangeRate->getTo()->getCode(), $exchangeRate->getRate()));
                }
            } elseif (isset($result['error'])) {
                $this->error(isset($result['error']['info']) ? $result['error']['info'] : json_encode($result));
            } else {
                $this->error('Unhandled error');
            }
        }

        $entityManager->flush();

        return 0;
    }
}
