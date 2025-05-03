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

use App\Http\Controllers\ItemController;

Route::get('items', [ItemController::class, 'index']);
Route::post('items', [ItemController::class, 'store']);
Route::get('items/{id}', [ItemController::class, 'show']);
Route::put('items/{id}', [ItemController::class, 'update']);
Route::delete('items/{id}', [ItemController::class, 'destroy']);


// Route untuk health check
Route::get('/health', function() {
    return response()->json(['status' => 'ok', 'timestamp' => now()]);
});