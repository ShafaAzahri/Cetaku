<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthApiController;
use App\Http\Controllers\API\ItemApiController;
use App\Http\Controllers\API\BahanApiController;
use App\Http\Controllers\API\JenisApiController;
use App\Http\Controllers\API\UkuranApiController;
use App\Http\Controllers\API\Admin\PesananApiController;
use App\Http\Controllers\API\BiayaDesainApiController;
use App\Http\Controllers\API\Admin\ProsesPesananApiController;

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
    
    // Pesanan routes
    Route::get('/pesanan', [PesananApiController::class, 'index']);
    Route::post('/pesanan', [PesananApiController::class, 'store']);
    Route::get('/pesanan/{id}', [PesananApiController::class, 'show']);
    Route::put('/pesanan/{id}', [PesananApiController::class, 'update']);
    Route::put('/pesanan/{id}/status', [PesananApiController::class, 'updateStatus']);
    Route::post('/pesanan/{id}/proses', [PesananApiController::class, 'assignProcess']);
    Route::post('/pesanan/{id}/desain', [PesananApiController::class, 'uploadDesain']);
    Route::get('/pesanan/{id}/produk/{detailId}', [PesananApiController::class, 'getProductDetail']);
    Route::post('/pesanan/{id}/konfirmasi-pengambilan', [PesananApiController::class, 'confirmPickup']);
    Route::post('/pesanan/{id}/konfirmasi-pengiriman', [PesananApiController::class, 'confirmShipment']);
    Route::post('/pesanan/{id}/konfirmasi-penerimaan', [PesananApiController::class, 'confirmReceived']);
    Route::get('/pesanan/statistik', [PesananApiController::class, 'getStatistics']);
    
    // List data
    Route::get('/operator/list', [PesananApiController::class, 'getOperators']);
    Route::get('/mesin/list', [PesananApiController::class, 'getMesins']);
    
    // Proses Pesanan routes
    Route::get('/proses', [ProsesPesananApiController::class, 'index']);
    Route::post('/proses', [ProsesPesananApiController::class, 'store']);
    Route::get('/proses/{id}', [ProsesPesananApiController::class, 'show']);
    Route::put('/proses/{id}', [ProsesPesananApiController::class, 'update']);
    Route::put('/proses/{id}/selesai', [ProsesPesananApiController::class, 'complete']);
    Route::put('/proses/{id}/batal', [ProsesPesananApiController::class, 'cancel']);
    Route::get('/proses/pesanan/{pesananId}', [ProsesPesananApiController::class, 'getByPesanan']);
    Route::get('/proses/operator/{operatorId}', [ProsesPesananApiController::class, 'getByOperator']);
    Route::get('/proses/statistik', [ProsesPesananApiController::class, 'getStatistics']);
});