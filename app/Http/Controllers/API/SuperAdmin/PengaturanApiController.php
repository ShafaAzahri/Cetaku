<?php
namespace App\Http\Controllers\API\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\TokoInfo;
use Illuminate\Http\Request;

class PengaturanApiController extends Controller
{
    public function getTokoInfo()
    {
        // Mengambil data toko_info
        $tokoInfo = TokoInfo::all(); // Ambil semua record

        // Mengembalikan response JSON
        return response()->json([
            'success' => true,
            'data' => $tokoInfo
        ]);
    }
}
