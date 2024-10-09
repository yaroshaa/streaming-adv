<?php

use Illuminate\Support\Facades\Route;
use Modules\ModuleName\Http\Controllers\ModuleNameController;

Route::group([
    'prefix' => 'api/module-name',
    'middleware' => [
        'api',
        'auth:api'
    ],
], function () {
    // Api routes
    Route::get('/', ModuleNameController::class);
});
