<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ProsesPesanan;
use App\Models\DetailPesanan;
use App\Models\Operator;
use App\Models\Mesin;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ProsesOperatorMesinApi extends Controller
{
    /**
     * Mendapatkan daftar proses produksi aktif
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getActiveProcesses(Request $request)
    {
        try {
            $query = ProsesPesanan::with([
                'detailPesanan.custom.item',
                'detailPesanan.custom.bahan',
                'detailPesanan.custom.ukuran',
                'detailPesanan.pesanan',
                'operator',
                'mesin'
            ])
            ->whereNull('waktu_selesai')
            ->where('status_proses', '!=', 'Selesai');
            
            // Filter berdasarkan operator
            if ($request->has('operator_id') && !empty($request->operator_id)) {
                $query->where('operator_id', $request->operator_id);
            }
            
            // Filter berdasarkan mesin
            if ($request->has('mesin_id') && !empty($request->mesin_id)) {
                $query->where('mesin_id', $request->mesin_id);
            }
            
            // Filter berdasarkan status proses
            if ($request->has('status_proses') && !empty($request->status_proses)) {
                $query->where('status_proses', $request->status_proses);
            }
            
            // Pengurutan
            $sortBy = $request->get('sort_by', 'waktu_mulai');
            $sortDirection = $request->get('sort_direction', 'desc');
            $query->orderBy($sortBy, $sortDirection);
            
            // Eksekusi query
            $processes = $query->get();
            
            return response()->json([
                'success' => true,
                'proses_produksi' => $processes
            ]);
        } catch (\Exception $e) {
            Log::error('API Error: Gagal mendapatkan daftar proses produksi aktif - ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil daftar proses produksi aktif',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan detail proses produksi berdasarkan ID
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $process = ProsesPesanan::with([
                'detailPesanan.custom.item',
                'detailPesanan.custom.bahan',
                'detailPesanan.custom.ukuran',
                'detailPesanan.pesanan',
                'operator',
                'mesin'
            ])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'proses_pesanan' => $process
            ]);
        } catch (\Exception $e) {
            Log::error('API Error: Gagal mendapatkan detail proses produksi - ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil detail proses produksi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengubah status proses produksi
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'status_proses' => 'required|in:Mulai,Sedang Dikerjakan,Pause,Selesai',
                'catatan' => 'nullable|string'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            DB::beginTransaction();
            
            $process = ProsesPesanan::with(['mesin', 'detailPesanan.pesanan'])->findOrFail($id);
            $oldStatus = $process->status_proses;
            $newStatus = $request->status_proses;
            
            // Update data proses
            $process->status_proses = $newStatus;
            if ($request->has('catatan') && !empty($request->catatan)) {
                $process->catatan = $request->catatan;
            }
            
            // Jika status menjadi Selesai, update waktu selesai
            if ($newStatus == 'Selesai' && $oldStatus != 'Selesai') {
                $process->waktu_selesai = now();
                
                // Update status mesin menjadi aktif
                $mesin = $process->mesin;
                if ($mesin) {
                    $mesin->status = 'aktif';
                    $mesin->save();
                }
                
                // Cek apakah semua proses untuk pesanan ini telah selesai
                $detailPesanan = $process->detailPesanan;
                if ($detailPesanan) {
                    $pesanan = $detailPesanan->pesanan;
                    if ($pesanan) {
                        $unfinishedProcesses = ProsesPesanan::whereHas('detailPesanan', function ($query) use ($pesanan) {
                            $query->where('pesanan_id', $pesanan->id);
                        })
                        ->where('status_proses', '!=', 'Selesai')
                        ->count();
                        
                        // Jika semua proses telah selesai, update status pesanan
                        if ($unfinishedProcesses == 0) {
                            if ($pesanan->metode_pengambilan == 'ambil') {
                                $pesanan->status = 'Menunggu Pengambilan';
                            } else {
                                $pesanan->status = 'Sedang Dikirim';
                            }
                            $pesanan->save();
                        }
                    }
                }
            }
            
            $process->save();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Status proses produksi berhasil diperbarui',
                'proses_pesanan' => $process
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('API Error: Gagal mengubah status proses produksi - ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengubah status proses produksi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan daftar proses produksi berdasarkan status
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProcessesByStatus(Request $request, $status)
    {
        try {
            if (!in_array($status, ['Mulai', 'Sedang Dikerjakan', 'Pause', 'Selesai'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Status tidak valid'
                ], 400);
            }
            
            $query = ProsesPesanan::with([
                'detailPesanan.custom.item',
                'detailPesanan.pesanan',
                'operator',
                'mesin'
            ])
            ->where('status_proses', $status);
            
            // Filter tambahan jika diperlukan
            if ($status == 'Selesai' && $request->has('date_range')) {
                // Contoh filter untuk proses selesai dalam rentang waktu tertentu
                if ($request->has('start_date') && !empty($request->start_date)) {
                    $query->whereDate('waktu_selesai', '>=', $request->start_date);
                }
                
                if ($request->has('end_date') && !empty($request->end_date)) {
                    $query->whereDate('waktu_selesai', '<=', $request->end_date);
                }
            }
            
            // Pengurutan
            $sortBy = $request->get('sort_by', ($status == 'Selesai' ? 'waktu_selesai' : 'waktu_mulai'));
            $sortDirection = $request->get('sort_direction', 'desc');
            $query->orderBy($sortBy, $sortDirection);
            
            // Paginasi
            $limit = $request->get('limit', 10);
            $processes = $query->paginate($limit);
            
            return response()->json([
                'success' => true,
                'status' => $status,
                'proses_produksi' => $processes
            ]);
        } catch (\Exception $e) {
            Log::error('API Error: Gagal mendapatkan daftar proses produksi berdasarkan status - ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil daftar proses produksi',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}