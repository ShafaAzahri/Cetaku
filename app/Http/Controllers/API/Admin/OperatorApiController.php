<?php

namespace App\Http\Controllers\API\Admin;

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
}