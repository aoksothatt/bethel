<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PlayerController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Health check endpoint for deployment monitoring
Route::get('/health', function () {
    $dbOk = false;
    try {
        \Illuminate\Support\Facades\DB::connection()->getPdo();
        $dbOk = true;
    } catch (\Throwable $e) {
        $dbOk = false;
    }

    return response()->json([
        'status' => 'ok',
        'app' => 'Bethel High School — Team Jersey Tracker',
        'database' => $dbOk ? 'connected' : 'disconnected',
        'timestamp' => now()->toIso8601String()
    ], $dbOk ? 200 : 503);
});

// Public routes for all team members (read-only)
Route::get('/players', [PlayerController::class, 'index']);
Route::get('/players/{id}', [PlayerController::class, 'show']);

// Admin Authentication route
Route::post('/admin/login', [AuthController::class, 'login']);

// Protected Admin Dashboard routes (Requires Sanctum Bearer Token)
Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Player CRUD management
    Route::post('/players', [PlayerController::class, 'store']);
    Route::put('/players/{id}', [PlayerController::class, 'update']);
    Route::patch('/players/{id}/payment', [PlayerController::class, 'updatePayment']);
    Route::delete('/players/{id}', [PlayerController::class, 'destroy']);
});
