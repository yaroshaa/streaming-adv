<?php

namespace App\Http\Controllers;

use App\Entities\Market;
use Doctrine\ORM\EntityManager;

class MarketController extends Controller
{
    public function getAll(EntityManager $entityManager)
    {
        return $this->json($entityManager->getRepository(Market::class)->findBy([], ['remoteId' => 'ASC']));
    }
}
