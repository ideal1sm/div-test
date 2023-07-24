<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\UserRequestController;
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

/* --- Auth --- */

Route::prefix('auth')->name('auth.')->group(function () {
    Route::post('register', [AuthController::class, 'register'])
        ->name('register');
    Route::post('login', [AuthController::class, 'login'])
        ->name('login');
});
/* --- Send Request --- */

Route::post('requests', [UserRequestController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {
    /* --- Manager functions --- */

    Route::middleware('manager')->group(function () {
        Route::apiResource('requests', UserRequestController::class)
            ->only('index', 'update', 'show', 'destroy')
            ->parameters(['requests' => 'userRequest']);
    });
    Route::get('auth/logout', [AuthController::class, 'logout'])
        ->name('logout');
});
