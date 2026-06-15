<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\User\WelcomeController;
use App\Http\Controllers\User\ProfileController;

use App\Http\Controllers\User\pesanan;
use App\Http\Controllers\User\CheckoutController;
use App\Http\Controllers\User\PaymentWeb;
use App\Http\Controllers\User\ReviewController;

// Admin Controllers
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ProductManagerController;
use App\Http\Controllers\Admin\PesananManagerController;
use App\Http\Controllers\Admin\OperatorController as AdminOperatorController;
use App\Http\Controllers\Admin\MesinController;
use App\Http\Controllers\Admin\EkspedisiController;

// Super Admin Controllers
use App\Http\Controllers\SuperAdmin\AdminController as ManagementAdmin;
use App\Http\Controllers\SuperAdmin\UserController;
use App\Http\Controllers\SuperAdmin\OperatorController;
use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\SuperAdmin\LaporanController;
use App\Http\Controllers\SuperAdmin\PengaturanController;

use App\Http\Controllers\User\PesananWebController;
use App\Http\Controllers\User\SearchController;
use App\Http\Controllers\Api\User\KeranjangApiController;
use App\Http\Controllers\GoogleLoginController;

// Super Admin controllers
use App\Http\Controllers\User\ProdukListController;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\User\ResetPasswordController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/test-email', function () {
    Mail::raw('Ini adalah email test dari Laravel.', function ($msg) {
        $msg->to('azshafa95@gmail.com')->subject('Test Email');
    });

    return 'Email test dikirim!';
});


// Halaman utama
Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

Route::get('/alamat', [ProfileController::class, 'getAlamat'])->name('Alamat.index');
Route::get('/alamat/{id}', [ProfileController::class, 'getAlamatDetail'])->name('Alamat.show');
Route::post('/alamat', [ProfileController::class, 'addAddress'])->name('Alamat.store');
Route::put('/alamat/{id}', [ProfileController::class, 'updateAddress'])->name('Alamat.update');
Route::delete('/alamat/{id}', [ProfileController::class, 'deleteAddress'])->name('Alamat.delete');

//search
Route::get('/search', [SearchController::class, 'index'])->name('search');

Route::middleware('auth:sanctum')->group(function () {
    Route::delete('/keranjang/clear', [KeranjangApiController::class, 'clear']);
});

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
    Route::get('/product/{id}', [App\Http\Controllers\User\ProductController::class, 'show'])->name('product.detail');
    Route::get('/login/google', [GoogleLoginController::class, 'redirectToGoogle'])->name('login.google');
    Route::get('/login/google/callback', [GoogleLoginController::class, 'handleGoogleCallback']);
    Route::get('/lupa-password', [ResetPasswordController::class, 'showEmailForm'])->name('password.email.form');
    Route::post('/lupa-password', [ResetPasswordController::class, 'submitEmail'])->name('password.email.submit');

    Route::get('/verifikasi-otp', [ResetPasswordController::class, 'showOtpForm'])->name('password.otp.form');
    Route::post('/verifikasi-otp', [ResetPasswordController::class, 'submitOtp'])->name('password.otp.submit');

    Route::get('/reset-password', [ResetPasswordController::class, 'showResetForm'])->name('password.reset.form');
    Route::post('/reset-password', [ResetPasswordController::class, 'submitReset'])->name('password.reset.submit');

});

// Auth (logout)
Route::middleware(['auth.check'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout.get');
});

