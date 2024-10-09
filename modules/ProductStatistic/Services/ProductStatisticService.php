<?php

namespace Modules\ProductStatistic\Services;

use App\ClickHouse\ClickHouseException;
use Modules\ProductStatistic\ClickHouse\Repositories\ProductStatisticRepository;
use Doctrine\ORM\EntityManager;
use App\Entities\ExchangeRate;
use App\Entities\ProductVariant;
use Illuminate\Support\Collection;

class ProductStatisticService
{
    /**
     * @var EntityManager
     */
    private EntityManager $entityManager;
    private ProductStatisticRepository $repository;

    /**
     * ProductStatisticRepository constructor.
     * @param EntityManager $entityManager
     * @param ProductStatisticRepository $repository
     */
    public function __construct(EntityManager $entityManager, ProductStatisticRepository $repository)
    {
        $this->entityManager = $entityManager;
        $this->repository = $repository;
    }

    /**
     * @param array $filters
     * @return Collection
     * @throws ClickHouseException
     */
    public function products(array $filters): Collection
    {
        $products = $this->getProducts($filters);
        $productsWithOrders = collect($this->getProductVariantIdsWithOrders($filters))
            ->map(fn($item) => $item['product_variant_id'])
            ->toArray();

        return collect($products)
            ->map(fn($item) =>
                $item + ['no_orders' => (int) !in_array($item['remoteId'], $productsWithOrders, true)]);
    }

    /**
     * @param array $filters
     * @return array
     */
    private function getProducts(array $filters) : array
    {
        $currencyId = array_key_exists('currency', $filters) && $filters['currency'] !== null
            ? $filters['currency']['id']
            : 3;

        $query = $this->entityManager->createQueryBuilder();
        $query->select(['p.id', 'p.name', 'p.remoteId', 'p.price * e.rate as price','p.weight']);
        $query->from(ProductVariant::class, 'p');
        $query->leftJoin(ExchangeRate::class, 'e',  'WITH', 'p.currencyId = e.from');
        $query->where('e.to = :filterCurrencyId');
        $query->andWhere('e.createdAt > :yesterday');
        $query->orderBy('p.name','ASC');
        $query->setParameter('filterCurrencyId', $currencyId);
        $query->setParameter(':yesterday', now()->subDay()->addMinutes(5));

        if(array_key_exists('search', $filters) && $filters['search'] !== '' && $filters['search'] !== null){
            $query->andWhere("MATCH_AGAINST (p.name) AGAINST ( :filterSearch boolean ) > 0");
            $query->setParameter('filterSearch',   mb_strtolower($filters['search']).'*');
        }

        $query->orderBy('p.id', 'ASC');

        return $query->getQuery()->getResult();
    }

    /**
     * @throws ClickHouseException
     */
    private function getProductVariantIdsWithOrders($filters): array
    {
        return $this->repository->getProductsIdsWithOrders($filters);
    }
}
