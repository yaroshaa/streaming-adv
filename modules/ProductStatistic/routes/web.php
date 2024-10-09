<?php

use Illuminate\Support\Facades\Route;
use Modules\ProductStatistic\Http\Controllers\ListingProductStatisticController;
use Modules\ProductStatistic\Http\Controllers\ProductStatisticDynamicsController;

Route::group([
    'prefix' => 'api/product-statistic',
    'middleware' => ['api', 'auth:api'],
], function () {
    Route::get('/', ListingProductStatisticController::class);
    Route::get('/data-dynamics', ProductStatisticDynamicsController::class);
});
