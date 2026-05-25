<?php

use App\Http\Controllers\Api\AuthController;

// Public endpoints
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected endpoints
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('rooms', \App\Http\Controllers\Api\RoomController::class)->only(['index', 'show']);
    Route::apiResource('bookings', \App\Http\Controllers\Api\BookingController::class)->except(['update']);
});
