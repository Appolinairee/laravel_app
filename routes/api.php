<?php

use App\Http\Controllers\Api\User\UserController;
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

Route::middleware('auth:sanctum')->group(function(){

    /*
    |--------------------------------------------------------------------------
    | API Auth Routes
    |--------------------------------------------------------------------------
    */

    Route::prefix('user')->group(function () {
        Route::get('{userId}', [UserController::class, 'get']);
        Route::put('{user}', [UserController::class, 'update']);
        Route::delete('{user}', [UserController::class, 'delete']);
    });

});