<?php

namespace Modules\Orders\Http\Resources;

use App\ClickHouse\ClickHouseException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Orders\ClickHouse\Repositories\OrderProductRepository;

class OrderResource extends JsonResource
{
    private OrderProductRepository $orderProductRepository;

    public function __construct($resource)
    {
        parent::__construct($resource);

        $this->orderProductRepository = resolve(OrderProductRepository::class);
    }

    /**
     * Transform the resource into an array
     *
     * @param Request $request
     * @return array
     * @throws ClickHouseException
     */
    public function toArray($request): array
    {
        $address = $this->getAddress();
        $currency = $this->getCurrency();
        $customer = $this->getCustomer();
        $market = $this->getMarket();
        $orderStatus = $this->getOrderStatus();
        $products = $this->getProductsByOrderId();

        $product_weight_sum = array_sum(array_column($products, 'product_weight'));
        $product_profit_sum = array_sum(array_column($products, 'product_profit'));
        $product_price_sum = array_sum(array_column($products, 'product_price'));


        return [
            'address' => $address->getAddress(),
            'address_id' => $address->getId(),
            'address_lat' => $address->getLat(),
            'address_lng' => $address->getLng(),
            'currency_id' => $currency->getId(),
            'customer_created_at' => $customer->getCreatedAt()->format('Y-m-d H:i:s'),
            'customer_id' => $customer->getId(),
            'customer_name' => $customer->getName(),
            'market_icon_link' => $market->getIconLink(),
            'market_id' => $market->getId(),
            'market_name' => $market->getName(),
            'order_id' => $this->getRemoteId(),
//            'status_color' => $orderStatus->getColor(),
            'status_id' => $orderStatus->getId(),
            'status_name' => $orderStatus->getName(),
            'updated_at' => $this->getUpdatedAt()->format('Y-m-d H:i:s'),
            'products' => $products,
            'product_price' => $product_price_sum,
            'product_profit' => $product_profit_sum,
            'product_weight' => $product_weight_sum,
        ];
    }

    /**
     * @return mixed
     */
    private function getProductsByOrderId()
    {
        return $this->orderProductRepository->findByOrderId($this->getRemoteId());
    }
}
