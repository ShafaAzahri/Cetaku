<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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
    return view('auth.login');
})->name('auth.login');

// Authentication routes
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

// Password reset route (hanya untuk tampilan, tidak perlu implementasi lengkap)
Route::get('/password/reset', function () {
    return view('welcome');
})->name('password.request');

// Admin routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');
    
    Route::get('/product-manager', function () {
        return view('admin.product-manager');
    })->name('product-manager');
    
    // Route lain untuk admin
});

// Superadmin routes
Route::prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('superadmin.dashboard');
    })->name('dashboard');
    
    // Route lain untuk superadmin
});

// User routes
Route::prefix('user')->name('user.')->group(function () {
    Route::get('/welcome', function () {
        return view('welcome');
    })->name('welcome');
    
    // Route lain untuk user biasa
});