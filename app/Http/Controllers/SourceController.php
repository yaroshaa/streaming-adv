<?php

namespace App\Http\Controllers;

use App\Entities\Source;
use Doctrine\ORM\EntityManager;

class SourceController extends Controller
{
    public function getAll(EntityManager $entityManager)
    {
        return $this->json($entityManager->getRepository(Source::class)->findBy([], ['remoteId' => 'ASC']));
    }
}
