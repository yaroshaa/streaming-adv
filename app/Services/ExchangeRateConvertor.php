<?php


namespace App\Services;


use App\Entities\Currency;
use App\Entities\ExchangeRate;
use App\Entities\OrderProductVariant;
use DateTime;
use Doctrine\ORM\EntityManager;
use Illuminate\Log\Logger;

class ExchangeRateConvertor
{
    /**
     * @var EntityManager
     */
    private EntityManager $entityManager;

    /**
     * @var array
     */
    private array $exchangeRates = [];

    /**
     * @var Logger
     */
    private Logger $logger;

    /**
     * ExchangeRateConvertor constructor.
     * @param EntityManager $entityManager
     * @param Logger $logger
     */
    public function __construct(EntityManager $entityManager, Logger $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }


    /**
     * @param OrderProductVariant[] $orders
     * @param Currency $to
     * @return OrderProductVariant[]
     */
    public function convert(array $orders, Currency $to): array
    {
        foreach ($orders as $order) {
            $order->setPrice($order->getPrice() * $this->getExchangeRate($order->getOrder()->getCurrency(), $to)->getRate());
            $order->setProfit($order->getProfit() * $this->getExchangeRate($order->getOrder()->getCurrency(), $to)->getRate());
        }

        return $orders;
    }

    /**
     * @param Currency $from
     * @param Currency $to
     * @return ExchangeRate
     */
    private function getExchangeRate(Currency $from, Currency $to): ExchangeRate
    {
        $key = $from->getCode() . $to->getCode();
        $dateTime = new DateTime('today');

        if (!array_key_exists($key, $this->exchangeRates)) {
            $rate = $this->entityManager->getRepository(ExchangeRate::class)->findOneBy([
                'from' => $from,
                'to' => $to,
                'createdAt' => $dateTime
            ]);

            if (!$rate) {
                $this->logger->error(sprintf('Please update exchange rate for %s/%s', $from->getCode(), $to->getCode()));

                $rate = new ExchangeRate();
                $rate->setFrom($from);
                $rate->setTo($to);
                $rate->setCreatedAt($dateTime);
                $rate->setRate(floatval(1));
            }

            $this->exchangeRates[$key] = $rate;
        }

        return $this->exchangeRates[$key];
    }
}
