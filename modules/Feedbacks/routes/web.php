<?php

use Modules\Feedbacks\Http\Controllers\FacebookWebhookController;
use Modules\Feedbacks\Http\Controllers\HelpScoutWebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Feedbacks\Http\Controllers\FeedbacksController;
use Modules\Feedbacks\Http\Controllers\TagController;

Route::group([
    'prefix' => 'api',
    'middleware' => [
        'api',
    ],
], function () {
    /** Route for register FB webhook */
    Route::get('facebook', function (Request $request) {
        return response($request->input('hub_challenge'));
    });

    Route::post('facebook', FacebookWebhookController::class);

    /** Route for register HelpScout webhook */
    Route::get('help-scout', function () {
        return response()->json(['html' => '<ul><li>Some html here</li></ul>']);
    });

    Route::post('help-scout', HelpScoutWebhookController::class);

    Route::post('/feedbacks', [FeedbacksController::class, 'insert']);
    Route::get('/feedbacks', [FeedbacksController::class, 'get']);

    Route::group([
        'prefix' => 'settings'
    ], function () {
        Route::resource('tag', TagController::class);
    });
});
