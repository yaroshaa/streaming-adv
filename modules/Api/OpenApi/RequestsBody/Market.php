<?php

/**
 *
 * @OA\RequestBody(
 *     request="StoreMarket",
 *     description="Market object that needs to be store",
 *     required=true,
 *     @OA\JsonContent(
 *        @OA\Property(
 *            property="data",
 *            type="object",
 *            ref="#/components/schemas/Market")
 *        ),
 *     ),
 * )
 */

/**
 *
 * @OA\RequestBody(
 *     request="DeleteMarket",
 *     description="Market object that needs to be delete",
 *     required=true,
 * )
 */
