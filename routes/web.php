<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\Admin\ProductManagerController;
use App\Http\Controllers\Admin\BahanController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Route halaman utama
Route::get('/', function () {
    return redirect()->route('login');
});

// Route autentikasi
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout.get');

// Route untuk user biasa
Route::group(['prefix' => 'user', 'middleware' => ['auth.check', 'role:user'], 'as' => 'user.'], function() {
    Route::get('/welcome', [UserController::class, 'welcome'])->name('welcome');
});

// Product Manager Routes di Admin
Route::prefix('admin')->name('admin.')->middleware(['auth.check', 'role:admin,super_admin'])->group(function () {
    // Dashboard route
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Product Manager
    Route::get('/product-manager', [ProductManagerController::class, 'index'])->name('product-manager');
    
    // Item CRUD
    Route::post('/items', [ProductManagerController::class, 'storeItem'])->name('items.store');
    Route::put('/items/{id}', [ProductManagerController::class, 'updateItem'])->name('items.update');
    Route::delete('/items/{id}', [ProductManagerController::class, 'destroyItem'])->name('items.destroy');
    
    // Bahan CRUD
    Route::post('/bahans', [ProductManagerController::class, 'storeBahan'])->name('bahans.store');
    Route::put('/bahans/{id}', [ProductManagerController::class, 'updateBahan'])->name('bahans.update');
    Route::delete('/bahans/{id}', [ProductManagerController::class, 'destroyBahan'])->name('bahans.destroy');
    
    // Jenis CRUD
    Route::post('/jenis', [ProductManagerController::class, 'storeJenis'])->name('jenis.store');
    Route::put('/jenis/{id}', [ProductManagerController::class, 'updateJenis'])->name('jenis.update');
    Route::delete('/jenis/{id}', [ProductManagerController::class, 'destroyJenis'])->name('jenis.destroy');
    
    // Ukuran CRUD
    Route::post('/ukurans', [ProductManagerController::class, 'storeUkuran'])->name('ukurans.store');
    Route::put('/ukurans/{id}', [ProductManagerController::class, 'updateUkuran'])->name('ukurans.update');
    Route::delete('/ukurans/{id}', [ProductManagerController::class, 'destroyUkuran'])->name('ukurans.destroy');
    
    // Biaya Desain CRUD
    Route::post('/biaya-desains', [ProductManagerController::class, 'storeBiayaDesain'])->name('biaya-desains.store');
    Route::put('/biaya-desains/{id}', [ProductManagerController::class, 'updateBiayaDesain'])->name('biaya-desains.update');
    Route::delete('/biaya-desains/{id}', [ProductManagerController::class, 'destroyBiayaDesain'])->name('biaya-desains.destroy');
});

// Route untuk super admin
Route::group(['prefix' => 'superadmin', 'middleware' => ['auth.check', 'role:super_admin'], 'as' => 'superadmin.'], function() {
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
});