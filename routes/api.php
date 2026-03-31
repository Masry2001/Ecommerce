<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\CustomerAuthController;

Route::prefix('v1')->group(function () {
    Route::post('/register', [CustomerAuthController::class, 'register']);
    Route::post('/login', [CustomerAuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/customer', [CustomerAuthController::class, 'customer']);
        Route::post('/logout', [CustomerAuthController::class, 'logout']);
    });
});
