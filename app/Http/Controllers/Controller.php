<?php

namespace App\Http\Controllers;

use App\ClickHouse\Services\OrderStatQueryFilter;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\Serializer\Serializer;

/**
 *  @OA\Info(
 *   title="Stream API",
 *   version="1.0.0",
 * )
 * @OA\SecurityScheme(
 *     name="authorization",
 *     in="header",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Bearer {JWT}",
 *     securityScheme="token",
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @var Serializer
     */
    private Serializer $serializer;

    public function __construct(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }


    public function dateGranularityOptions()
    {
        return $this->json(OrderStatQueryFilter::$dateGranularityOptions);
    }

    protected function json($data, array $context = [])
    {
        return $this->serializer->serialize($data, 'json', $context);
    }

    protected function getFilters(Request $request): array
    {
        return $request->has('filter') ? json_decode($request->get('filter'), 1) : [];
    }

    protected function getFiltersFromArray(Request $request): array
    {
        return $request->has('filter') ? $request->get('filter') : [];
    }
}
