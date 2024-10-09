<?php

use App\Http\Actions\OpCacheResetAction;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\MarketController;
use App\Http\Controllers\OrderStatusController;
use App\Http\Controllers\SourceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

/**
 * Api routes
 */
Route::group([
    'middleware' => ['api'],
], function () {
    Route::group([
        'prefix' => 'system'
    ], function() {
       Route::get('op-cache-reset/{secret}', OpCacheResetAction::class);
    });

    Route::group(['middleware' => ['web']], function (){
        Route::post('analytics/{event}', [\App\Http\Controllers\AnalyticsController::class, 'track']);
    });

    /**
     * Authentication  part
     */
    Route::group([
        'prefix' => 'auth'
    ], function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('social', [AuthController::class, 'social']);
        Route::post('social/register', [AuthController::class, 'socialRegister']);
        Route::post('register', [AuthController::class, 'register']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('refresh', [AuthController::class, 'refresh']);
        Route::get('user', [AuthController::class, 'user']);
    });

    /**
     * Only authorized users
     */
    Route::group([
        'middleware' => ['auth:api'],
    ], function () {
        Route::get('/markets', [MarketController::class, 'getAll']);
        Route::get('/sources', [SourceController::class, 'getAll']);
        Route::get('/currencies', [CurrencyController::class, 'getAll']);
        Route::get('/exchange-rates/{currency}', [CurrencyController::class, 'getExchangeRatesFor']);
        Route::get('/order-statuses', [OrderStatusController::class, 'getAll']);
        Route::get('/date-granularity-options', [Controller::class, 'dateGranularityOptions']);
    });
});
