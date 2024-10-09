<?php

/**
 * List all
 * @OA\Get(
 *     path="/api/warehouse",
 *     summary="Get list of warehouses",
 *     tags={"Warehouses"},
 *     description="Returns list of warehouses",
 *       @OA\Response(
 *         response=200,
 *         description="successful operation",
 *         @OA\JsonContent(
 *              @OA\Property(
 *                  property="data",
 *                  type="array",
 *                  @OA\Items(ref="#/components/schemas/Warehouse"),
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
 *     path="/api/warehouse",
 *     tags={"Warehouses"},
 *     security={{"token":{}}},
 *     @OA\Response(
 *         response=422,
 *         description="Unprocessable Entity"
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Created"
 *     ),
 *     requestBody={"$ref": "#/components/requestBodies/StoreWarehouse"}
 * )
 */

/**
 * Read
 *
 * @OA\Get(
 *     path="/api/warehouse/{id}",
 *     tags={"Warehouses"},
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
 *         @OA\JsonContent(@OA\Property(property="data",type="object",ref="#/components/schemas/Warehouse")),
 *     ),

 * )
 */

/**
 * Update
 *
 * @OA\Put(
 *     path="/api/warehouse/{id}",
 *     tags={"Warehouses"},
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
 *     requestBody={"$ref": "#/components/requestBodies/StoreWarehouse"}
 * )
 */

/**
 * Delete
 *
 * @OA\Delete (
 *     path="/api/warehouse/{id}",
 *     tags={"Warehouses"},
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
