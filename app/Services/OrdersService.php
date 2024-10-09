<?php

namespace App\Services;

use App\ClickHouse\ClickhouseConfig;
use App\ClickHouse\ClickHouseException;
use App\ClickHouse\Models\Order;
use App\ClickHouse\Models\OrderProduct;
use App\ClickHouse\Models\OrderStatusHistory;
use App\Entities\Currency;
use App\Entities\Customer;
use App\Entities\Market;
use App\Entities\Order as EntityOrder;
use App\Entities\OrderStatus;
use App\Entities\ProductVariant;
use App\Entities\Warehouse;
use App\Repositories\CurrencyRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\MarketRepository;
use App\Repositories\OrderStatusRepository;
use App\Repositories\ProductVariantRepository;
use ClickHouseDB\Client;
use DateTime;
use Doctrine\DBAL\Exception\InvalidArgumentException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ObjectRepository;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Redis;
use Modules\Orders\Events\OrderUpdated;
use ReflectionException;
use Illuminate\Support\Facades\Validator;

class OrdersService
{
    /**
     * @var EntityManager
     */
    private EntityManager $entityManager;

    /**
     * @var AddressService
     */
    private AddressService $addressService;

    /**
     * @var MarketRepository|EntityRepository|ObjectRepository
     */
    private $marketRepository;

    /**
     * @var OrderStatusRepository|EntityRepository|ObjectRepository
     */
    private $orderStatusRepository;

    /**
     * @var CurrencyRepository|EntityRepository|ObjectRepository
     */
    private $currencyRepository;

    /**
     * @var ProductVariantRepository|EntityRepository|ObjectRepository
     */
    private $productVariantRepository;

    /**
     * @var CustomerRepository|EntityRepository|ObjectRepository
     */
    private $customerRepository;

    /**
     * @var EntityRepository|ObjectRepository
     */
    private $warehouseRepository;

    /**
     * @var Client
     */
    private Client $clickhouse;
    /**
     * @var ClickhouseConfig
     */
    private ClickhouseConfig $clickhouseConfig;


    public bool $uniqProducts;

    /**
     * OrdersService constructor.
     * @param EntityManager $entityManager
     * @param AddressService $addressService
     * @param Client $clickhouse
     * @param ClickhouseConfig $clickhouseConfig
     */
    public function __construct(EntityManager $entityManager, AddressService $addressService, Client $clickhouse, ClickhouseConfig $clickhouseConfig)
    {
        $this->entityManager = $entityManager;
        $this->addressService = $addressService;
        $this->clickhouse = $clickhouse;
        $this->marketRepository = $this->entityManager->getRepository(Market::class);
        $this->orderStatusRepository = $this->entityManager->getRepository(OrderStatus::class);
        $this->currencyRepository = $this->entityManager->getRepository(Currency::class);
        $this->productVariantRepository = $this->entityManager->getRepository(ProductVariant::class);
        $this->customerRepository = $this->entityManager->getRepository(Customer::class);
        $this->warehouseRepository = $this->entityManager->getRepository(Warehouse::class);
        $this->clickhouseConfig = $clickhouseConfig;
        $this->uniqProducts = false;
    }

    /**
     * @param array $data
     * @return EntityOrder|object|null
     * @throws ClickHouseException
     * @throws InvalidArgumentException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ReflectionException
     * @throws \Doctrine\DBAL\Exception
     * @throws GuzzleException
     * @throws Exception
     */
    public function save(array $data)
    {
        $this->validate($data);
        if (($order = $this->saveOrder($data)) !== null) {
            OrderUpdated::broadcast($order);
            return $order;
        }

        throw new Exception('Order cannot be saved');
    }

