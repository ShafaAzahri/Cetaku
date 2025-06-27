<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alamat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AlamatApiController extends Controller
{
    /**
     * Mendapatkan semua alamat (akses hanya via middleware untuk admin/super admin)
     */
    public function getAllAlamat()
    {
        try {
            $allAlamat = Alamat::all();

            return response()->json([
                'success' => true,
                'data' => $allAlamat
            ]);
        } catch (\Exception $e) {
            Log::error('Error mengambil semua alamat: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil semua alamat'
            ], 500);
        }
    }
}
