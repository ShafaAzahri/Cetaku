<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthApiController;
use App\Http\Controllers\API\ItemController;
use App\Http\Controllers\API\BahanController;
use App\Http\Controllers\API\UkuranController;
use App\Http\Controllers\API\JenisController;
use App\Http\Controllers\API\BiayaDesainController;
use App\Http\Controllers\Admin\ItemViewController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Auth Routes
Route::prefix('auth')->group(function() {
    // Public routes
    Route::post('/login', [AuthApiController::class, 'login']);
    Route::post('/register', [AuthApiController::class, 'register']);
    
    // User info and logout
    Route::get('/user', [AuthApiController::class, 'getUserByToken']);
    Route::post('/logout', [AuthApiController::class, 'logout']);
});