<?php

// use App\Http\Controllers\LogoutController;

use App\Http\Controllers\Product\CategoriesController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    /*
    |------------------------
    | API Admin Routes
    |------------------------
    */

    Route::prefix('category')->group(function () {
        Route::post('', [CategoriesController::class, 'store']);
        Route::match(['put', 'post'], '{category}', [CategoriesController::class, 'update']);
        Route::delete('{category}', [CategoriesController::class, 'delete']);
    });

});