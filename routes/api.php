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

// Admin routes yang memerlukan autentikasi
Route::middleware(['auth:api'])->prefix('admin')->group(function () {
    // Items Routes
    Route::prefix('items')->group(function () {
        Route::get('/', [ItemController::class, 'index']);
        Route::post('/', [ItemController::class, 'store']);
        Route::get('/all', [ItemController::class, 'getAll']); // For dropdowns
        Route::get('/{id}', [ItemController::class, 'show']);
        Route::put('/{id}', [ItemController::class, 'update']);
        Route::delete('/{id}', [ItemController::class, 'destroy']);
    });
    
    // Bahans (Materials) Routes
    Route::prefix('bahans')->group(function () {
        Route::get('/', [BahanController::class, 'index']);
        Route::post('/', [BahanController::class, 'store']);
        Route::get('/all', [BahanController::class, 'getAll']); // For dropdowns
        Route::get('/item/{itemId}', [BahanController::class, 'getByItem']); // Get bahans by item
        Route::get('/{id}', [BahanController::class, 'show']);
        Route::put('/{id}', [BahanController::class, 'update']);
        Route::delete('/{id}', [BahanController::class, 'destroy']);
    });
    
    // Ukurans (Sizes) Routes
    Route::prefix('ukurans')->group(function () {
        Route::get('/', [UkuranController::class, 'index']);
        Route::post('/', [UkuranController::class, 'store']);
        Route::get('/all', [UkuranController::class, 'getAll']); // For dropdowns
        Route::get('/item/{itemId}', [UkuranController::class, 'getByItem']); // Get ukurans by item
        Route::get('/{id}', [UkuranController::class, 'show']);
        Route::put('/{id}', [UkuranController::class, 'update']);
        Route::delete('/{id}', [UkuranController::class, 'destroy']);
    });
    
    // Jenis (Categories) Routes
    Route::prefix('jenis')->group(function () {
        Route::get('/', [JenisController::class, 'index']);
        Route::post('/', [JenisController::class, 'store']);
        Route::get('/all', [JenisController::class, 'getAll']); // For dropdowns
        Route::get('/item/{itemId}', [JenisController::class, 'getByItem']); // Get jenis by item
        Route::get('/{id}', [JenisController::class, 'show']);
        Route::put('/{id}', [JenisController::class, 'update']);
        Route::delete('/{id}', [JenisController::class, 'destroy']);
    });
    
    // Biaya Desain (Design Costs) Routes
    Route::prefix('biaya-desain')->group(function () {
        Route::get('/', [BiayaDesainController::class, 'index']);
        Route::post('/', [BiayaDesainController::class, 'store']);
        Route::get('/all', [BiayaDesainController::class, 'getAll']); // For dropdowns
        Route::get('/{id}', [BiayaDesainController::class, 'show']);
        Route::put('/{id}', [BiayaDesainController::class, 'update']);
        Route::delete('/{id}', [BiayaDesainController::class, 'destroy']);
    });
});

// Route untuk view controller (untuk mengambil data dari API)
Route::prefix('view')->middleware(['auth:api'])->group(function () {
    // Item View Routes
    Route::prefix('items')->group(function() {
        Route::get('/', [ItemViewController::class, 'getItems']);
        Route::get('/dropdown', [ItemViewController::class, 'getItemsDropdown']);
        Route::get('/{id}', [ItemViewController::class, 'getItem']);
        Route::post('/clear-cache', [ItemViewController::class, 'clearCache']);
    });
});