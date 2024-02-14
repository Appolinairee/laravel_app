<?php

use App\Http\Controllers\Api\creator\DeleteCreatorController;
use App\Http\Controllers\Api\Creator\GetCreatorController;
use App\Http\Controllers\Api\Creator\storeCreatorController;
use App\Http\Controllers\Api\Creator\UpdateCreatorController;
use App\Http\Controllers\Api\Product\StoreProductController;
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
    | API User Routes
    |--------------------------------------------------------------------------
    */

    Route::prefix('user')->group(function () {
        Route::get('{userId}', [UserController::class, 'get']);
        Route::put('{user}', [UserController::class, 'update']);
        Route::delete('{user}', [UserController::class, 'delete']);
    });


    /*
    |--------------------------------------------------------------------------
    | API Creator Routes
    |--------------------------------------------------------------------------
    */

    Route::prefix('user/creator')->group(function () {
        Route::post('', [storeCreatorController::class, '__invoke']);
        Route::get('/{creator}', [GetCreatorController::class, '__invoke']);
        Route::match(['put', 'post'], '/{creator}', [UpdateCreatorController::class, '__invoke']);
        Route::delete('/{creator}', [DeleteCreatorController::class, '__invoke']);
    });



    /*
    |--------------------------------------------------------------------------
    | API Products Routes
    |--------------------------------------------------------------------------
    */

    Route::prefix('products')->group(function () {
        Route::post('', [StoreProductController::class, '__invoke']);
    });

});