// User routes
Route::middleware(['auth.check', 'role:user'])->group(function () {
    // Redirect user/welcome to home page
    Route::get('/user/welcome', function () {
        return redirect()->route('welcome');
    })->name('user.welcome');

    Route::get('/profile', [ProfileController::class, 'showProfile'])->name('user.profile');
    // Menyimpan perubahan password
    Route::post('/profile/update', [ProfileController::class, 'updateProfile'])->name('user.profile.update');
    Route::post('/profile/update-password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');

    Route::post('/alamat', [ProfileController::class, 'addAddress'])->name('Alamat.store');

    Route::get('/keranjang', [App\Http\Controllers\User\KeranjangController::class, 'index'])->name('keranjang');
    Route::post('/keranjang/add', [App\Http\Controllers\User\KeranjangController::class, 'addToCart'])->name('keranjang.add');
    Route::put('/keranjang/{id}/quantity', [App\Http\Controllers\User\KeranjangController::class, 'updateQuantity'])->name('keranjang.update-quantity');
    Route::post('/keranjang/{id}/upload-design', [App\Http\Controllers\User\KeranjangController::class, 'uploadDesign'])->name('keranjang.upload-design');
    Route::delete('/keranjang/{id}', [App\Http\Controllers\User\KeranjangController::class, 'removeItem'])->name('keranjang.remove');
    Route::delete('/keranjang', [App\Http\Controllers\User\KeranjangController::class, 'clearCart'])->name('keranjang.clear');
    Route::get('/keranjang/count', [App\Http\Controllers\User\KeranjangController::class, 'getCartCount'])->name('keranjang.count');


    Route::get('/pesanan', [pesanan::class, 'index'])->name('pesanan');
    Route::get('/produk', [pesanan::class, 'allproduk'])->name('produk-all');
    Route::get('/produk/{id}', [ProdukListController::class, 'show'])->name('produk.show');
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout/payment', [CheckoutController::class, 'checkoutTerpilih'])->name('checkout.terpilih');
    Route::post('/checkout/process', [PaymentWeb::class, 'checkoutPayment'])->name('checkout.payment');


    Route::get('/user/pesanan', [PesananWebController::class, 'index'])->name('user.pesanan');
    Route::get('/pesanan/detail', [PesananWebController::class, 'show'])->name('user.pesanan.detail');
    Route::delete('/pesanan/{id}/cancel', [PesananWebController::class, 'cancel'])->name('user.pesanan.cancel');    

    // Routes untuk guest users (dapat mengakses produk tanpa login)
    Route::get('/produk', [ProdukListController::class, 'index'])->name('produk-all');
    Route::get('/produk/{id}', [ProdukListController::class, 'show'])->name('produk.show');
    Route::get('/pesanan/show', [PesananWebController::class, 'show'])->name('pesanan.show');

    // Jika ingin menambahkan API endpoint untuk sorting via AJAX (opsional)
    Route::get('/api/produk', [ProdukListController::class, 'apiIndex'])->name('produk.api');

    // Route untuk search dengan sorting (jika diperlukan)
    Route::get('/produk/search', [ProdukListController::class, 'search'])->name('produk.search');

    Route::get('/detail-pesanan', [PesananWebController::class, 'show'])->name('user.pesanan.detail');


    //review
    Route::get('/ulasan/{id}', [ReviewController::class, 'form'])->name('review.form');
    Route::post('/ulasan/{id}', [ReviewController::class, 'submit'])->name('review.submit');

    // Add more user routes here if needed
});

