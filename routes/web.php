<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\Admin\ProductManagerController;
use App\Http\Controllers\Admin\PesananController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Route halaman utama (welcome page) - Menangani semua pengunjung
Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

// Route autentikasi
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

// Logout route memerlukan autentikasi
Route::middleware(['auth.check'])->group(function() {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout.get');
});

// Route untuk user yang sudah login (jika diperlukan fitur khusus user)
Route::middleware(['auth.check', 'role:user'])->group(function() {
    // Redirects user/welcome ke halaman utama
    Route::get('/user/welcome', function() {
        return redirect()->route('welcome');
    })->name('user.welcome');
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

// Route untuk Pesanan - semua route ini berada di dalam group admin middleware
Route::prefix('admin')->name('admin.')->middleware(['auth.check', 'role:admin,super_admin'])->group(function () {
    // Pesanan routes
    Route::get('/pesanan', [App\Http\Controllers\Admin\PesananController::class, 'index'])->name('pesanan.index');
    Route::get('/pesanan/{id}', [App\Http\Controllers\Admin\PesananController::class, 'show'])->name('pesanan.show');
    Route::post('/pesanan/{id}/status', [App\Http\Controllers\Admin\PesananController::class, 'updateStatus'])->name('pesanan.update-status');
    Route::get('/pesanan/{id}/print', [App\Http\Controllers\Admin\PesananController::class, 'printInvoice'])->name('pesanan.print');
    Route::post('/pesanan/{id}/upload', [App\Http\Controllers\Admin\PesananController::class, 'uploadDesain'])->name('pesanan.upload');
    Route::post('/pesanan/{id}/cancel', [App\Http\Controllers\Admin\PesananController::class, 'cancel'])->name('pesanan.cancel');
    Route::post('/pesanan/{id}/complete', [App\Http\Controllers\Admin\PesananController::class, 'complete'])->name('pesanan.complete');
    Route::post('/pesanan/{id}/confirm-pickup', [App\Http\Controllers\Admin\PesananController::class, 'confirmPickup'])->name('pesanan.confirm-pickup');
    Route::post('/pesanan/{id}/update-tracking', [App\Http\Controllers\Admin\PesananController::class, 'updateTracking'])->name('pesanan.update-tracking');
    Route::post('/pesanan/{id}/confirm-shipment', [App\Http\Controllers\Admin\PesananController::class, 'confirmShipment'])->name('pesanan.confirm-shipment');
    Route::post('/pesanan/{id}/notification', [App\Http\Controllers\Admin\PesananController::class, 'sendNotification'])->name('pesanan.send-notification');
    Route::get('/pesanan/{id}/history', [App\Http\Controllers\Admin\PesananController::class, 'history'])->name('pesanan.history');
    Route::get('/pesanan-dashboard', [App\Http\Controllers\Admin\PesananController::class, 'dashboard'])->name('pesanan.dashboard');
    
    // API untuk AJAX requests pada halaman pesanan
    Route::get('/api/pesanan', [App\Http\Controllers\Admin\PesananController::class, 'getDataForAjax'])->name('api.pesanan');
});

// Route untuk super admin
Route::prefix('superadmin')->name('superadmin.')->middleware(['auth.check', 'role:super_admin'])->group(function() {
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
});