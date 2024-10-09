<?php

namespace Modules\Feedbacks\Http\Controllers;

use Carbon\Carbon;
use Modules\Feedbacks\ClickHouse\Repositories\FeedbackRepository;
use Modules\Feedbacks\ClickHouse\Services\FeedbackService;
use Modules\Feedbacks\Http\Requests\FeedbackRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FeedbacksController
{
    /**
     * @OA\Post(
     *     path="/api/feedbacks",
     *     summary="Create feedback",
     *     description="Create new feedback",
     *     operationId="addFeedback",
     *     tags={"Feedbacks"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Pass user credentials",
     *          @OA\JsonContent(
     *              @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/feedback"))
     *          ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="ok"
     *     ),
     *      @OA\Response(
     *          response=500,
     *          description="Wrong credentials response",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Invalid validation")
     *          )
     *      )
     * )
     * @param FeedbackRequest $request
     * @param FeedbackService $feedbacksService
     * @return JsonResponse
     */
    public function insert(FeedbackRequest $request, FeedbackService $feedbacksService): JsonResponse
    {
        try {
            foreach ($request->input('data') as $feedbackArray) {
                $feedbacksService->saveAndBroadcast($feedbackArray);
            }
        } catch (Exception $exception) {
            Log::critical('Feedback API exception: ' . $exception->getMessage());

            return response()->json(['error' => $exception->getMessage()], 500);
        }

        return response()->json(['result' => 'ok']);
    }

    /**
     * @OA\Get(
     *     path="/api/feedbacks",
     *     security={{"oauth2": {"*"}}},
     *     operationId="getFeedbacks",
     *     tags={"Feedbacks"},
     *     summary="Get list of feedbacks",
     *     description="Returns list of feedbacks",
     *     security={ {"oauth2": {} }},
     *     @OA\Parameter(
     *         name="source",
     *         in="query",
     *         description="Sources for search",
     *         @OA\Schema(
     *         type="array",
     *         @OA\Items(ref="#/components/schemas/Source")
     *         ),
     *         style="form"
     *     ),
     *     @OA\Parameter(
     *         name="market",
     *         in="query",
     *         description="Markets for search",
     *         @OA\Schema(
     *         type="array",
     *             @OA\Items(ref="#/components/schemas/Market")
     *         ),
     *         style="form"
     *     ),
     *     @OA\Parameter(
     *         name="words",
     *         in="query",
     *         description="Words for search",
     *         @OA\Schema(
     *         type="array",
     *           @OA\Items(
     *               type="string",
     *           ),
     *         ),
     *         style="form"
     *     ),
     *     @OA\Parameter(
     *         name="filter",
     *         in="query",
     *         style="deepObject",
     *         @OA\Schema(
     *             type="string",
     *
     *                 @OA\Property(
     *                     property="from",
     *                     type="string",
     *                     format="date-time",
     *                 ),
     *                 example={"from"="2021-03-21T22:00:00.000+00:00"}
     *             )
     *         ),
     *       @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(
     *             @OA\Items(
     *                 type="array",
     *                 @OA\Items(
     *                     ref="#/components/schemas/feedback"
     *                 ),
     *             ),
     *         ),
     *       ),
     *       @OA\Response(response=400, description="Bad request"),
     *          security={
     *              {"oauth2": {}}
     *          }
     *     )
     *
     * @param Request $request
     * @param FeedbackRepository $feedbackRepository
     * @return JsonResponse
     */
    public function get(Request $request, FeedbackRepository $feedbackRepository): JsonResponse
    {
        return response()->json($feedbackRepository->search(
            $request->input('market.*.remote_id', []),
            $request->input('source.*.remote_id', []),
            $request->input('words', []),
            Carbon::parse($request->input('from'))
        ));
    }
}
