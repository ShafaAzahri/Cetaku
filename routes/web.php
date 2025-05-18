<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\PesananController;
use App\Http\Controllers\Admin\ProsesPesananController;
use App\Http\Controllers\Admin\PelangganController;
use App\Http\Controllers\Admin\ProductManagerController;
use App\Http\Controllers\Admin\PesananManagerController;
use App\Http\Controllers\Admin\OperatorController;
use App\Http\Controllers\Admin\MesinController;
use App\Http\Controllers\Admin\ProsesProduksiController;
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
    
    // Pesanan Management
    Route::get('/pesanan', [PesananManagerController::class, 'index'])->name('pesanan.index');
    Route::get('/pesanan/{id}', [PesananManagerController::class, 'show'])->name('pesanan.show');
    Route::put('/pesanan/{id}/status', [PesananManagerController::class, 'updateStatus'])->name('pesanan.update-status');
    Route::post('/pesanan/{id}/assign-production', [PesananManagerController::class, 'assignProduction'])->name('pesanan.assign-production');
    Route::post('/pesanan/{id}/complete-production', [PesananManagerController::class, 'completeProduction'])->name('pesanan.complete-production');
    Route::post('/pesanan/{id}/confirm-shipment', [PesananManagerController::class, 'confirmShipment'])->name('pesanan.confirm-shipment');
    Route::post('/pesanan/{id}/confirm-pickup', [PesananManagerController::class, 'confirmPickup'])->name('pesanan.confirm-pickup');
    Route::post('/pesanan/{id}/upload-desain', [PesananManagerController::class, 'uploadDesain'])->name('pesanan.upload-desain');
    Route::post('/pesanan/{id}/cancel', [PesananManagerController::class, 'cancelOrder'])->name('pesanan.cancel');

    // Pelanggan Management
   
    Route::get('/pelanggan', [PelangganController::class, 'index'])->name('pelanggan.index');
    Route::get('/admin/pelanggan', [PelangganController::class, 'index'])->name('admin.pelanggan.index');


    // Operator Management
    Route::get('/operators', [OperatorController::class, 'index'])->name('operators.index');
    Route::get('/operators/{id}', [OperatorController::class, 'show'])->name('operators.show');
    Route::put('/operators/{id}/status', [OperatorController::class, 'updateStatus'])->name('operators.update-status');
    
    // Mesin Management
    Route::get('/mesins', [MesinController::class, 'index'])->name('mesins.index');
    Route::get('/mesins/{id}', [MesinController::class, 'show'])->name('mesins.show');
    Route::put('/mesins/{id}/status', [MesinController::class, 'updateStatus'])->name('mesins.update-status');
    
    // Proses Produksi Management
    Route::get('/proses-produksi', [ProsesProduksiController::class, 'index'])->name('proses-produksi.index');
    Route::get('/proses-produksi/status/{status}', [ProsesProduksiController::class, 'showByStatus'])->name('proses-produksi.status');
    Route::get('/proses-produksi/{id}', [ProsesProduksiController::class, 'show'])->name('proses-produksi.show');
    Route::put('/proses-produksi/{id}/status', [ProsesProduksiController::class, 'updateStatus'])->name('proses-produksi.update-status');
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