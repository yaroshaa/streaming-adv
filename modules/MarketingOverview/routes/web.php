<?php

use Illuminate\Support\Facades\Route;
use Modules\MarketingOverview\Http\Controllers\HolidayEventController;
use Modules\MarketingOverview\Http\Controllers\MarketingOverviewController;

Route::group([
    'prefix' => 'api',
    'middleware' => [
        'api',
    ],
], function () {
    // Api routes
    Route::get('marketing-overview/data', [MarketingOverviewController::class, 'getData']);

    Route::group([
        'prefix' => 'settings'
    ], function () {
        Route::resource('holiday-event', HolidayEventController::class);
    });
});
