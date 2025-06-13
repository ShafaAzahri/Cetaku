<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthApiController;
use App\Http\Controllers\API\Admin\ItemApiController;
use App\Http\Controllers\API\Admin\BahanApiController;
use App\Http\Controllers\API\Admin\JenisApiController;
use App\Http\Controllers\API\Admin\UkuranApiController;
use App\Http\Controllers\API\Admin\BiayaDesainApiController;
use App\Http\Controllers\API\Admin\PesananAdminController;
use App\Http\Controllers\API\Admin\OperatorApiController;
use App\Http\Controllers\API\Admin\MesinApiController;
use App\Http\Controllers\API\Admin\ProsesOperatorMesinApi;
use App\Http\Controllers\API\Admin\KategoriApiController;
use App\Http\Controllers\API\SuperAdmin\LaporanApiController;
use App\Http\Controllers\API\SuperAdmin\PengaturanApiController;
// use App\Http\Controllers\PaymentController; 

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Auth Routes (Public)
Route::post('/login', [AuthApiController::class, 'login']);
Route::post('/register', [AuthApiController::class, 'register']);

// Route autentikasi publik (tidak memerlukan autentikasi)
Route::prefix('auth')->group(function() {
    Route::get('/user', [AuthApiController::class, 'getUserByToken']);
    Route::post('/logout', [AuthApiController::class, 'logout']);
});

// Alamat API routes (dengan middleware api.auth)
Route::middleware('api.auth')->prefix('alamats')->group(function() {
    Route::get('/', 'App\Http\Controllers\User\AlamatApiController@index');
    Route::post('/', 'App\Http\Controllers\API\User\AlamatApiController@store');
    Route::get('/{id}', 'App\Http\Controllers\User\AlamatApiController@show');
    Route::put('/{id}', 'App\Http\Controllers\API\User\AlamatApiController@update');
    Route::delete('/{id}', 'App\Http\Controllers\API\User\AlamatApiController@destroy');
});
// Route::middleware('api.auth')->prefix('payments')->group(function() {
//     Route::post('/qris', [PaymentController::class, 'createQrisPayment']);
//     // Anda bisa menambahkan rute pembayaran lain di sini nanti, misal:
//     // Route::post('/credit-card', [PaymentController::class, 'createCreditCardPayment']);
// });
// Routes untuk item (Public GET)
Route::get('/items', [ItemApiController::class, 'index']);
Route::get('/items/terlaris', [ItemApiController::class, 'index']);
Route::get('/items/{id}', [ItemApiController::class, 'show']);

// Route untuk bahan (GET - publik)
Route::get('/bahans', [BahanApiController::class, 'index']);
Route::get('/bahans/{id}', [BahanApiController::class, 'show']);
    Route::get('/bahans/{id}/items', [BahanApiController::class, 'getItemsByBahan']);

// Route untuk jenis (GET - publik)
Route::get('/jenis', [JenisApiController::class, 'index']);
Route::get('/jenis/{id}', [JenisApiController::class, 'show']);
Route::get('/jenis/{id}/items', [JenisApiController::class, 'getItemsByJenis']);

// Route untuk ukuran (GET - publik)
Route::get('/ukurans', [UkuranApiController::class, 'index']);
Route::get('/ukurans/{id}', [UkuranApiController::class, 'show']);
Route::get('/ukurans/{id}/items', [UkuranApiController::class, 'getItemsByUkuran']);

// Route untuk biaya desain (GET - publik)
Route::get('/biaya-desains', [BiayaDesainApiController::class, 'index']);
Route::get('/biaya-desains/{id}', [BiayaDesainApiController::class, 'show']);

Route::get('/kategoris', [KategoriApiController::class, 'index']);
Route::get('/kategoris/{id}', [KategoriApiController::class, 'show']);
Route::get('/kategoris/{id}/items', [KategoriApiController::class, 'getItemsByKategori']);

// Route untuk health check
Route::get('/health', function() {
    return response()->json(['status' => 'ok', 'timestamp' => now()]);
});

