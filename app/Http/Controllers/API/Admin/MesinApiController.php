<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mesin;
use App\Models\ProsesPesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class MesinApiController extends Controller
{
    /**
     * Mendapatkan daftar semua mesin
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $query = Mesin::query();
            
            // Filter berdasarkan status
            if ($request->has('status') && in_array($request->status, ['aktif', 'digunakan'])) {
                $query->where('status', $request->status);
            }
            
            // Filter berdasarkan tipe mesin
            if ($request->has('tipe') && !empty($request->tipe)) {
                $query->where('tipe_mesin', 'like', '%' . $request->tipe . '%');
            }
            
            // Pencarian berdasarkan nama
            if ($request->has('search') && !empty($request->search)) {
                $query->where('nama_mesin', 'like', '%' . $request->search . '%');
            }
            
            // Ambil data mesin
            $mesins = $query->get();
            
            // Untuk setiap mesin, periksa apakah sedang digunakan
            foreach ($mesins as $mesin) {
                $currentUsage = ProsesPesanan::with(['detailPesanan.custom.item', 'operator'])
                    ->where('mesin_id', $mesin->id)
                    ->whereNull('waktu_selesai')
                    ->where('status_proses', '!=', 'Selesai')
                    ->orderBy('waktu_mulai', 'desc')
                    ->first();
                
                $mesin->current_usage = $currentUsage;
            }
            
            return response()->json([
                'success' => true,
                'mesins' => $mesins
            ]);
        } catch (\Exception $e) {
            Log::error('API Error: Gagal mendapatkan daftar mesin - ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil daftar mesin',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan detail mesin berdasarkan ID
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $mesin = Mesin::findOrFail($id);
            
            // Ambil penggunaan saat ini
            $currentUsage = ProsesPesanan::with(['detailPesanan.custom.item', 'operator'])
                ->where('mesin_id', $id)
                ->whereNull('waktu_selesai')
                ->where('status_proses', '!=', 'Selesai')
                ->orderBy('waktu_mulai', 'desc')
                ->first();
            
            $mesin->current_usage = $currentUsage;
            
            return response()->json([
                'success' => true,
                'mesin' => $mesin
            ]);
        } catch (\Exception $e) {
            Log::error('API Error: Gagal mendapatkan detail mesin - ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil detail mesin',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan riwayat penggunaan mesin
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getHistory(Request $request, $id)
    {
        try {
            // Validasi mesin
            $mesin = Mesin::findOrFail($id);
            
            // Query riwayat
            $query = ProsesPesanan::with(['detailPesanan.custom.item', 'operator'])
                ->where('mesin_id', $id)
                ->where('status_proses', 'Selesai')
                ->whereNotNull('waktu_selesai');
            
            // Filter berdasarkan tanggal
            if ($request->has('start_date') && !empty($request->start_date)) {
                $query->whereDate('waktu_mulai', '>=', $request->start_date);
            }
            
            if ($request->has('end_date') && !empty($request->end_date)) {
                $query->whereDate('waktu_selesai', '<=', $request->end_date);
            }
            
            // Pengurutan
            $sortBy = $request->get('sort_by', 'waktu_selesai');
            $sortDirection = $request->get('sort_direction', 'desc');
            $query->orderBy($sortBy, $sortDirection);
            
            // Paginasi
            $limit = $request->get('limit', 10);
            $history = $query->paginate($limit);
            
            // Tambahkan durasi penggunaan untuk setiap item
            foreach ($history as $process) {
                if ($process->waktu_mulai && $process->waktu_selesai) {
                    $mulai = new \DateTime($process->waktu_mulai);
                    $selesai = new \DateTime($process->waktu_selesai);
                    $interval = $mulai->diff($selesai);
                    
                    $process->durasi_penggunaan = '';
                    
                    if ($interval->d > 0) {
                        $process->durasi_penggunaan .= $interval->d . ' hari ';
                    }
                    
                    if ($interval->h > 0) {
                        $process->durasi_penggunaan .= $interval->h . ' jam ';
                    }
                    
                    if ($interval->i > 0) {
                        $process->durasi_penggunaan .= $interval->i . ' menit';
                    }
                    
                    $process->durasi_penggunaan = trim($process->durasi_penggunaan);
                }
            }
            
            return response()->json([
                'success' => true,
                'mesin' => [
                    'id' => $mesin->id,
                    'nama_mesin' => $mesin->nama_mesin,
                    'tipe_mesin' => $mesin->tipe_mesin
                ],
                'usage_history' => $history
            ]);
        } catch (\Exception $e) {
            Log::error('API Error: Gagal mendapatkan riwayat mesin - ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil riwayat mesin',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengubah status mesin
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:aktif,digunakan,maintenance',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $mesin = Mesin::findOrFail($id);
            
            // Jika status sebelumnya digunakan dan akan diubah ke maintenance
            // Kita perlu memeriksa apakah mesin sedang digunakan dalam proses produksi
            if ($mesin->status == 'digunakan' && $request->status == 'maintenance') {
                $currentUsage = ProsesPesanan::where('mesin_id', $id)
                    ->whereNull('waktu_selesai')
                    ->where('status_proses', '!=', 'Selesai')
                    ->first();
                
                if ($currentUsage) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tidak dapat mengubah status mesin ke maintenance karena sedang digunakan dalam proses produksi'
                    ], 400);
                }
            }
            
            // Update status mesin
            Mesin::where('id', $id)->update(['status' => $request->status]);
            
            // Ambil data mesin yang sudah diupdate
            $updatedMesin = Mesin::find($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Status mesin berhasil diperbarui',
                'mesin' => $updatedMesin
            ]);
        } catch (\Exception $e) {
            Log::error('API Error: Gagal mengubah status mesin - ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengubah status mesin',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}