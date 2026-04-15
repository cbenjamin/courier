<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CourierOrderController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

// ─── Public ───────────────────────────────────────────────────────────────────

Route::post('/auth/login', [AuthController::class, 'login']);

// ─── Authenticated ────────────────────────────────────────────────────────────

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/auth/logout', [AuthController::class, 'logout']);

    Route::get('/user', [UserController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);

    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);

    // Pusher channel auth — must accept Bearer tokens, not session cookies
    Route::post('/broadcasting/auth', function (\Illuminate\Http\Request $request) {
        return Broadcast::auth($request);
    });

    // ─── Courier (admin only) ─────────────────────────────────────────────────

    Route::middleware('api.admin')->prefix('courier')->group(function () {
        Route::get('/orders', [CourierOrderController::class, 'index']);
        Route::patch('/orders/{order}/status', [CourierOrderController::class, 'updateStatus']);
        Route::post('/orders/{order}/location', [CourierOrderController::class, 'updateLocation']);
    });
});
