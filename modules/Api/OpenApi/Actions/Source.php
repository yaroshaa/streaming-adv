<?php

/**
 * List all
 * @OA\Get(
 *     path="/api/source",
 *     summary="Get list of sources",
 *     tags={"Sources"},
 *     description="Returns list of sources",
 *       @OA\Response(
 *         response=200,
 *         description="successful operation",
 *         @OA\JsonContent(
 *              @OA\Property(
 *                  property="data",
 *                  type="array",
 *                  @OA\Items(ref="#/components/schemas/Source"),
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
 *     path="/api/source",
 *     tags={"Sources"},
 *     security={{"token":{}}},
 *     @OA\Response(
 *         response=422,
 *         description="Unprocessable Entity"
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Created"
 *     ),
 *     requestBody={"$ref": "#/components/requestBodies/StoreSource"}
 * )
 */

/**
 * Read
 *
 * @OA\Get(
 *     path="/api/source/{id}",
 *     tags={"Sources"},
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
 *         @OA\JsonContent(@OA\Property(property="data",type="object",ref="#/components/schemas/Source")),
 *     ),
 * )
 */

/**
 * Update
 *
 * @OA\Put(
 *     path="/api/source/{id}",
 *     tags={"Sources"},
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
 *     requestBody={"$ref": "#/components/requestBodies/StoreSource"}
 * )
 */

/**
 * Delete
 *
 * @OA\Delete (
 *     path="/api/source/{id}",
 *     tags={"Sources"},
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
