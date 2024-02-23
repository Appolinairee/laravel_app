<?php

use App\Http\Controllers\Api\Order\OrderItemStoreController;
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
    Route::post('/items', [OrderItemStoreController::class, '__invoke']);

});

