<?php

use App\Http\Controllers\Api\Order\OrderGetController;
use App\Http\Controllers\Api\Order\OrderItemController;
use App\Http\Controllers\Api\Order\OrderItemStoreController;
use App\Http\Controllers\Api\Order\OrderItemUpdateController;
use App\Http\Controllers\Api\Order\OrderUpdateController;
use App\Http\Controllers\Api\Order\PaymentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    
    /*
    |------------------------
    | API Orders Routes
    |------------------------
    */

    Route::match(['post', 'put'] ,'{order}', [OrderUpdateController::class, '__invoke']);

    Route::get('/{orderId}', [OrderGetController::class, 'getOrder']);
    Route::get('/user/{user}', [OrderGetController::class, 'ordersByUser']);
    Route::get('/creator/{creator}', [OrderGetController::class, 'ordersByCreator']);
    Route::get('/product/{product}', [OrderGetController::class, 'ordersByProduct']);

    Route::delete('{order}', [OrderGetController::class, 'delete']);

    // Payments routes
    Route::put('/{order}/payment', [PaymentController::class, 'update']);


    /*
    |------------------------
    | API Order_item Routes
    |------------------------
    */
    Route::prefix('items')->group(function () {
        Route::post('/store', [OrderItemStoreController::class, '__invoke']);
        Route::put('/{orderItem}', [OrderItemUpdateController::class, '__invoke']);
        Route::get('/{orderItem}', [OrderItemController::class, 'orderItem']);
        Route::delete('/{orderItem}', [OrderItemController::class, 'delete']);
    });

});

