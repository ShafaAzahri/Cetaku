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

// Routes untuk item (Public GET)
Route::get('/items', [ItemApiController::class, 'index']);
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

// Route untuk health check
Route::get('/health', function() {
    return response()->json(['status' => 'ok', 'timestamp' => now()]);
});

// Route yang memerlukan autentikasi admin
Route::middleware('api.admin')->group(function() {
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
    
    // Statistik
    Route::get('/admin/pesanan/statistics', [PesananAdminController::class, 'getStatistics']);
    
    // Operator API routes
    Route::get('/operators', [OperatorApiController::class, 'index']);
    Route::get('/operators/{id}', [OperatorApiController::class, 'show']);
    Route::get('/operators/{id}/history', [OperatorApiController::class, 'getHistory']);
    Route::put('/operators/{id}/status', [OperatorApiController::class, 'updateStatus']);
    
    // Mesin API routes
    Route::get('/mesins', [MesinApiController::class, 'index']);
    Route::get('/mesins/{id}', [MesinApiController::class, 'show']);
    Route::get('/mesins/{id}/history', [MesinApiController::class, 'getHistory']);
    Route::put('/mesins/{id}/status', [MesinApiController::class, 'updateStatus']);
    
    // Proses Operator & Mesin API routes
    Route::get('/proses-produksi/aktif', [ProsesOperatorMesinApi::class, 'getActiveProcesses']);
    Route::get('/proses-produksi/{id}', [ProsesOperatorMesinApi::class, 'show']);
    Route::put('/proses-produksi/{id}/status', [ProsesOperatorMesinApi::class, 'updateStatus']);
    Route::get('/proses-produksi/status/{status}', [ProsesOperatorMesinApi::class, 'getProcessesByStatus']);
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
});