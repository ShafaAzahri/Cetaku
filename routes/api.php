<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthApiController;
use App\Http\Controllers\API\ItemApiController;

// Route autentikasi publik (tidak memerlukan autentikasi)
Route::prefix('auth')->group(function() {
    Route::post('/login', [AuthApiController::class, 'login']);
    Route::post('/register', [AuthApiController::class, 'register']);
    Route::get('/user', [AuthApiController::class, 'getUserByToken']);
    Route::post('/logout', [AuthApiController::class, 'logout']);
});

// Routes untuk item
Route::get('/items', [ItemApiController::class, 'index']);
Route::post('/items', [ItemApiController::class, 'store']);
Route::get('/items/{id}', [ItemApiController::class, 'show']);
Route::put('/items/{id}', [ItemApiController::class, 'update']);
Route::delete('/items/{id}', [ItemApiController::class, 'destroy']);

// Route untuk health check
Route::get('/health', function() {
    return response()->json(['status' => 'ok', 'timestamp' => now()]);
});