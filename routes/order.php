<?php

use App\Http\Controllers\Api\Order\OrderStoreController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    /*
    |------------------------
    | API Order Routes
    |------------------------
    */

    Route::post('', [OrderStoreController::class, '__invoke']);

});

