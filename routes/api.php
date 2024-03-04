<?php

use App\Http\Controllers\Api\creator\DeleteCreatorController;
use App\Http\Controllers\Api\Creator\DeleteLogoController;
use App\Http\Controllers\Api\Creator\GetCreatorController;
use App\Http\Controllers\Api\Creator\storeCreatorController;
use App\Http\Controllers\Api\Creator\UpdateCreatorController;
use App\Http\Controllers\Api\Message\MessageController;
use App\Http\Controllers\Api\Message\MessageStoreController;
use App\Http\Controllers\Api\Message\MessageUpdateController;
use App\Http\Controllers\Api\Product\DeleteProductController;
use App\Http\Controllers\Api\Product\getProductController;
use App\Http\Controllers\Api\Product\LikeContoller;
use App\Http\Controllers\Api\Product\MediasController;
use App\Http\Controllers\Api\Product\StoreProductController;
use App\Http\Controllers\Api\Product\UpdateProductController;
use App\Http\Controllers\Api\User\UserController;
use App\Http\Controllers\Api\Product\CategoriesController;
use App\Http\Controllers\Api\Product\CommentController;
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

Route::middleware('auth:sanctum')->group(function () {

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


    Route::prefix('comments')->group(function () {
        Route::post('/{product}', [CommentController::class, 'storeComment']);
        Route::put('/{comment}', [CommentController::class, 'updateComment']);
        Route::delete('/{comment}', [CommentController::class, 'deleteComment']);
    });

    Route::prefix('likes')->group(function () {
        Route::post('/{product}', [LikeContoller::class, 'storeLike']);
    });



    /*
    |--------------------------------------------------------------------------
    | API Messages Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('messages')->group(function () {
        Route::post('', [MessageStoreController::class, '__invoke']);
        Route::put('/{message}', [MessageUpdateController::class, '__invoke']);
        Route::delete('/{message}', [MessageController::class, 'delete']);
        Route::get('/user/{user}', [MessageController::class, 'messagesByUser']);
        Route::get('/users', [MessageController::class, 'getUsersWithLastMessages']);
    });


    /*
    |--------------------------------------------------------------------------
    | API Messages Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('notifications')->group(function () {
        Route::get('/user/{user}', [NotificationController::class, 'getUserNotifications']);
        Route::get('/product/{product}', [NotificationController::class, 'getProductNotifications']);
    });


});


/*
|--------------------------------------------------------------------------
| Public Creators Routes
|--------------------------------------------------------------------------
*/
Route::get('/creators', [GetCreatorController::class, 'getCreators']);



/*
|--------------------------------------------------------------------------
| Public Categories Routes
|--------------------------------------------------------------------------
*/
Route::get('/categories', [CategoriesController::class, 'getCategories']);



/*
|--------------------------------------------------------------------------
| Public Products Routes
|--------------------------------------------------------------------------
*/
Route::get('products', [getProductController::class, 'getProducts']);
Route::get('products/{product}', [getProductController::class, 'getProduct']);
Route::get('products/creator/{creator}', [getProductController::class, 'getProductByCreator']);
Route::get('products/category/{category}', [getProductController::class, 'getProductByCategory']);


/*
|--------------------------------------------------------------------------
| Public Interactions (about Product) Routes
|--------------------------------------------------------------------------
*/
Route::get('comments/{product}', [CommentController::class, 'getCommentsByProduct']);
Route::get('likes/{product}', [LikeContoller::class, 'getLikesByProduct']);
