<?php


namespace App\ClickHouse\DataTransformers;


use App\ClickHouse\ClickHouseException;
use App\ClickHouse\ClickHouseModel;
use App\ClickHouse\ModelCollection;
use App\ClickHouse\ModelTransformer;
use App\ClickHouse\Models\Order;
use App\ClickHouse\Models\OrderProduct;
use App\ClickHouse\ModelTransformerInterface;
use Generator;
use ReflectionException;

class OrderTransformer extends ModelTransformer implements ModelTransformerInterface
{
    /**
     * @param Order|ClickHouseModel $model
     * @return Generator
     * @throws ClickHouseException
     * @throws ReflectionException
     */
    public function oldfromObjectToArray(ClickHouseModel $model): Generator
    {
        $orderArr = iterator_to_array(parent::fromObjectToArray($model));
        $orderArr = array_shift($orderArr);

        /** @var OrderProduct $orderProduct */
        foreach ($model->getOrderProducts() as $orderProduct) {
            $productArr = iterator_to_array($this->bag->getTransformer(OrderProduct::class)->fromObjectToArray($orderProduct));
            yield array_merge($orderArr, array_shift($productArr));
        }
    }

    /**
     * @param array $data
     * @return Order[]|ModelCollection
     * @throws ClickHouseException
     */
    public function fromArrayToObjects(array $data): ModelCollection
    {
        $resultSet = new ModelCollection([]);

        foreach ($data as $row) {
            /** @var Order $order */
            if (!$order = $resultSet->findBy('id', $row['order_id'])) {
                $order = parent::fromArrayToObjects([$row])->first();
                $resultSet->add($order, 'id');
            }

            /** @var OrderProduct $product */
            foreach ($this->bag->getTransformer(OrderProduct::class)->fromArrayToObjects([$row]) as $product) {
                $order->addOrderProduct($product);
            }
        }

        return $resultSet;
    }
}
