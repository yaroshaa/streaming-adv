<?php

/**
 *
 * @OA\RequestBody(
 *     request="StoreSource",
 *     description="Source object that needs to be store",
 *     required=true,
 *     @OA\JsonContent(
 *        @OA\Property(
 *            property="data",
 *            type="object",
 *            ref="#/components/schemas/Source")
 *        ),
 *     ),
 * )
 */

/**
 *
 * @OA\RequestBody(
 *     request="DeleteSource",
 *     description="Source object that needs to be delete",
 *     required=true,
 * )
 */
