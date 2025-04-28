<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\ProductManagerController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application.
|
*/

// Halaman utama
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Auth routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    
    // Ini hanya placeholder untuk route reset password
    Route::get('/forgot-password', function() {
        return view('auth.forgot_password');
    })->name('password.request');
});

// Dashboard & Authenticated Routes
Route::middleware('auth')->group(function () {
    // Dashboard route restricted to admin role
    Route::get('/dashboard', function() {
        // Check if user has admin role - fixed to use Auth facade
        if (Auth::check() && Auth::user()->role && Auth::user()->role->nama_role !== 'admin') {
            return redirect()->route('welcome');
        }
        return view('admin.dashboard');
    })->name('dashboard');
    
    // Admin Routes
    Route::prefix('admin')->group(function () {
        // Product Manager - All in one page
        Route::controller(ProductManagerController::class)->group(function () {
            // Main page
            Route::get('/products', 'index')->name('admin.product-manager');
            
            // Items management
            Route::post('/products/items', 'storeItem')->name('admin.items.store');
            Route::put('/products/items/{id}', 'updateItem')->name('admin.items.update');
            Route::delete('/products/items/{id}', 'deleteItem')->name('admin.items.destroy');
            
            // Bahans management
            Route::post('/products/bahans', 'storeBahan')->name('admin.bahans.store');
            Route::put('/products/bahans/{id}', 'updateBahan')->name('admin.bahans.update');
            Route::delete('/products/bahans/{id}', 'deleteBahan')->name('admin.bahans.destroy');
            
            // Ukurans management
            Route::post('/products/ukurans', 'storeUkuran')->name('admin.ukurans.store');
            Route::put('/products/ukurans/{id}', 'updateUkuran')->name('admin.ukurans.update');
            Route::delete('/products/ukurans/{id}', 'deleteUkuran')->name('admin.ukurans.destroy');
            
            // Jenis management
            Route::post('/products/jenis', 'storeJenis')->name('admin.jenis.store');
            Route::put('/products/jenis/{id}', 'updateJenis')->name('admin.jenis.update');
            Route::delete('/products/jenis/{id}', 'deleteJenis')->name('admin.jenis.destroy');
            
            // Biaya Desain management
            Route::post('/products/biaya-desain', 'storeBiayaDesain')->name('admin.biaya-desain.store');
            Route::put('/products/biaya-desain/{id}', 'updateBiayaDesain')->name('admin.biaya-desain.update');
            Route::delete('/products/biaya-desain/{id}', 'deleteBiayaDesain')->name('admin.biaya-desain.destroy');
        });
    });
    
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});