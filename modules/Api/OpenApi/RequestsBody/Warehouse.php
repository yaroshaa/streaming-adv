<?php

/**
 *
 * @OA\RequestBody(
 *     request="StoreWarehouse",
 *     description="Warehouse object that needs to be store",
 *     required=true,
 *     @OA\JsonContent(
 *        @OA\Property(
 *            property="data",
 *            type="object",
 *            ref="#/components/schemas/Warehouse")
 *        ),
 *     ),
 * )
 */

/**
 *
 * @OA\RequestBody(
 *     request="DeleteWarehouse",
 *     description="Warehouse object that needs to be delete",
 *     required=true,
 * )
 */
