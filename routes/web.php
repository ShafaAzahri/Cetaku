<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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

// Admin routes with middleware
Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');
    
    Route::get('/product-manager', function () {
        return view('admin.product-manager');
    })->name('product-manager');
});

// Superadmin routes with middleware
Route::middleware(['admin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('superadmin.dashboard');
    })->name('dashboard');
});

// User routes
Route::prefix('user')->name('user.')->group(function () {
    Route::get('/welcome', function () {
        if (!session()->has('api_token')) {
            return redirect()->route('login');
        }
        return view('welcome');
    })->name('welcome');
});