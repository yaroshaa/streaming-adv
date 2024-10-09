<?php

/**
 * Cart action
 *
 * @OA\Post(
 *     path="/api/cart-action",
 *     summary="Store cart action",
 *     tags={"StreamAnalytics"},
 *     description="Store cart actions",
 *     @OA\Response(
 *         response=201,
 *         description="Created"
 *     ),
 *     requestBody={"$ref": "#/components/requestBodies/CartAction"}
 * )
 *
 *
 * Active user
 *
 * @OA\Post(
 *     path="/api/active-user",
 *     summary="Store active user",
 *     tags={"StreamAnalytics"},
 *     description="Store active user",
 *     @OA\Response(
 *         response=201,
 *         description="Created"
 *     ),
 *     requestBody={"$ref": "#/components/requestBodies/ActiveUser"}
 * )
 *
 *
 * Warehouse statistics.
 *
 * @OA\Post(
 *     path="/api/warehouse-statistic",
 *     summary="Store warehouse statistics",
 *     tags={"StreamAnalytics"},
 *     description="Store warehouse statistics",
 *     @OA\Response(
 *         response=201,
 *         description="Created"
 *     ),
 *     requestBody={"$ref": "#/components/requestBodies/WarehouseStatistic"}
 * )
 */

