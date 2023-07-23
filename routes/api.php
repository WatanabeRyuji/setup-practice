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


Route::post('/auth/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('/auth/register', [AuthController::class, 'register'])->name('auth.register');
Route::post('/auth/refresh-token', [AuthController::class, 'refresh'])->name('auth.refresh_token')
    ->middleware(['auth:sanctum', 'ability:'.TokenAbility::RefreshToken]);

Route::middleware(['auth:sanctum', 'ability:'.TokenAbility::AccessApi])->group(function () {
    Route::prefix('/auth')->name('auth.')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    });
});
