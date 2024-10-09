<?php

namespace App\Http\Controllers;

use App\Entities\Currency;
use App\Entities\ExchangeRate;
use DateTime;
use Doctrine\ORM\EntityManager;

class CurrencyController extends Controller
{
    public function getAll(EntityManager $entityManager)
    {
        return $this->json($entityManager->getRepository(Currency::class)->findAll());
    }

    public function getExchangeRatesFor(EntityManager $entityManager, Currency $currency)
    {
        return $this->json($entityManager->getRepository(ExchangeRate::class)->findBy([
            'from' => $currency,
            'createdAt' => new DateTime('today')
        ]));
    }
}
