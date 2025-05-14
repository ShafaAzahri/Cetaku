<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\WelcomeController;

// Admin controllers
use App\Http\Controllers\Admin\ProductManagerController;
use App\Http\Controllers\Admin\PesananManagerController;
use App\Http\Controllers\Admin\OperatorController as AdminOperatorController;
use App\Http\Controllers\Admin\MesinController;
use App\Http\Controllers\Admin\ProsesProduksiController;

// Super Admin controllers
use App\Http\Controllers\SuperAdmin\AdminController as SuperAdminController;
use App\Http\Controllers\SuperAdmin\UserController;
use App\Http\Controllers\SuperAdmin\OperatorController;
use App\Http\Controllers\SuperAdmin\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Route halaman utama (welcome page)
Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
});

// Logout route (needs auth)
Route::middleware(['auth.check'])->group(function() {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout.get');
});

// Regular user routes
Route::middleware(['auth.check', 'role:user'])->group(function() {
    // Redirect user/welcome to home page
    Route::get('/user/welcome', function() {
        return redirect()->route('welcome');
    })->name('user.welcome');
    
    // Add more user routes here if needed
});

// Admin & Super Admin shared routes
Route::prefix('admin')->name('admin.')->middleware(['auth.check', 'role:admin,super_admin'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Product Manager
    Route::get('/product-manager', [ProductManagerController::class, 'index'])->name('product-manager');
    
    // Item CRUD
    Route::post('/items', [ProductManagerController::class, 'storeItem'])->name('items.store');
    Route::post('/items/{id}', [ProductManagerController::class, 'updateItem'])->name('items.update');
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

    // Operator Management
    Route::get('/operators', [AdminOperatorController::class, 'index'])->name('operators.index');
    Route::get('/operators/{id}', [AdminOperatorController::class, 'show'])->name('operators.show');
    Route::put('/operators/{id}/status', [AdminOperatorController::class, 'updateStatus'])->name('operators.update-status');
    
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

// Super Admin specific routes
Route::prefix('superadmin')->name('superadmin.')->middleware(['auth.check', 'role:super_admin'])->group(function() {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Admin Management
    Route::get('/admin', [SuperAdminController::class, 'index'])->name('admin.index');
    Route::get('/admin/create', [SuperAdminController::class, 'create'])->name('admin.create');
    Route::post('/admin', [SuperAdminController::class, 'store'])->name('admin.store');
    Route::get('/admin/{id}', [SuperAdminController::class, 'show'])->name('admin.show');
    Route::get('/admin/{id}/edit', [SuperAdminController::class, 'edit'])->name('admin.edit');
    Route::put('/admin/{id}', [SuperAdminController::class, 'update'])->name('admin.update');
    Route::delete('/admin/{id}', [SuperAdminController::class, 'destroy'])->name('admin.destroy');
    Route::post('/admin/{id}/reset-password', [SuperAdminController::class, 'resetPassword'])->name('admin.reset-password');
    
    // User Management
    Route::get('/user', [UserController::class, 'index'])->name('user.index');
    Route::get('/user/{id}', [UserController::class, 'show'])->name('user.show');
    Route::get('/user/{id}/edit', [UserController::class, 'edit'])->name('user.edit');
    Route::put('/user/{id}', [UserController::class, 'update'])->name('user.update');
    Route::delete('/user/{id}', [UserController::class, 'destroy'])->name('user.destroy');
    Route::post('/user/{id}/reset-password', [UserController::class, 'resetPassword'])->name('user.reset-password');
    Route::get('/user/{id}/order-history', [UserController::class, 'orderHistory'])->name('user.order-history');
    
    // Operator Management
    Route::get('/operator', [OperatorController::class, 'index'])->name('operator.index');
    Route::get('/operator/create', [OperatorController::class, 'create'])->name('operator.create');
    Route::post('/operator', [OperatorController::class, 'store'])->name('operator.store');
    Route::get('/operator/{id}', [OperatorController::class, 'show'])->name('operator.show');
    Route::get('/operator/{id}/edit', [OperatorController::class, 'edit'])->name('operator.edit');
    Route::put('/operator/{id}', [OperatorController::class, 'update'])->name('operator.update');
    Route::delete('/operator/{id}', [OperatorController::class, 'destroy'])->name('operator.destroy');
    Route::put('/operator/{id}/status', [OperatorController::class, 'updateStatus'])->name('operator.update-status');
    Route::get('/operator/{id}/work-history', [OperatorController::class, 'workHistory'])->name('operator.work-history');
});

// Debug routes (only for development)
if (env('APP_DEBUG', false)) {
    Route::get('/debug/session', function() {
        return response()->json(session()->all());
    });
    
    Route::get('/debug/routes', function() {
        $routes = collect(Route::getRoutes())->map(function ($route) {
            return [
                'method' => implode('|', $route->methods()),
                'uri' => $route->uri(),
                'name' => $route->getName(),
                'action' => $route->getActionName(),
                'middleware' => implode(', ', $route->middleware()),
            ];
        });
        
        return response()->json($routes);
    });
}