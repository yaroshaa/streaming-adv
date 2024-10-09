<?php

/**
 *
 * @OA\RequestBody(
 *     request="StoreMarketingChannel",
 *     description="Channel object that needs to be store",
 *     required=true,
 *     @OA\JsonContent(
 *        @OA\Property(
 *            property="data",
 *            type="object",
 *            ref="#/components/schemas/MarketingChannel")
 *        ),
 *     ),
 * )
 */

/**
 *
 * @OA\RequestBody(
 *     request="DeleteMarketingChannel",
 *     description="Channel object that needs to be delete",
 *     required=true,
 * )
 */
