<?php

use App\Enums\TokenAbility;
use App\Http\Controllers\User\Auth\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::middleware('guest')->group(function () {
    Route::prefix('/auth')->name('auth.')->group(function () {
        Route::post('/login', [AuthController::class, 'login'])->name('login');
        Route::post('/register', [AuthController::class, 'register'])->name('register');
        Route::post('/refresh-token', [AuthController::class, 'refresh'])->name('refresh_token')
            ->middleware(['auth:sanctum', 'ability:'.TokenAbility::RefreshToken]);
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot_password');
        Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('reset_password');
    });
});

Route::middleware(['auth:sanctum', 'ability:'.TokenAbility::AccessApi])->group(function () {
    Route::prefix('/auth')->name('auth.')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    });
});
