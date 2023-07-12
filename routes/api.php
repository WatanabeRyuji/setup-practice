<?php

use App\Enums\TokenAbility;
use App\Http\Controllers\User\Auth\LoginController;
use App\Http\Controllers\User\Auth\LogoutController;
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


Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::middleware(['auth:sanctum', 'ability:'.TokenAbility::AccessApi])->group(function () {
    Route::post('/logout', LogoutController::class)->name('logout');
});

Route::post('/refresh-token', [LoginController::class, 'refresh'])->name('refresh_token')
    ->middleware(['auth:sanctum', 'ability:'.TokenAbility::RefreshToken]);
