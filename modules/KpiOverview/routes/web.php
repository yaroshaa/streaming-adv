<?php

use Modules\KpiOverview\Http\Controllers\KpiOverviewController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'api/kpi-overview',
    'middleware' => ['api']
], function () {
    Route::get('/totals', [KpiOverviewController::class, 'getTotals']);
});
