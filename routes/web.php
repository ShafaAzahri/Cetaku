<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;

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
    
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});