// Route yang memerlukan autentikasi admin
Route::middleware('api.admin')->group(function() {
    // dashboard
    Route::get('/pesanan/stats', [PesananAdminController::class, 'getPesananStats']);

    // Item routes (admin only)
    Route::post('/items', [ItemApiController::class, 'store']);
    Route::put('/items/{id}', [ItemApiController::class, 'update']);
    Route::delete('/items/{id}', [ItemApiController::class, 'destroy']);
    
    // Bahan routes (admin only)
    Route::post('/bahans', [BahanApiController::class, 'store']);
    Route::put('/bahans/{id}', [BahanApiController::class, 'update']);
    Route::delete('/bahans/{id}', [BahanApiController::class, 'destroy']);
    
    // Jenis routes (admin only)
    Route::post('/jenis', [JenisApiController::class, 'store']);
    Route::put('/jenis/{id}', [JenisApiController::class, 'update']);
    Route::delete('/jenis/{id}', [JenisApiController::class, 'destroy']);
    
    // Ukuran routes (admin only)
    Route::post('/ukurans', [UkuranApiController::class, 'store']);
    Route::put('/ukurans/{id}', [UkuranApiController::class, 'update']);
    Route::delete('/ukurans/{id}', [UkuranApiController::class, 'destroy']);
    
    // Biaya desain routes (admin only)
    Route::post('/biaya-desains', [BiayaDesainApiController::class, 'store']);
    Route::put('/biaya-desains/{id}', [BiayaDesainApiController::class, 'update']);
    Route::delete('/biaya-desains/{id}', [BiayaDesainApiController::class, 'destroy']);

    Route::post('/kategoris', [KategoriApiController::class, 'store']);
    Route::put('/kategoris/{id}', [KategoriApiController::class, 'update']);
    Route::delete('/kategoris/{id}', [KategoriApiController::class, 'destroy']);
    
    // Statistik
    Route::get('/admin/pesanan/statistics', [PesananAdminController::class, 'getStatistics']);
    
    // Manajemen Pesanan Admin routes
    Route::get('/admin/pesanan', [PesananAdminController::class, 'index']);
    Route::get('/admin/pesanan/{id}', [PesananAdminController::class, 'show']);
    Route::put('/admin/pesanan/{id}/status', [PesananAdminController::class, 'updateStatus']);
    Route::post('/admin/pesanan/{id}/assign-production', [PesananAdminController::class, 'assignProduction']);
    Route::post('/admin/pesanan/{id}/complete-production', [PesananAdminController::class, 'completeProduction']);
    Route::post('/admin/pesanan/{id}/confirm-shipment', [PesananAdminController::class, 'confirmShipment']);
    Route::post('/admin/pesanan/{id}/confirm-pickup', [PesananAdminController::class, 'confirmPickup']);
    Route::post('/admin/pesanan/{id}/upload-desain', [PesananAdminController::class, 'uploadDesain']);
    Route::post('/admin/pesanan/{id}/cancel', [PesananAdminController::class, 'cancelOrder']);
    
    // Daftar mesin dan operator
    Route::get('/admin/mesin/available', [PesananAdminController::class, 'getAvailableMachines']);
    Route::get('/admin/operators', [PesananAdminController::class, 'getOperators']);
    
    
    // Operator API routes
    Route::get('/operators', [OperatorApiController::class, 'index']);
    Route::get('/operators/{id}', [OperatorApiController::class, 'show']);
    
    // Mesin API routes
    Route::get('/mesins', [MesinApiController::class, 'index']);
    Route::get('/mesins/{id}', [MesinApiController::class, 'show']);
    Route::put('/mesins/{id}/status', [MesinApiController::class, 'updateStatus']);
});

// Route untuk API Super Admin
Route::middleware(['api.superadmin'])->prefix('superadmin')->group(function() {
    // Admin Management Routes
    Route::apiResource('admins', 'App\Http\Controllers\API\SuperAdmin\AdminManagementApiController');
    Route::post('admins/{id}/reset-password', 'App\Http\Controllers\API\SuperAdmin\AdminManagementApiController@resetPassword');
    
    // User Management Routes
    Route::apiResource('users', 'App\Http\Controllers\API\SuperAdmin\UserManagementApiController');
    Route::post('users/{id}/reset-password', 'App\Http\Controllers\API\SuperAdmin\UserManagementApiController@resetPassword');
    Route::get('users/{id}/order-history', 'App\Http\Controllers\API\SuperAdmin\UserManagementApiController@orderHistory');
    
    // Operator Management Routes
    Route::apiResource('operators', 'App\Http\Controllers\API\SuperAdmin\OperatorManagementApiController');
    Route::put('operators/{id}/status', 'App\Http\Controllers\API\SuperAdmin\OperatorManagementApiController@updateStatus');
    Route::get('operators/{id}/work-history', 'App\Http\Controllers\API\SuperAdmin\OperatorManagementApiController@workHistory');

    Route::get('dashboard/stats', 'App\Http\Controllers\API\SuperAdmin\DashboardApiController@getStats');
    Route::get('/superadmin/sales', [LaporanApiController::class, 'getSalesData']);

    // pengaturan route
    Route::get('/toko-info', [PengaturanApiController::class, 'getTokoInfo']);
});