<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\ProductManagerController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Log;

// Public routes
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout.get');

// Password reset route (placeholder)
Route::get('/password/reset', function () {
    return view('auth.login');
})->name('password.request');

// Debug route untuk memeriksa data sesi
Route::get('/debug/session', function () {
    Log::info('Session debug', ['session' => session()->all()]);
    return response()->json([
        'api_token' => session()->has('api_token'),
        'user' => session('user'),
        'expires_at' => session('expires_at')
    ]);
});

// Admin routes dengan middleware
Route::prefix('admin')->name('admin.')->middleware(['auth.check', 'admin'])->group(function () {
    // Dashboard route
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Product Manager Routes
    Route::get('/product-manager', [ProductManagerController::class, 'index'])->name('product-manager');
    
    // Item Routes
    Route::post('/items', [ProductManagerController::class, 'storeItem'])->name('items.store');
    Route::get('/items/{id}/edit', [ProductManagerController::class, 'editItem'])->name('items.edit');
    Route::put('/items/{id}', [ProductManagerController::class, 'updateItem'])->name('items.update');
    Route::delete('/items/{id}', [ProductManagerController::class, 'destroyItem'])->name('items.destroy');
    
    // Bahan Routes
    Route::post('/bahans', [ProductManagerController::class, 'storeBahan'])->name('bahans.store');
    Route::get('/bahans/{id}/edit', [ProductManagerController::class, 'editBahan'])->name('bahans.edit');
    Route::put('/bahans/{id}', [ProductManagerController::class, 'updateBahan'])->name('bahans.update');
    Route::delete('/bahans/{id}', [ProductManagerController::class, 'destroyBahan'])->name('bahans.destroy');
    
    // Ukuran Routes
    Route::post('/ukurans', [ProductManagerController::class, 'storeUkuran'])->name('ukurans.store');
    Route::get('/ukurans/{id}/edit', [ProductManagerController::class, 'editUkuran'])->name('ukurans.edit');
    Route::put('/ukurans/{id}', [ProductManagerController::class, 'updateUkuran'])->name('ukurans.update');
    Route::delete('/ukurans/{id}', [ProductManagerController::class, 'destroyUkuran'])->name('ukurans.destroy');
    
    // Jenis Routes
    Route::post('/jenis', [ProductManagerController::class, 'storeJenis'])->name('jenis.store');
    Route::get('/jenis/{id}/edit', [ProductManagerController::class, 'editJenis'])->name('jenis.edit');
    Route::put('/jenis/{id}', [ProductManagerController::class, 'updateJenis'])->name('jenis.update');
    Route::delete('/jenis/{id}', [ProductManagerController::class, 'destroyJenis'])->name('jenis.destroy');
    
    // Biaya Desain Routes
    Route::post('/biaya-desain', [ProductManagerController::class, 'storeBiayaDesain'])->name('biaya-desain.store');
    Route::get('/biaya-desain/{id}/edit', [ProductManagerController::class, 'editBiayaDesain'])->name('biaya-desain.edit');
    Route::put('/biaya-desain/{id}', [ProductManagerController::class, 'updateBiayaDesain'])->name('biaya-desain.update');
    Route::delete('/biaya-desain/{id}', [ProductManagerController::class, 'destroyBiayaDesain'])->name('biaya-desain.destroy');
});

// User routes
Route::prefix('user')->name('user.')->middleware(['auth.check'])->group(function () {
    Route::get('/welcome', function () {
        return view('welcome');
    })->name('welcome');
});

// Superadmin routes
Route::prefix('superadmin')->name('superadmin.')->middleware(['auth.check', 'role:super_admin'])->group(function () {
    Route::get('/dashboard', function () {
        return view('superadmin.dashboard');
    })->name('dashboard');
});