<?php

use App\Http\Controllers\User\Auth\LoginController;
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
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/', function () {
        dd(auth()->user()->name);
    })->name('home');
});