    /**
     * @param array $order
     * @return EntityOrder|object|null
     * @throws ClickHouseException
     * @throws InvalidArgumentException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ReflectionException
     * @throws \Doctrine\DBAL\Exception
     * @throws GuzzleException
     */
    private function saveOrder(array $order)
    {
        $orderId = $order['market']['remote_id'] . '_' . $order['order_id'];
        /** @var EntityOrder|null $existing */
        $orderEntity = $this->entityManager->getRepository(EntityOrder::class)->findOneBy(['remoteId' => $orderId]);

        $orderStatus = $this->orderStatusRepository->findOneByOrCreate(['remoteId' => $order['status']['remote_id']], [
            'remoteId' => $order['status']['remote_id'],
            'name' => $order['status']['name']
        ]);

        $orderCustomer = $this->customerRepository->findOneByOrCreate(['remoteId' => $order['customer']['remote_id']], [
            'remoteId' => $order['customer']['remote_id'],
            'name' => $order['customer']['name'],
            'createdAt' => new DateTime()
        ]);

        if (null !== $orderEntity) {
            $orderStatusHistory = [
                'order_id' => $orderEntity->getRemoteId(),
                'status_before' => $orderEntity->getOrderStatus()->getRemoteId(),
                'status_after' => $order['status']['remote_id'],
                'updated_at' => new DateTime(),
            ];

            $this->clickhouse->insertAssocBulk($this->clickhouseConfig->getTableName(OrderStatusHistory::class), [$orderStatusHistory]);


            $orderEntity->setOrderStatus($orderStatus);

            $this->entityManager->persist($orderEntity);
            $this->entityManager->flush();
        } else {

            $order = $this->modifyWithEntities($order);

            $orderEntity = new EntityOrder();
            $orderEntity->setRemoteId($orderId);
            $orderEntity->setWarehouse($order['warehouse']);
            $orderEntity->setOrderStatus($orderStatus);
            $orderEntity->setAddress($order['address']);
            $orderEntity->setCurrency($order['currency']);
            $orderEntity->setCustomer($orderCustomer);
            $orderEntity->setMarket($order['market']);
            $orderEntity->setUpdatedAt(new DateTime($order['updated_at']));
            $orderEntity->setPackingCost($order['packing_cost']);
            $this->entityManager->persist($orderEntity);
            $this->entityManager->flush();

            $orderStatusHistory = [
                'order_id' => $orderEntity->getRemoteId(),
                'status_before' => 0,
                'status_after' => $orderEntity->getOrderStatus()->getRemoteId(),
                'updated_at' => new DateTime(),
            ];

            $this->clickhouse->insertAssocBulk($this->clickhouseConfig->getTableName(OrderStatusHistory::class), [$orderStatusHistory]);

            $products = array_map(fn($product) => [
                'product_variant_id' => $this->uniqProducts
                    ? $product['remote_id']
                    : $orderEntity->getMarket()->getRemoteId() . '_' . $product['remote_id'],
                'product_weight' => $product['weight'],
                'product_price' => $product['price'],
                'product_profit' => $product['profit'],
                'product_discount' => $product['discount'],
                'product_qty' => $product['qty'],
                'order_id' => $orderEntity->getRemoteId(),
                'updated_at' => $orderEntity->getUpdatedAt(),
                'market_id' => $orderEntity->getMarket()->getRemoteId(),
                'currency_id' => $orderEntity->getCurrency()->getId(),
                'customer_id' => $orderEntity->getCustomer()->getRemoteId()
            ], $order['products']);

            $this->clickhouse->insertAssocBulk($this->clickhouseConfig->getTableName(OrderProduct::class), $products);
        }

        return $orderEntity;
    }

    /**
     * @param int $orderId
     * @return array|null
     * @throws ClickHouseException
     */
    private function findExisting(int $orderId): ?array
    {
        return $this->findInCache($orderId) ?: $this->clickhouse->select("SELECT * from {$this->clickhouseConfig->getTableName(Order::class)} WHERE order_id = {orderId} and sign > 0 ORDER BY updated_at desc", [
            'orderId' => $orderId,
        ])->fetchRow();
    }

    /**
     * @param array $order
     */
    private function saveInCache(array $order): void
    {
        Redis::connection()->set(sprintf('order_cache_%d', $order['order_id']), json_encode($order));
    }

    /**
     * @param int $orderId
     * @return array
     */
    private function findInCache(int $orderId): array
    {
        $key = sprintf('order_cache_%d', $orderId);

        return Redis::connection()->get($key) ? json_decode(Redis::connection()->get($key), 1) : [];
    }

