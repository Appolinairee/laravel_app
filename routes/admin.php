<?php

use App\Http\Controllers\Api\Admin\AdminUpdateCreator;
use App\Http\Controllers\Api\Admin\ProductAdminUpdate;
use App\Http\Controllers\Api\Creator\GetCreatorController;
use App\Http\Controllers\Api\Order\PaymentController;
use App\Http\Controllers\Api\Product\CategoriesController;
use App\Http\Controllers\Api\Product\GetProductController;
use App\Http\Controllers\Api\User\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    /*
    |------------------------
    | API Admin Routes
    |------------------------
    */

    Route::prefix('user')->group(function () {
        Route::get('/trash', [UserController::class, 'getUsersInTrash']);
    });

    Route::prefix('orders')->group(function () {
        Route::put('/{order}/validRefund', [PaymentController::class, 'validateRefund']);
    });

    Route::prefix('category')->group(function () {
        Route::post('', [CategoriesController::class, 'store']);
        Route::match(['put', 'post'], '{category}', [CategoriesController::class, 'update']);
        Route::delete('{category}', [CategoriesController::class, 'delete']);
    });


    Route::prefix('products')->group(function () {
        Route::put('{product}/status', [ProductAdminUpdate::class, 'activeProduct']);
        Route::get('/trash', [GetProductController::class, 'getProductsInTrash']);
        Route::get('/invalid', [GetProductController::class, 'getToValidateProducts']);
        Route::get('/invalid/{productId}', [GetProductController::class, 'getToValidateProduct']);
    });


    Route::prefix('creators')->group(function () {
        Route::put('{creator}/status', [AdminUpdateCreator::class, 'activeCreator']);
        Route::get('/trash', [GetCreatorController::class, 'getCreatorsInTrash']);
    });

});