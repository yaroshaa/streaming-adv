<?php

namespace App\Http\Controllers;

use App\Entities\OrderStatus;
use Doctrine\ORM\EntityManager;

class OrderStatusController extends Controller
{
    public function getAll(EntityManager $entityManager)
    {
        return $this->json($entityManager->getRepository(OrderStatus::class)->findAll());
    }
}
