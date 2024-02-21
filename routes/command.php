<?php

use App\Http\Controllers\Command\CommandStoreController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    /*
    |------------------------
    | API Command Routes
    |------------------------
    */

    Route::post('', [CommandStoreController::class, '__invoke']);

});

