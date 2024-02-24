<?php

use App\Http\Controllers\Api\Order\OrderItemController;
use App\Http\Controllers\Api\Order\OrderItemStoreController;
use App\Http\Controllers\Api\Order\OrderItemUpdateController;
use App\Http\Controllers\Api\Order\OrderStoreController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    
    /*
    |------------------------
    | API Orders Routes
    |------------------------
    */

    Route::post('', [OrderStoreController::class, '__invoke']);




    /*
    |------------------------
    | API Order_item Routes
    |------------------------
    */
    Route::prefix('items')->group(function () {
        Route::post('', [OrderItemStoreController::class, '__invoke']);
        Route::put('/{orderItem}', [OrderItemUpdateController::class, '__invoke']);
        Route::get('/{orderItem}', [OrderItemController::class, 'orderItem']);
        Route::delete('/{orderItem}', [OrderItemController::class, 'delete']);
    });


});

