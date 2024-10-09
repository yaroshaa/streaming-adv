<?php

use Illuminate\Support\Facades\Route;
use Modules\Analytic\Http\Actions\ClickAction;
use Modules\Analytic\Http\Actions\TrackAction;
use Modules\Analytic\Http\Controllers\AnalyticController;
use Modules\Analytic\Http\Controllers\EventController;
use Modules\Analytic\Http\Controllers\EventPropertiesController;
use Modules\Analytic\Http\Controllers\ConversationController;
use Modules\Analytic\Http\Controllers\SitesController;
use Modules\Analytic\Http\Controllers\ConversationRateController;

Route::group([
    'prefix' => 'api/analytic',
    'middleware' => [
        'api',
//        'auth:api'
    ],
], function () {
    // Api routes
    Route::get('/', AnalyticController::class);
});

Route::group([
    'prefix' => 'api/analytic',
    'middleware' => [
        'api'
    ],
], function () {
    Route::get('/sites', SitesController::class);

    Route::get('/events', EventController::class);
    Route::get('/events/properties', EventPropertiesController::class);

    Route::get('/conversations', [ConversationController::class, 'byDates']);
    Route::get('/conversations/by-minutes', [ConversationController::class, 'byMinutes']);
    Route::get('/conversations/by-hours', [ConversationController::class, 'byHours']);
});

Route::prefix('analytic')->group(function() {
});
