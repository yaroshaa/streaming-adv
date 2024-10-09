<?php

use Modules\StoreToday\Http\Controllers\StoreTodayController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'api',
    'middleware' => [
        'api',
    ]
], function ($router) {
    Route::get('store-today/data', [StoreTodayController::class, 'getData']);
});
