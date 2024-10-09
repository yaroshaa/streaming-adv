<?php

use Illuminate\Support\Facades\Route;
use Modules\Orders\Http\Controllers\OrdersController;

Route::group([
    'prefix' => 'api',
    'middleware' => [
        'api',
    ],
], function () {
    Route::post('/orders', [OrdersController::class, 'insert']);
    Route::get('/orders', [OrdersController::class, 'list']);
    Route::get('/top-selling-products', [OrdersController::class, 'topSellingProducts']);
    Route::get('/fifteen-min-totals', [OrdersController::class, 'fifteenMinTotals']);
});
