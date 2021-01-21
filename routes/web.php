<?php

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

Route::get('/', [\App\Http\Controllers\IndexController::class, 'index']);
Route::get('/couriers', [\App\Http\Controllers\CouriersController::class, 'index']);
Route::post('/couriers/update', [\App\Http\Controllers\CouriersController::class, 'update']);

Route::get('/map', [\App\Http\Controllers\MapController::class, 'index']);

Route::post('/orders/update', [\App\Http\Controllers\OrdersController::class, 'update']);



Route::post('/user/filters/set', [\App\Http\Controllers\UserFiltersController::class, 'update']);