    /**
     * @param array $order
     * @return array
     * @throws InvalidArgumentException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ReflectionException
     * @throws \Doctrine\DBAL\Exception
     * @throws GuzzleException
     */
    private function modifyWithEntities(array $order): array
    {
        $order['currency'] = $this->currencyRepository->findOneByOrCreate(['code' => $order['currency']['code']], [
            'code' => $order['currency']['code'],
            'name' => $order['currency']['code'],
        ]);

        $order['customer'] = $this->customerRepository->findOneByOrCreate(['remoteId' => $order['customer']['remote_id']], [
            'remoteId' => $order['customer']['remote_id'],
            'name' => $order['customer']['name'],
            'createdAt' => new DateTime()
        ]);

        $marketData = [
            'remoteId' => $order['market']['remote_id'],
            'name' => $order['market']['name']
        ];
        if(array_key_exists('iconLink', $order['market'])) {
            $marketData['iconLink'] = $order['market']['iconLink'];
        }
        if(array_key_exists('color', $order['market'])) {
            $marketData['color'] = $order['market']['color'];
        }

        $order['market'] = $this->marketRepository->findOneByOrCreate(['remoteId' => $order['market']['remote_id']], $marketData);

        $order['status'] = $this->orderStatusRepository->findOneByOrCreate(['remoteId' => $order['status']['remote_id']], [
            'remoteId' => $order['status']['remote_id'],
            'name' => $order['status']['name']
        ]);

        $order['warehouse'] = $this->warehouseRepository->findOneByOrCreate(['name' => $order['warehouse']['name']], [
            'name' => $order['warehouse']['name']
        ]);

        $order['address'] = $this->addressService->getAddress($order['address']);

        $defaultImgLink = 'https://www.tights.no/wp-content/uploads/sites/7/2021/03/Monster-Sukkerfrie-Pa%CC%8Askeegg-150g1-1200x1500.jpg';

        foreach ($order['products'] as $product) {

            $remoteId = $this->uniqProducts
                ? $product['remote_id']
                : $order['market']->getRemoteId() . '_' . $product['remote_id'];

            $this->productVariantRepository->findOneByOrCreate(

                ['remoteId' => $remoteId], [
                    'remoteId' => $remoteId,
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'currencyId' => $product['currency_id'] ?? (int) config('currency.default.id'),
                    'link' => $product['link'],
                    'imageLink' => $product['image_link'] ?? $product['image'],
                    'weight'=> $product['weight']
                ]
            );
        }

        return $order;
    }

    /**
     * @param array $rowOrder
     * @throws Exception
     */
    private function validate(array $rowOrder): void
    {
        $validation = [
            'order_id' => 'required|integer',
            'address' => 'required|string',
            'updated_at' => 'required|date',
            'currency' => 'required|array',
            'currency.code' => 'required|string|size:3',
            'customer' => 'required|array',
            'customer.remote_id' => 'required|integer',
            'customer.name' => 'required|string',
            'market' => 'required|array',
            'market.remote_id' => 'required|integer',
            'market.name' => 'required|string',
            'status' => 'required|array',
            'status.remote_id' => 'required|integer',
            'status.name' => 'required|string',
            'warehouse' => 'required|array',
            'warehouse.name' => 'required|string',
            'products' => 'array|min:1',
            'products.*.remote_id' => 'required|integer',
            'products.*.name' => 'required|string',
            'products.*.weight' => 'required|numeric',
            'products.*.link' => 'required|url',
            'products.*.image_link' => 'required', // 'required|url', \u030 crashed
            'products.*.price' => 'required|numeric',
            'products.*.profit' => 'required|numeric',
            'products.*.discount' => 'required|numeric',
            'products.*.qty' => 'required|numeric',
        ];

        $validator = Validator::make($rowOrder, $validation);

        if ($validator->fails()) {
            $errorMessage = implode('; ', $validator->errors()->all());
            throw new Exception('Validation failed. ' . $errorMessage . ' - ' . json_encode($rowOrder));
        }
    }
}
