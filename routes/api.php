<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProductApiController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Rute Publik
Route::post('/login', [AuthController::class, 'apiLogin']);
Route::post('/register', [AuthController::class, 'apiRegister']);

// Rute Produk Publik (yang bisa diakses tanpa login)
Route::prefix('products')->group(function() {
    Route::get('/', [ProductApiController::class, 'getAllItems']);
    Route::get('/{id}', [ProductApiController::class, 'getItem']);
    Route::post('/calculate-price', [ProductApiController::class, 'calculatePrice']);
    
    // Rute untuk mendapatkan opsi produk
    Route::get('/options/{id}', [ProductApiController::class, 'getItemOptions']);
    
    // Rute untuk mendapatkan semua bahan, ukuran, dan jenis
    Route::get('/bahans', [ProductApiController::class, 'getAllBahans']);
    Route::get('/ukurans', [ProductApiController::class, 'getAllUkurans']);
    Route::get('/jenis', [ProductApiController::class, 'getAllJenis']);
    Route::get('/biaya-desain', [ProductApiController::class, 'getAllBiayaDesain']);
});

// Protected Routes - Requires API Token
Route::middleware('auth:api')->group(function() {
    // User Route
    Route::get('/user', function(Request $request) {
        $user = $request->user();
        return response()->json([
            'id' => $user->id,
            'nama' => $user->nama,
            'email' => $user->email,
            'role' => $user->role->nama_role ?? null    
        ]);
    });
    
    // Admin Routes
    Route::prefix('admin')->group(function() {
        // Item routes
        Route::get('/items', [ProductApiController::class, 'getAllItems']);
        Route::post('/items', [ProductApiController::class, 'storeItem']);
        Route::get('/items/{id}', [ProductApiController::class, 'getItem']);
        Route::post('/items/{id}', [ProductApiController::class, 'updateItem']); // POST untuk file upload
        Route::delete('/items/{id}', [ProductApiController::class, 'deleteItem']);
        
        // Bahan routes
        Route::get('/bahans', [ProductApiController::class, 'getAllBahans']);
        Route::post('/bahans', [ProductApiController::class, 'storeBahan']);
        Route::get('/bahans/{id}', [ProductApiController::class, 'getBahan']);
        Route::put('/bahans/{id}', [ProductApiController::class, 'updateBahan']);
        Route::delete('/bahans/{id}', [ProductApiController::class, 'deleteBahan']);
        
        // Ukuran routes
        Route::get('/ukurans', [ProductApiController::class, 'getAllUkurans']);
        Route::post('/ukurans', [ProductApiController::class, 'storeUkuran']);
        Route::get('/ukurans/{id}', [ProductApiController::class, 'getUkuran']);
        Route::put('/ukurans/{id}', [ProductApiController::class, 'updateUkuran']);
        Route::delete('/ukurans/{id}', [ProductApiController::class, 'deleteUkuran']);
        
        // Jenis routes
        Route::get('/jenis', [ProductApiController::class, 'getAllJenis']);
        Route::post('/jenis', [ProductApiController::class, 'storeJenis']);
        Route::get('/jenis/{id}', [ProductApiController::class, 'getJenis']);
        Route::put('/jenis/{id}', [ProductApiController::class, 'updateJenis']);
        Route::delete('/jenis/{id}', [ProductApiController::class, 'deleteJenis']);
        
        // Biaya Desain routes
        Route::get('/biaya-desain', [ProductApiController::class, 'getAllBiayaDesain']);
        Route::post('/biaya-desain', [ProductApiController::class, 'storeBiayaDesain']);
        Route::get('/biaya-desain/{id}', [ProductApiController::class, 'getBiayaDesain']);
        Route::put('/biaya-desain/{id}', [ProductApiController::class, 'updateBiayaDesain']);
        Route::delete('/biaya-desain/{id}', [ProductApiController::class, 'deleteBiayaDesain']);
    });
});