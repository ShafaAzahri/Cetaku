<?php
namespace App\Http\Controllers\Api\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pesanan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
class PesananApiController extends Controller
{
    /**
     * Ambil daftar pesanan berdasarkan status
     */
public function index(Request $request)
{
    $user = $request->get('authenticated_user');
    $status = $request->query('status');
    $query = \App\Models\Pesanan::with([
        'detailPesanans.custom.item',
        'detailPesanans.custom.bahan',
        'detailPesanans.custom.jenis',
        'detailPesanans.custom.ukuran',
    ])->select([
        'id',
        'user_id', 
        'status',
        'total',
        'alamat_pengiriman',
        'bukti_pengiriman',
        'created_at',
        'updated_at'
    ])->where('user_id', $user->id);
    if ($status && $status !== 'Semua') {
        $query->where('status', $status);
    }
    $pesanan = $query->latest()->get();
    return response()->json([
        'status' => 'success',  
        'data' => $pesanan
    ]);
}
public function show($id, Request $request)
{
    $user = $request->get('authenticated_user');

    Log::info('🔍 User terautentikasi:', ['id' => $user->id]);

    $pesanan = Pesanan::with([
        'detailPesanans.custom.item',
        'detailPesanans.custom.bahan',
        'detailPesanans.custom.jenis',
        'detailPesanans.custom.ukuran',
    ])
    ->where('id', $id)
    ->where('user_id', $user->id)
    ->select([
        'id',
        'user_id', 
        'status',
        'total',
        'alamat_pengiriman',
        'bukti_pengiriman',
        'created_at',
        'updated_at'
    ])
    ->first();

    if (!$pesanan) {
        Log::warning('❌ Pesanan tidak ditemukan atau bukan milik user ini.', [
            'id_dipesan' => $id,
            'user_id_diautentikasi' => $user->id,
            'data_pesanan' => Pesanan::where('id', $id)->first(),
        ]);

        return response()->json([
            'status' => 'error',
            'message' => 'Pesanan tidak ditemukan atau Anda tidak memiliki akses.'
        ], 404);
    }

    return response()->json([
        'status' => 'success',
        'data' => $pesanan
    ]);
}

}