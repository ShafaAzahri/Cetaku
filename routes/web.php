<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\PesananController;
use App\Http\Controllers\Admin\ProsesPesananController;
use App\Http\Controllers\Admin\ProductManagerController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\WelcomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Route halaman utama (welcome page)
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

// Route untuk user yang sudah login
Route::middleware(['auth.check', 'role:user'])->group(function() {
    // Redirects user/welcome ke halaman utama
    Route::get('/user/welcome', function() {
        return redirect()->route('welcome');
    })->name('user.welcome');
});

// Route untuk Admin dan Super Admin
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
    
    Route::get('/pesanan', [PesananController::class, 'index'])->name('pesanan.index');
    Route::get('/pesanan/{id}', [PesananController::class, 'show'])->name('pesanan.show')->where('id', '[0-9]+');
    
    // Konfirmasi
    Route::get('/pesanan/{id}/konfirmasi', [PesananController::class, 'konfirmasi'])->name('pesanan.konfirmasi');
    Route::post('/pesanan/{id}/konfirmasi', [PesananController::class, 'prosesKonfirmasi'])->name('pesanan.proses-konfirmasi');
    
    // Proses
    Route::get('/pesanan/{id}/proses', [PesananController::class, 'proses'])->name('pesanan.proses');
    Route::post('/pesanan/{id}/process-print', [PesananController::class, 'prosesPrint'])->name('pesanan.process-print');
    
    // Pengiriman
    Route::get('/pesanan/{id}/kirim', [PesananController::class, 'kirim'])->name('pesanan.kirim');
    Route::post('/pesanan/{id}/kirim', [PesananController::class, 'prosesKirim'])->name('pesanan.proses-kirim');
    
    // Update status
    Route::post('/pesanan/{id}/status', [PesananController::class, 'updateStatus'])->name('pesanan.update-status');
    
    // Konfirmasi tindakan
    Route::post('/pesanan/{id}/confirm-pickup', [PesananController::class, 'confirmPickup'])->name('pesanan.confirm-pickup');
    Route::post('/pesanan/{id}/confirm-delivery', [PesananController::class, 'confirmDelivery'])->name('pesanan.confirm-delivery');
    Route::post('/pesanan/{id}/cancel', [PesananController::class, 'cancel'])->name('pesanan.cancel');
    
    // Detail produk
    Route::get('/pesanan/{id}/produk/{produk_id}', [PesananController::class, 'getDetailProduk'])->name('pesanan.detail-produk');
    Route::post('/pesanan/{id}/upload', [PesananController::class, 'uploadDesain'])->name('pesanan.upload');
});

// Route untuk super admin
Route::prefix('superadmin')->name('superadmin.')->middleware(['auth.check', 'role:super_admin'])->group(function() {
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
});

// Route debug (hanya untuk development)
if (env('APP_DEBUG', false)) {
    Route::get('/debug/session', function() {
        return response()->json(session()->all());
    });
}