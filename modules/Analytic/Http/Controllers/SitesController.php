<?php

namespace Modules\Analytic\Http\Controllers;

use App\Http\Controllers\Controller;
use Doctrine\ORM\EntityManager;
use Modules\Analytic\Services\AnalyticService;

/**
 * Class SitesController
 * @package Modules\Analytic\Http\Controllers
 */
class SitesController extends Controller
{
    /**
     * @param EntityManager $em
     * @param AnalyticService $service
     * @return string
     */
    public function __invoke(EntityManager $em, AnalyticService $service)
    {
        $sites = $service->getSites($em);
        return $this->json($sites);
    }
}
