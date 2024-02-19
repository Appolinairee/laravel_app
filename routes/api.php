<?php

use App\Http\Controllers\Api\creator\DeleteCreatorController;
use App\Http\Controllers\Api\Creator\DeleteLogoController;
use App\Http\Controllers\Api\Creator\GetCreatorController;
use App\Http\Controllers\Api\Creator\storeCreatorController;
use App\Http\Controllers\Api\Creator\UpdateCreatorController;
use App\Http\Controllers\Api\Product\DeleteProductController;
use App\Http\Controllers\Api\Product\getProductController;
use App\Http\Controllers\Api\Product\MediasController;
use App\Http\Controllers\Api\Product\StoreProductController;
use App\Http\Controllers\Api\Product\UpdateProductController;
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
        Route::get('/{creator}', [GetCreatorController::class, 'getCreator']);
        Route::match(['put', 'post'], '/{creator}', [UpdateCreatorController::class, '__invoke']);
        Route::delete('/{creator}', [DeleteCreatorController::class, '__invoke']);
        Route::delete('/{creator}/logo', [DeleteLogoController::class, '__invoke']);
    });



    /*
    |--------------------------------------------------------------------------
    | API Products Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('products')->group(function () {
        Route::post('', [StoreProductController::class, '__invoke']);
        Route::put('/{product}', [UpdateProductController::class, '__invoke']);
        Route::delete('/{product}', [DeleteProductController::class, '__invoke']);


        // Product Media
        Route::post('/{product}/image', [MediasController::class, 'storeImage']);
        Route::post('/{product}/video', [MediasController::class, 'storeVideo']);
        Route::delete('/{product}/{media}', [MediasController::class, 'delete']);
    });



    /*
    |--------------------------------------------------------------------------
    | Public Creators Routes
    |--------------------------------------------------------------------------
    */

    Route::get('/creator', [GetCreatorController::class, 'getCreators']);

});


 
/*
|--------------------------------------------------------------------------
| Public Products Routes
|--------------------------------------------------------------------------
*/

Route::get('products', [getProductController::class, 'getProducts']);
Route::get('products{categorie}', [getProductController::class, 'getProducts']);
Route::get('products/{product}', [getProductController::class, 'getProduct']);
