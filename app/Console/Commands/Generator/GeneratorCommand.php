<?php

namespace App\Console\Commands\Generator;

use App\Entities\Currency;
use App\Entities\ExchangeRate;
use App\Entities\ProductVariant;
use Modules\ProductStatistic\Http\Resources\ProductVariantResource;
use App\Repositories\ExchangeRatesRepository;
use App\Services\OrdersService;
use Doctrine\ORM\EntityManager;
use Illuminate\Console\Command;

class GeneratorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generator {--count-orders=} {--currency=} {--I|infinity} {--sleep=?}
    {--H|foreach-hour} {--start-date=?} {--end-date=?} {--use-products}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Data generator';

    private OrdersService $orderService;
    private EntityManager $entityManager;

    private array $products;
    private array $addresses;
    private array $names;
    private array $markets;
    private array $currencies;
    private array $statuses;
    private array $warehouses;

    private $currency = null;

    /**
     * @var bool false
     */
    private bool $useProductsTable;

    /**
     * @param EntityManager $entityManager
     * @param OrdersService $ordersService
     * @return int
     * @throws \App\ClickHouse\ClickHouseException
     * @throws \Doctrine\DBAL\Exception
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \ReflectionException
     */
    public function handle(EntityManager $entityManager, OrdersService $ordersService)
    {
        $this->orderService = $ordersService;
        $this->entityManager = $entityManager;

        $this->products = json_decode(file_get_contents('app/Console/Commands/Generator/products.json'), true);
        $this->addresses = json_decode(file_get_contents('app/Console/Commands/Generator/addresses.json'), true);
        $this->names = json_decode(file_get_contents('app/Console/Commands/Generator/names.json'), true);
        $this->markets = json_decode(file_get_contents('app/Console/Commands/Generator/markets.json'), true);
        $this->currencies = json_decode(file_get_contents('app/Console/Commands/Generator/currencies.json'), true);
        $this->statuses = json_decode(file_get_contents('app/Console/Commands/Generator/statuses.json'), true);

        $this->useProductsTable = false;
        $this->warehouses = json_decode(file_get_contents('app/Console/Commands/Generator/warehouses.json'), true);

        $countOrders = $this->option('count-orders');
        $sleep = (int) $this->option('sleep');
        $infinity = $this->option('infinity');
        $foreachHour = $this->option('foreach-hour');
        $startDate = $this->option('start-date');
        $endDate = $this->option('end-date');
        $this->currency = $this->option('currency');
        $useProducts = (bool)$this->option('use-products');

        $waitInSeconds = $sleep > 0 ? $sleep : 1;

        if( $useProducts ) {
            $this->useProductsTable = true;
            $this->orderService->uniqProducts = true;

            $query = $this->entityManager->createQueryBuilder();
            $query->select('p' );
            $query->from(ProductVariant::class, 'p');
            $query->where('p.id  IN  (:ids)');
            $query->setParameter(':ids', [1,2,3]);
            $productsData = $query->getQuery()->getResult();

            $this->products = collect(ProductVariantResource::collection($productsData))->toArray();
        }

        if (!$this->checkOptions()) {
            return 0;
        }

        $startDate = new \DateTime($startDate);
        $endDate = new \DateTime($endDate);

        if ($infinity) {
            while (true) {
                for ($i = 0; $i < $countOrders; $i++) {
                    $this->generateOrders($startDate, $endDate, $foreachHour);
                }

                $this->info('wait ' . $waitInSeconds . ' seconds...');
                sleep($waitInSeconds);
            }
        } else {
            for ($i = 0; $i < $countOrders; $i++) {
                $this->generateOrders($startDate, $endDate, $foreachHour);
            }
        }

        return 0;
    }

    /**
     * Check options.
     * @return bool
     */
    public function checkOptions()
    {
        $countOrders = $this->option('count-orders');
        $sleep = (int) $this->option('sleep');
        $infinity = $this->option('infinity');
        $startDate = $this->option('start-date');
        $endDate = $this->option('end-date');
        $this->currency = $this->option('currency');

        if (!$countOrders) {
            $this->error('--count-orders is required');
            return false;
        }

        if (!$startDate) {
            $this->error('--start-date is required');
            return false;
        }

        if (!$endDate) {
            $this->error('--end-date is required');
            return false;
        }

        if ($infinity) {
            if (!$sleep) {
                $this->error('--sleep is required option with --infinity');
                return false;
            }
        }

        return true;
    }

    /**
     * Generate orders.
     * @param \DateTime|null $startDate
     * @param \DateTime|null $endDate
     * @param null $foreachHour
     * @throws \App\ClickHouse\ClickHouseException
     * @throws \Doctrine\DBAL\Exception
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \ReflectionException
     */
    private function generateOrders(\DateTime $startDate = null, \DateTime $endDate = null, $foreachHour = null): void
    {
        if ($startDate && $endDate) {



            $interval = \DateInterval::createFromDateString('1 day');
            $period = new \DatePeriod($startDate, $interval, $endDate);

            /**
             * @var \DateTime $updatedAt
             */
            foreach ($period as $date) {

                $this->generateExchangeRates($date->format('Y-m-d'));

                if ($foreachHour) {
                    $this->info('Generate orders for ' . $date->format('Y-m-d H:i:s'));
                    $this->info('Generate orders foreach hour for  ' . $date->format('Y-m-d') . ' date');
                    $hourPeriod = $this->getHoursPeriod($date);

                    foreach ($hourPeriod as $updatedAt) {
                        $this->info('Generate single order  ' . $updatedAt->format('Y-m-d H:i:s'));
                        $indexes = $this->randIndexes();

                        $orderData = $this->generateOrder($indexes, $updatedAt);
                        $this->orderService->save($orderData);
                    }

                } else {
                    $indexes = $this->randIndexes();
                    $this->info('Generate single orders...');

                    $hour = rand(0, 23);
                    $minute = rand(0, 59);
                    $date->add(new \DateInterval('PT' . $hour . 'H'));
                    $date->add(new \DateInterval('PT' . $minute . 'M'));
                    $this->info('Generate orders for ' . $date->format('Y-m-d H:i:s'));

                    $orderData = $this->generateOrder($indexes, $date);
                    $this->orderService->save($orderData);
                }
            }
        } else {

            if ($foreachHour) {

                $this->info('Generate orders foreach hour');

                $hourPeriod = $this->getHoursPeriod(new \DateTime());
                foreach ($hourPeriod as $updatedAt) {
                    $this->info('Generate single order  ' . $updatedAt->format('Y-m-d H:i:s'));
                    $indexes = $this->randIndexes();

                    $orderData = $this->generateOrder($indexes, $updatedAt);
                    $this->orderService->save($orderData);
                }

            } else {
                $indexes = $this->randIndexes();

                $orderData = $this->generateOrder($indexes);
                $this->orderService->save($orderData);
            }
        }
    }

    /**
     * Generate order.
     * @param array $indexes
     * @param \DateTime|null $updatedAt
     * @return array
     * @throws \Exception
     */
    private function generateOrder(array $indexes, \DateTime $updatedAt = null): array
    {

        $address = $this->addresses[$indexes['address']]['address'];
        $name = $this->names[$indexes['name']]['name'];
        $currency = $this->currency ? ['code' => $this->currency] : $this->currencies[$indexes['currency']];
        $market = $this->markets[$indexes['market']];
        $status = $this->statuses[$indexes['status']];
        $warehouse = $this->warehouses[$indexes['warehouse']];

        if (!$updatedAt) {
            $updatedAt = new \DateTime();
        }

        $countProductsInOrder = rand(1, 5);
        $packingCost = rand(1, 10) / 100 + rand(1, 20);
        $products = [];

        for ($i = 0; $i < $countProductsInOrder; $i++) {
            $products[] = $this->generateProduct($indexes);
        }

        return [
            'order_id' => hexdec(substr(uniqid(),0,8)),
            'address' => $address,
            'currency' => $currency,
            'customer' => [
                'id' => hexdec(uniqid()),
                'name' => $name,
                'remote_id' => hexdec(uniqid())
            ],
            'market' => $market,
            'products' => $products,
            'status' => $status,
            'warehouse' => $warehouse,
            'packing_cost' => $packingCost,
            'updated_at' => $updatedAt->format('Y-m-d\TH:i')
        ];
    }

    /**
     * Generate product.
     * @param array $indexes
     * @return array
     */
    private function generateProduct(array $indexes) : array
    {

        $product = $this->products[$indexes['product']];

        $price = (rand(1, 100) / 100) + rand(1, 999);
        $discountPercent = rand(0, 50);
        $discountValue = ($price / 100) * $discountPercent;
        $profit = $price - $discountValue;

        $discountValue = number_format($discountValue,2, '.', '' );
        $price = number_format($price,2, '.', '' );
        $profit = number_format($profit,2, '.', '' );

        return [
            'id'         => $product['id'] ?? hexdec(uniqid()),
            'remote_id'  =>  $product['remote_id'] ?? (string) hexdec(uniqid()),
            'discount'   => $discountValue,
            'image_link' => $product['image_link'] ?? $product['image'],
            'link' => $product['link'],
            'name' => $product['name'],
            'price' => $price,
            'profit' => $profit,
            'qty' => rand(1, 10),
            'weight' => (rand(1, 100) / 100) + rand(0, 20)
        ];
    }

    /**
     * Get hours period.
     * @param $date
     * @return \DatePeriod
     */
    private function getHoursPeriod($date)
    {
        $startDateTime = clone $date;
        $endDateTime = clone $date;

        $startDateTime->setTime(0, 0, 0);
        $endDateTime->setTime(23, 59, 59);

        $hourInterval = \DateInterval::createFromDateString('1 hour');
        return new \DatePeriod($startDateTime, $hourInterval, $endDateTime);
    }

    /**
     * Generate exchange rates.
     * @param $date
     * @throws \Doctrine\DBAL\Exception
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \ReflectionException
     */
    private function generateExchangeRates($date)
    {
        $currencies = $this->entityManager->getRepository(Currency::class)->findAll();
        $today = new \DateTime($date);

        $exchangeRatesSrc = [];

        foreach ($currencies as $fromCurrency) {

            foreach ($currencies as $toCurrency) {
//                if ($fromCurrency->getId() !== $toCurrency->getId()) {
                    $exchangeRatesSrc[] = [
                        'from' => $fromCurrency,
                        'to' => $toCurrency,
                        'rate' =>($fromCurrency->getId() !== $toCurrency->getId())
                            ? (rand(1, 5) / 10) + rand(1, 3)
                            : 1,
                    ];
//                }
            }
        }


        foreach ($exchangeRatesSrc as $exchangeRateSrcItem) {

            /** @var ExchangeRatesRepository $exchangeRateRepository */
            $exchangeRateRepository = $this->entityManager->getRepository(ExchangeRate::class);
            $criteria = [
                'from' => $exchangeRateSrcItem['from'],
                'to' => $exchangeRateSrcItem['to'],
                'createdAt' => $today
            ];

            /** @var ExchangeRate $exchangeRate */
            $exchangeRate = $exchangeRateRepository->findOneByOrCreate($criteria, $criteria, false);
            $exchangeRate->setRate($exchangeRateSrcItem['rate']);
            $this->entityManager->persist($exchangeRate);
        }
    }

    /**
     * Rand indexes.
     * @return array
     */
    private function randIndexes() : array
    {
        return [
            'product' => rand(0, count($this->products) - 1),
            'address' => rand(0, count($this->addresses) - 1),
            'name' => rand(0, count($this->names) - 1),
            'market' => rand(0, count($this->markets) - 1),
            'currency' => rand(0, count($this->currencies) - 1),
            'status' => rand(0, count($this->statuses) - 1),
            'warehouse' => rand(0, count($this->warehouses) - 1)
        ];
    }
}
