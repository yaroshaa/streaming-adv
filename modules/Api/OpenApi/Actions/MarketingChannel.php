<?php

/**
 * List all
 * @OA\Get(
 *     path="/api/marketing-channel",
 *     summary="Get list of marketing channels",
 *     tags={"Marketing channels"},
 *     description="Returns list of marketing channels",
 *       @OA\Response(
 *         response=200,
 *         description="successful operation",
 *         @OA\JsonContent(
 *              @OA\Property(
 *                  property="data",
 *                  type="array",
 *                  @OA\Items(ref="#/components/schemas/MarketingChannel"),
 *             ),
 *         ),
 *       ),
 *       @OA\Response(response=400, description="Bad request"),
 *          security={{"oauth2": {}}
 *       }
 * )
 *
 */

/**
 * Create
 *
 * @OA\Post(
 *     path="/api/marketing-channel",
 *     tags={"Marketing channels"},
 *     security={{"token":{}}},
 *     @OA\Response(
 *         response=422,
 *         description="Unprocessable Entity"
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Created"
 *     ),
 *     requestBody={"$ref": "#/components/requestBodies/StoreMarketingChannel"}
 * )
 */

/**
 * Read
 *
 * @OA\Get(
 *     path="/api/marketing-channel/{id}",
 *     tags={"Marketing channels"},
 *     security={{"token":{}}},
 *     @OA\Parameter(
 *      name="id",
 *      in="path",
 *      required=true,
 *      @OA\Schema (
 *          type="integer"
 *      )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Not found"
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Ok",
 *         @OA\JsonContent(@OA\Property(property="data",type="object",ref="#/components/schemas/MarketingChannel")),
 *     ),

 * )
 */

/**
 * Update
 *
 * @OA\Put(
 *     path="/api/marketing-channel/{id}",
 *     tags={"Marketing channels"},
 *     security={{"token":{}}},
 *     @OA\Parameter(
 *      name="id",
 *      in="path",
 *      required=true,
 *      @OA\Schema (
 *          type="integer"
 *      )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Unprocessable Entity"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Not found"
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Updated"
 *     ),
 *     requestBody={"$ref": "#/components/requestBodies/StoreMarketingChannel"}
 * )
 */

/**
 * Delete
 *
 * @OA\Delete (
 *     path="/api/marketing-channel/{id}",
 *     tags={"Marketing channels"},
 *     security={{"token":{}}},
 *     @OA\Parameter(
 *      name="id",
 *      in="path",
 *      required=true,
 *      @OA\Schema (
 *          type="integer"
 *      )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Not found"
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Deleted"
 *     ),
 * )
 */
