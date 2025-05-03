<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthApiController;
use App\Http\Controllers\API\ItemApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Route autentikasi publik (tidak memerlukan autentikasi)
Route::prefix('auth')->group(function() {
    Route::post('/login', [AuthApiController::class, 'login']);
    Route::post('/register', [AuthApiController::class, 'register']);
    Route::get('/user', [AuthApiController::class, 'getUserByToken']);
    Route::post('/logout', [AuthApiController::class, 'logout']);
});

// Route untuk Item API
// Catatan: Sementara tanpa middleware auth:api untuk memudahkan testing
Route::prefix('items')->group(function() {
    Route::get('/', [ItemApiController::class, 'index']);
    Route::get('/{id}', [ItemApiController::class, 'show']);
    Route::post('/', [ItemApiController::class, 'store']);
    Route::put('/{id}', [ItemApiController::class, 'update']);
    Route::delete('/{id}', [ItemApiController::class, 'destroy']);
});

// Route untuk health check
Route::get('/health', function() {
    return response()->json(['status' => 'ok', 'timestamp' => now()]);
});