<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Operator;
use App\Models\ProsesPesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class OperatorApiController extends Controller
{
    /**
     * Mendapatkan daftar semua operator
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */

    // Di app/Http/Controllers/API/OperatorApiController.php
    // Pada method index

    public function index(Request $request)
    {
        try {
            $query = Operator::query();
            
            // Filter berdasarkan status
            if ($request->has('status') && in_array($request->status, ['aktif', 'tidak_aktif'])) {
                $query->where('status', $request->status);
            }
            
            // Filter berdasarkan posisi
            if ($request->has('posisi') && !empty($request->posisi)) {
                $query->where('posisi', 'like', '%' . $request->posisi . '%');
            }
            
            // Pencarian berdasarkan nama
            if ($request->has('search') && !empty($request->search)) {
                $query->where('nama', 'like', '%' . $request->search . '%');
            }
            
            // Ambil data operator
            $operators = $query->get();
            
            // Untuk setiap operator, periksa apakah sedang mengerjakan pesanan
            foreach ($operators as $operator) {
                $currentAssignment = ProsesPesanan::with(['detailPesanan.custom.item', 'detailPesanan.pesanan', 'mesin'])
                    ->where('operator_id', $operator->id)
                    ->whereNull('waktu_selesai')
                    ->where('status_proses', '!=', 'Selesai')
                    ->orderBy('waktu_mulai', 'desc')
                    ->first();
                
                // Jika tidak ada tugas aktif, pastikan status operator 'tidak_aktif'
                if (!$currentAssignment && $operator->status == 'aktif') {
                    // Perbaikan: Hanya update kolom status saja
                    Operator::where('id', $operator->id)
                        ->update(['status' => 'tidak_aktif']);
                }
                
                // Attach current_assignment sebagai properti tanpa menyimpannya ke database
                $operator->current_assignment = $currentAssignment;
            }
            
            return response()->json([
                'success' => true,
                'operators' => $operators
            ]);
        } catch (\Exception $e) {
            Log::error('API Error: Gagal mendapatkan daftar operator - ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil daftar operator',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan detail operator berdasarkan ID
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $operator = Operator::findOrFail($id);
            
            // Ambil penugasan saat ini dengan relasi yang lebih lengkap
            $currentAssignment = ProsesPesanan::with([
                'detailPesanan.custom.item',
                'detailPesanan.custom.bahan',
                'detailPesanan.custom.ukuran',
                'detailPesanan.pesanan',
                'mesin'
            ])
            ->where('operator_id', $id)
            ->whereNull('waktu_selesai')
            ->where('status_proses', '!=', 'Selesai')
            ->orderBy('waktu_mulai', 'desc')
            ->first();
            
            // Pastikan data informasi pesanan terlampir dengan benar
            $operator->current_assignment = $currentAssignment;
            
            // Untuk debugging, tambahkan log
            if ($currentAssignment) {
                Log::debug('Informasi pesanan operator', [
                    'operator_id' => $id,
                    'proses_id' => $currentAssignment->id,
                    'detail_pesanan_id' => $currentAssignment->detail_pesanan_id,
                    'pesanan_id' => $currentAssignment->detailPesanan->pesanan_id ?? null,
                    'has_pesanan' => isset($currentAssignment->detailPesanan->pesanan)
                ]);
            } else {
                Log::debug('Operator tidak memiliki tugas aktif', ['operator_id' => $id]);
            }
            
            return response()->json([
                'success' => true,
                'operator' => $operator
            ]);
        } catch (\Exception $e) {
            Log::error('API Error: Gagal mendapatkan detail operator - ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil detail operator',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan riwayat pekerjaan operator
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getHistory(Request $request, $id)
    {
        try {
            // Validasi operator
            $operator = Operator::findOrFail($id);
            
            // Query riwayat
            $query = ProsesPesanan::with(['detailPesanan.custom.item', 'mesin'])
                ->where('operator_id', $id)
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
            
            // Tambahkan durasi pengerjaan untuk setiap item
            foreach ($history as $process) {
                if ($process->waktu_mulai && $process->waktu_selesai) {
                    $mulai = new \DateTime($process->waktu_mulai);
                    $selesai = new \DateTime($process->waktu_selesai);
                    $interval = $mulai->diff($selesai);
                    
                    $process->durasi_pengerjaan = '';
                    
                    if ($interval->d > 0) {
                        $process->durasi_pengerjaan .= $interval->d . ' hari ';
                    }
                    
                    if ($interval->h > 0) {
                        $process->durasi_pengerjaan .= $interval->h . ' jam ';
                    }
                    
                    if ($interval->i > 0) {
                        $process->durasi_pengerjaan .= $interval->i . ' menit';
                    }
                    
                    $process->durasi_pengerjaan = trim($process->durasi_pengerjaan);
                }
            }
            
            // Statistik
            $totalCompleted = ProsesPesanan::where('operator_id', $id)
                ->where('status_proses', 'Selesai')
                ->count();
            
            $thisMonth = ProsesPesanan::where('operator_id', $id)
                ->where('status_proses', 'Selesai')
                ->whereMonth('waktu_selesai', now()->month)
                ->whereYear('waktu_selesai', now()->year)
                ->count();
            
            $thisWeek = ProsesPesanan::where('operator_id', $id)
                ->where('status_proses', 'Selesai')
                ->whereBetween('waktu_selesai', [now()->startOfWeek(), now()->endOfWeek()])
                ->count();
            
            return response()->json([
                'success' => true,
                'operator' => [
                    'id' => $operator->id,
                    'nama' => $operator->nama,
                    'posisi' => $operator->posisi
                ],
                'completed_assignments' => $history,
                'summary' => [
                    'total_completed' => $totalCompleted,
                    'this_month' => $thisMonth,
                    'this_week' => $thisWeek
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('API Error: Gagal mendapatkan riwayat operator - ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil riwayat operator',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengubah status operator
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
        public function updateStatus(Request $request, $id)
        {
            try {
                $validator = Validator::make($request->all(), [
                    'status' => 'required|in:aktif,tidak_aktif',
                ]);
                
                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validasi gagal',
                        'errors' => $validator->errors()
                    ], 422);
                }
                
                $operator = Operator::findOrFail($id);
                
                // Cek apakah operator sedang mengerjakan pesanan
                $currentAssignment = ProsesPesanan::where('operator_id', $id)
                    ->whereNull('waktu_selesai')
                    ->where('status_proses', '!=', 'Selesai')
                    ->first();
                
                if ($currentAssignment && $request->status == 'tidak_aktif') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tidak dapat menonaktifkan operator karena sedang mengerjakan pesanan'
                    ], 400);
                }
                
                $operator->status = $request->status;
                $operator->save();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Status operator berhasil diperbarui',
                    'operator' => $operator
                ]);
            } catch (\Exception $e) {
                Log::error('API Error: Gagal mengubah status operator - ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat mengubah status operator',
                    'error' => $e->getMessage()
                ], 500);
            }
        }
}