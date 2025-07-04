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
        
        $query = Pesanan::with([
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
            'resi_pesanan',
            'updated_at'
        ])->where('user_id', $user->id);
        
        if ($status && $status !== 'Semua') {
            $query->where('status', $status);
        }
        
        $pesanans = $query->latest()->get();
        
        // Transform data untuk memastikan konsistensi
        $transformedPesanans = $pesanans->map(function ($pesanan) {
            return [
                'id' => $pesanan->id,
                'user_id' => $pesanan->user_id,
                'status' => $pesanan->status,
                'total_harga' => $pesanan->total, // Menggunakan total dari tabel pesanan
                'total' => $pesanan->total, // Untuk kompatibilitas
                'alamat_pengiriman' => $pesanan->alamat_pengiriman,
                'bukti_pengiriman' => $pesanan->bukti_pengiriman,
                'resi_pesanan' => $pesanan->resi_pengiriman,
                'created_at' => $pesanan->created_at,
                'updated_at' => $pesanan->updated_at,
                'detail_pesanans' => $pesanan->detailPesanans,
                'total_items' => $pesanan->detailPesanans->sum('jumlah'), // Total jumlah item
            ];
        });
        
        return response()->json([
            'status' => 'success',  
            'data' => $transformedPesanans
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
            'resi_pesanan',
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
        
        // Transform data untuk detail view
        $transformedPesanan = [
            'id' => $pesanan->id,
            'user_id' => $pesanan->user_id,
            'status' => $pesanan->status,
            'total_harga' => $pesanan->total, // Menggunakan total dari tabel pesanan
            'total' => $pesanan->total, // Untuk kompatibilitas
            'alamat_pengiriman' => $pesanan->alamat_pengiriman,
            'bukti_pengiriman' => $pesanan->bukti_pengiriman,
            'created_at' => $pesanan->created_at,
            'updated_at' => $pesanan->updated_at,
            'resi_pesanan' => $pesanan -> resi_pesanan,
            'detail_pesanans' => $pesanan->detailPesanans,
            'total_items' => $pesanan->detailPesanans->sum('jumlah'), // Total jumlah item
        ];
        
        return response()->json([
            'status' => 'success',
            'data' => $transformedPesanan
        ]);
    }

    public function cancel(Request $request, $id)
    {
        $user = $request->get('authenticated_user');
        $pesanan = Pesanan::where('id', $id)->where('user_id', $user->id)->first();
        
        if (!$pesanan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pesanan tidak ditemukan.'
            ], 404);
        }
        
        if ($pesanan->status == 'Selesai' || $pesanan->status == 'Dibatalkan') {
            return response()->json([
                'status' => 'error',
                'message' => 'Pesanan tidak dapat dibatalkan.'
            ], 400);
        }
        
        $pesanan->status = 'Dibatalkan';
        $pesanan->save();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Pesanan berhasil dibatalkan.'
        ]);
    }
}