<?php

use Modules\Api\Http\Controllers\MarketController;
use Modules\Api\Http\Controllers\MarketingChannelController;
use Modules\Api\Http\Controllers\SourceController;
use Modules\Api\Http\Controllers\WarehouseController;
use Modules\Api\Http\Controllers\StreamAnalyticsController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'api',
    'middleware' => [
        'api',
    ],
], function () {
    Route::resource('market', MarketController::class);
    Route::resource('source', SourceController::class);
    Route::resource('warehouse', WarehouseController::class);
    Route::resource('marketing-channel', MarketingChannelController::class)
        ->parameters(['marketing-channel' => 'channel']);
    Route::post('cart-action', [StreamAnalyticsController::class, 'cartAction']);
    Route::post('active-user', [StreamAnalyticsController::class, 'activeUser']);
    Route::post('warehouse-statistic', [StreamAnalyticsController::class, 'warehouseStatistic']);
});
