<?php

use App\Http\Controllers\Api\Creator\GetCreatorController;
use App\Http\Controllers\Api\Creator\storeCreatorController;
use App\Http\Controllers\Api\Creator\UpdateCreatorController;
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
        Route::put('/{creator}', [UpdateCreatorController::class, '__invoke']);
    });

});

// Quel est l'intérêt d'envoyer des requêtes json quand l'utilisateur n'est pas trouvé
// Bonne pratique de parser les paramètres depuis les routes