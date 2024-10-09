<?php

use Modules\CompanyOverview\Http\Controllers\CompanyOverviewController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'api/company-overview',
    'middleware' => 'api'
], function ($router) {
    Route::get('/pie-chart-data', [CompanyOverviewController::class, 'getPieChartData']);
    Route::get('/stream-graph-data', [CompanyOverviewController::class, 'getStreamGraphData']);
    Route::get('/totals', [CompanyOverviewController::class, 'getTotals']);
    Route::get('/totals-by-market', [CompanyOverviewController::class, 'getTotalsByMarket']);
    Route::get('/orders', [CompanyOverviewController::class, 'getOrders']);
});
