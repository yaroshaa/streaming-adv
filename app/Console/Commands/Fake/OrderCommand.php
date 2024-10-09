<?php

namespace App\Console\Commands\Fake;

use App\Entities\Currency;
use App\Entities\Market;
use App\Entities\OrderStatus;
use App\Entities\ProductVariant;
use App\Entities\Warehouse;
use App\Repositories\ChildEntityRepository;
use App\Services\OrdersService;
use Carbon\Carbon;
use Doctrine\ORM\QueryBuilder;
use EntityManager;
use Exception;
use Faker\Factory;
use Faker\Generator;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class OrderCommand extends Command
{
    /**
     * @var string[]
     */
    protected array $addresses = [];
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fake:order
                                {--count=100}
                                {--date=now}
                                {--market=}
                                {--status=}
                                {--warehouse-name=}
                                {--currency-code=}
                                {--product-ids=}
                                {--product-count-min=1}
                                {--product-count-max=3}
                                {--I|infinity}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate fake data of order';

    /** @var Market[] array */
    private array $markets = [];

    /** @var OrderStatus[] array */
    private array $statuses = [];

    /** @var ProductVariant[] array */
    private array $productVariants = [];

    private Generator $faker;

    /**
     * Execute the console command.
     *
     * @param OrdersService $ordersService
     * @return int
     * @throws Exception
     * @throws GuzzleException
     */
    public function handle(OrdersService $ordersService): int
    {
        $this->init();

        $currencies = $this->getRepository(Currency::class)->findAll();
        $warehouses = $this->getRepository(Warehouse::class)->findAll();

        $count = $this->option('count');

        do {
            $status = Arr::random($this->statuses);
            $market = Arr::random($this->markets);
            $ordersService->save([
                'order_id' => hexdec(substr($this->faker->uuid, 0, 8)),
                'address' => Arr::random($this->addresses),
                'currency' => [
                    'code' => $this->option('currency-code') ?? Arr::random($currencies)->getCode(),
                ],
                'customer' => [
                    'id' => $this->faker->randomNumber(),
                    'name' => $this->faker->name
                ],
                'market' => [
                    'id' => $market->getRemoteId(),
                    'name' => $market->getName(),
                ],
                'products' => $this->getProducts(),
                'status' => [
                    'id' => $status->getRemoteId(),
                    'name' => $status->getName(),
                ],
                'warehouse' => [
                    'name' => $this->option('warehouse-name') ?? Arr::random($warehouses)->getName(),
                ],
                'packing_cost' => $this->faker->randomFloat(2, 0, 20),
                'updated_at' => Carbon::parse($this->option('date'))
            ]);
        } while (--$count || $this->option('infinity'));

        return 0;
    }

    public function init()
    {
        $this->faker = Factory::create();
        $this->addresses = array_column(json_decode(Storage::disk('local')->get('fake/addresses.json')), 'address');

        $statusQuery = $this->getQueryBuilder()->select('s')->from(OrderStatus::class, 's');
        if ($this->option('status')) {
            $statusQuery->where('s.remoteId = :id')->setParameter('id', $this->option('status'));
        }

        $this->statuses = $statusQuery->getQuery()->getResult();

        $marketQuery = $this->getQueryBuilder()->select('m')->from(Market::class, 'm');
        if ($this->option('market')) {
            $marketQuery->where('m.remoteId = :id')->setParameter('id', $this->option('market'));
        }

        $this->markets = $marketQuery->getQuery()->getResult();

        $productQuery = $this->getQueryBuilder();
        $productQuery->select('p');
        $productQuery->from(ProductVariant::class, 'p');

        if ($this->option('product-ids')) {
            $productQuery->where('p.id  IN  (:ids)');
            $productQuery->setParameter(':ids', explode(',', $this->option('product-ids')));
        }

        $this->productVariants = $productQuery->getQuery()->getResult();
    }

    /**
     * @return QueryBuilder
     */
    private function getQueryBuilder(): QueryBuilder
    {
        return EntityManager::createQueryBuilder();
    }

    /**
     * @throws Exception
     */
    private function getRepository($entity): ChildEntityRepository
    {
        if (empty($repository = EntityManager::getRepository($entity))) {
            throw new Exception(sprintf('Empty data of %s entity', $entity));
        }

        return $repository;
    }

    /**
     * @throws Exception
     */
    private function getProducts(): array
    {
        /** @var ProductVariant[] $products */
        $products = Arr::random($this->productVariants, rand(
            $this->option('product-count-min'),
            $this->option('product-count-max')
        ));

        $generatedProducts = [];
        foreach ($products as $product) {
            $generatedProducts[] = $this->generateProduct($product);
        }

        return $generatedProducts;
    }

    private function generateProduct(ProductVariant $productVariant): array
    {
        $price = $this->faker->randomFloat(2, 5, 999);
        $discount = $this->faker->randomFloat(2, 5, $price);
        $profit = $price - $discount;

        $discountValue = number_format($discount, 2, '.', '');
        $price = number_format($price, 2, '.', '');
        $profit = number_format($profit, 2, '.', '');

        return [
            'id' => $productVariant->getId(),
            'remote_id' => $productVariant->getRemoteId(),
            'discount' => $discountValue,
            'image_link' => $productVariant->getImageLink(),
            'link' => $productVariant->getLink(),
            'name' => $productVariant->getName(),
            'price' => $price,
            'profit' => $profit,
            'qty' => $this->faker->numberBetween(1, 10),
            'weight' => $this->faker->randomFloat(2, 0, 20)
        ];
    }
}
