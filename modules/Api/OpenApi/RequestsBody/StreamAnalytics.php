<?php

/**
 *
 * @OA\RequestBody(
 *     request="CartAction",
 *     description="CartAction object need to be store",
 *     required=true,
 *     @OA\JsonContent(
 *          ref="#/components/schemas/CartAction"
 *     ),
 * )
 *
 * @OA\RequestBody(
 *     request="ActiveUser",
 *     description="ActiveUser object need to be store",
 *     required=true,
 *     @OA\JsonContent(
 *          ref="#/components/schemas/ActiveUser"
 *     ),
 * )
 *
 * @OA\RequestBody(
 *     request="WarehouseStatistic",
 *     description="WarehouseStatistic object need to be store",
 *     required=true,
 *     @OA\JsonContent(
 *          ref="#/components/schemas/WarehouseStatistic"
 *     ),
 * )
 */
