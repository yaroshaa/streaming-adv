<?php

/**
 * List all
 * @OA\Get(
 *     path="/api/market",
 *     summary="Get list of markets",
 *     tags={"Markets"},
 *     description="Returns list of markets",
 *       @OA\Response(
 *         response=200,
 *         description="successful operation",
 *         @OA\JsonContent(
*              @OA\Property(
 *                  property="data",
 *                  type="array",
 *                  @OA\Items(ref="#/components/schemas/Market"),
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
 *     path="/api/market",
 *     tags={"Markets"},
 *     security={{"token":{}}},
 *     @OA\Response(
 *         response=422,
 *         description="Unprocessable Entity"
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Created"
 *     ),
 *     requestBody={"$ref": "#/components/requestBodies/StoreMarket"}
 * )
 */

/**
 * Read
 *
 * @OA\Get(
 *     path="/api/market/{id}",
 *     tags={"Markets"},
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
 *         @OA\JsonContent(@OA\Property(property="data",type="object",ref="#/components/schemas/Market")),
 *      ),
 *     )
 *
 */

/**
 * Update
 *
 * @OA\Put(
 *     path="/api/market/{id}",
 *     tags={"Markets"},
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
 *     requestBody={"$ref": "#/components/requestBodies/StoreMarket"}
 * )
 */

/**
 * Delete
 *
 * @OA\Delete (
 *     path="/api/market/{id}",
 *     tags={"Markets"},
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
