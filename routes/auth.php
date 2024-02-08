<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\PasswordResetController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Auth Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::post('register', [RegisterController::class, '__invoke']);
    Route::post('login', [LoginController::class, '__invoke']);

    Route::post('password/email', [PasswordResetController::class, 'sendLink']);
    Route::post('password/reset', [PasswordResetController::class, 'reset'])->middleware('signed')->name('password.reset');

    Route::post('email/verify', [VerifyEmailController::class, 'verifyEmail'])->middleware('signed')->name('email.verify');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [LogoutController::class, '__invoke']);
    Route::post('email/send', [VerifyEmailController::class, 'sendLink']);
});