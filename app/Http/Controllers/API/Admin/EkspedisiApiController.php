<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ekspedisi;
use Illuminate\Http\Request;

class EkspedisiApiController extends Controller
{
    /**
     * Menampilkan daftar ekspedisis yang bisa difilter berdasarkan pesanan_id.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            // Mengambil pesanan_id dari query parameter
            $pesananId = $request->get('pesanan_id'); // Mendapatkan pesanan_id dari query params

            // Jika pesanan_id diberikan, filter ekspedisi berdasarkan pesanan_id
            if ($pesananId) {
                $ekspedisis = Ekspedisi::where('pesanan_id', $pesananId)
                    ->select('nama_ekspedisi', 'ongkos_kirim') // Hanya ambil kolom yang dibutuhkan
                    ->get();
            } else {
                // Jika pesanan_id tidak ada, ambil semua ekspedisis
                $ekspedisis = Ekspedisi::select('nama_ekspedisi', 'ongkos_kirim')->get();
            }

            // Mengembalikan response JSON dengan data ekspedisi
            return response()->json([
                'success' => true,
                'data' => $ekspedisis,
            ], 200);
        } catch (\Exception $e) {
            // Menangani kesalahan jika terjadi error pada proses fetching
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan dalam mengambil data.',
            ], 500);
        }
    }

}

