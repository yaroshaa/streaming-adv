<?php

namespace Modules\ProductStatistic\Http\Controllers;

use App\ClickHouse\ClickHouseException;
use Doctrine\ORM\EntityManager;
use Illuminate\Http\JsonResponse;
use Modules\ProductStatistic\Http\Requests\ProductStatisticRequest;
use Modules\ProductStatistic\Services\ProductStatisticService;
use App\Http\Controllers\Controller;
use Modules\ProductStatistic\Http\Resources\ProductStatisticResource;
use Symfony\Component\Serializer\Serializer;

class ListingProductStatisticController extends Controller
{
    /**
     * @var ProductStatisticService
     */
    private ProductStatisticService $service;

    /**
     * ListingProductStatisticController constructor.
     * @param Serializer $serializer
     * @param ProductStatisticService $service
     */
    public function __construct(Serializer $serializer, ProductStatisticService $service)
    {
        $this->service = $service;
        parent::__construct($serializer);
    }

    /**
     * @param ProductStatisticRequest $request
     * @param EntityManager $entityManager
     * @return JsonResponse
     * @throws ClickHouseException
     */
    public function __invoke(ProductStatisticRequest $request, EntityManager $entityManager) : JsonResponse
    {
        $filters = $this->getFiltersFromArray($request);

        return response()->json([
            'message'  => trans('api_messages.resource.paginated_list', ['items' => 'products']),
            'products' => ProductStatisticResource::collection($this->service->products($filters)),
        ]);
    }
}