// Admin & Super Admin routes
Route::prefix('admin')->name('admin.')->middleware(['auth.check', 'role:admin,super_admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Produk Manager
    Route::get('/product-manager', [ProductManagerController::class, 'index'])->name('product-manager');

    Route::post('/kategoris', [ProductManagerController::class, 'storeKategori'])->name('kategoris.store');
    Route::put('/kategoris/{id}', [ProductManagerController::class, 'updateKategori'])->name('kategoris.update');
    Route::delete('/kategoris/{id}', [ProductManagerController::class, 'destroyKategori'])->name('kategoris.destroy');

    Route::post('/items', [ProductManagerController::class, 'storeItem'])->name('items.store');
    Route::put('/items/{id}', [ProductManagerController::class, 'updateItem'])->name('items.update');
    Route::delete('/items/{id}', [ProductManagerController::class, 'destroyItem'])->name('items.destroy');

    Route::post('/bahans', [ProductManagerController::class, 'storeBahan'])->name('bahans.store');
    Route::put('/bahans/{id}', [ProductManagerController::class, 'updateBahan'])->name('bahans.update');
    Route::delete('/bahans/{id}', [ProductManagerController::class, 'destroyBahan'])->name('bahans.destroy');

    Route::post('/jenis', [ProductManagerController::class, 'storeJenis'])->name('jenis.store');
    Route::put('/jenis/{id}', [ProductManagerController::class, 'updateJenis'])->name('jenis.update');
    Route::delete('/jenis/{id}', [ProductManagerController::class, 'destroyJenis'])->name('jenis.destroy');

    Route::post('/ukurans', [ProductManagerController::class, 'storeUkuran'])->name('ukurans.store');
    Route::put('/ukurans/{id}', [ProductManagerController::class, 'updateUkuran'])->name('ukurans.update');
    Route::delete('/ukurans/{id}', [ProductManagerController::class, 'destroyUkuran'])->name('ukurans.destroy');

    Route::post('/biaya-desains', [ProductManagerController::class, 'storeBiayaDesain'])->name('biaya-desains.store');
    Route::put('/biaya-desains/{id}', [ProductManagerController::class, 'updateBiayaDesain'])->name('biaya-desains.update');
    Route::delete('/biaya-desains/{id}', [ProductManagerController::class, 'destroyBiayaDesain'])->name('biaya-desains.destroy');

    // Pesanan
    Route::get('/pesanan', [PesananManagerController::class, 'index'])->name('pesanan.index');
    Route::get('/pesanan/{id}', [PesananManagerController::class, 'show'])->name('pesanan.show');
    Route::put('/pesanan/{id}/status', [PesananManagerController::class, 'updateStatus'])->name('pesanan.update-status');
    Route::post('/pesanan/{id}/assign-production', [PesananManagerController::class, 'assignProduction'])->name('pesanan.assign-production');
    Route::post('/pesanan/{id}/complete-production', [PesananManagerController::class, 'completeProduction'])->name('pesanan.complete-production');
    Route::post('/pesanan/{id}/confirm-shipment', [PesananManagerController::class, 'confirmShipment'])->name('pesanan.confirm-shipment');
    Route::post('/pesanan/{id}/confirm-pickup', [PesananManagerController::class, 'confirmPickup'])->name('pesanan.confirm-pickup');
    Route::post('/pesanan/{id}/upload-desain', [PesananManagerController::class, 'uploadDesain'])->name('pesanan.upload-desain');
    Route::post('/pesanan/{id}/cancel', [PesananManagerController::class, 'cancelOrder'])->name('pesanan.cancel');
    Route::post('/pesanan/{id}/upload-resi', [PesananManagerController::class, 'uploadResi'])->name('pesanan.upload-resi');
    Route::post('/pesanan/{id}/upload-bukti', [PesananManagerController::class, 'uploadBukti'])->name('pesanan.upload-bukti');


    // Operator
    Route::get('/operators', [AdminOperatorController::class, 'index'])->name('operators.index');
    Route::get('/operators/{id}', [AdminOperatorController::class, 'show'])->name('operators.show');
    Route::put('/operators/{id}/status', [AdminOperatorController::class, 'updateStatus'])->name('operators.update-status');

    // Mesin
    Route::get('/mesins', [MesinController::class, 'index'])->name('mesins.index');
    Route::get('/mesins/{id}', [MesinController::class, 'show'])->name('mesins.show');
    Route::put('/mesins/{id}/status', [MesinController::class, 'updateStatus'])->name('mesins.update-status');

    Route::get('/ekspedisi', [EkspedisiController::class, 'index'])->name('ekspedisi.index');
});

// Super Admin
Route::prefix('superadmin')->name('superadmin.')->middleware(['auth.check', 'role:super_admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Admin
    Route::resource('admin', ManagementAdmin::class)->except(['edit', 'update']);
    Route::get('/admin/{id}/edit', [ManagementAdmin::class, 'edit'])->name('admin.edit');
    Route::put('/admin/{id}', [ManagementAdmin::class, 'update'])->name('admin.update');
    Route::post('/admin/{id}/reset-password', [ManagementAdmin::class, 'resetPassword'])->name('admin.reset-password');

    // User
    Route::resource('user', UserController::class)->except(['edit', 'update']);
    Route::get('/user/{id}/edit', [UserController::class, 'edit'])->name('user.edit');
    Route::put('/user/{id}', [UserController::class, 'update'])->name('user.update');
    Route::post('/user/{id}/reset-password', [UserController::class, 'resetPassword'])->name('user.reset-password');
    Route::get('/user/{id}/order-history', [UserController::class, 'orderHistory'])->name('user.order-history');

    // Operator
    Route::resource('operator', OperatorController::class)->except(['edit', 'update']);
    Route::get('/operator/{id}/edit', [OperatorController::class, 'edit'])->name('operator.edit');
    Route::put('/operator/{id}', [OperatorController::class, 'update'])->name('operator.update');
    Route::put('/operator/{id}/status', [OperatorController::class, 'updateStatus'])->name('operator.update-status');
    Route::get('/operator/{id}/work-history', [OperatorController::class, 'workHistory'])->name('operator.work-history');

    // Laporan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/export', [LaporanController::class, 'exportExcel'])->name('laporan.export');

    // Pengaturan
    Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index');
    Route::post('/pengaturan/update', [PengaturanController::class, 'update'])->name('pengaturan.update');
});

