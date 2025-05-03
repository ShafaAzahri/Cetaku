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

// Debug route for checking session data
Route::get('/debug/session', function () {
    Log::info('Session debug', ['session' => session()->all()]);
    return response()->json([
        'api_token' => session()->has('api_token'),
        'user' => session('user'),
        'expires_at' => session('expires_at')
    ]);
});

// Route sederhana untuk test tanpa middleware
Route::get('/test-product-manager', function() {
    return view('admin.product-manager', [
        'activeTab' => 'items',
        'items' => [],
        'itemsTotal' => 0
    ]);
})->name('test.product-manager');

// Admin routes with clear middleware specification
Route::prefix('admin')->name('admin.')->middleware(['auth.check', 'admin'])->group(function () {
    // Dashboard route
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Product Manager Routes - UBAH MIDDLEWARE DI SINI
    Route::get('/product-manager', [ProductManagerController::class, 'index'])
         ->middleware(['auth.check']) // Gunakan hanya auth.check
         ->name('product-manager');
 });

