<?php

use App\Http\Controllers\GAuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('element-ui');
});

Route::get('/tw', function () {
    return view('tailwind');
});

Route::group([
    'middleware' => 'web'
], function($router) {
    Route::get('/gauth', [GAuthController::class, 'gAuth']);
    Route::get('/gauth/postback', [GAuthController::class, 'gAuthPostBack'])->name('gauth.postback');
});
