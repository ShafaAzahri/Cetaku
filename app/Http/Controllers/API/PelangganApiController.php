<?php
namespace App\Http\Controllers\API;

use App\Models\User;  // Gunakan model User
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PelangganApiController extends Controller
{
    // Menampilkan semua pelanggan beserta pesanan mereka dengan fitur pencarian
    public function index(Request $request)  // Pastikan parameter Request $request ditambahkan
    {
        // Mengambil nilai pencarian dari parameter query string
        $search = $request->get('search');  // Mengambil data pencarian dari URL
        
        // Mengambil data pelanggan dengan role_id = 1 (user) dan memuat pesanan terkait
        $pelanggans = User::where('role_id', 1)  // Filter berdasarkan role_id = 1 (user)
            ->when($search, function ($query, $search) {
                // Jika ada pencarian, filter berdasarkan nama atau email
                $query->where(function ($q) use ($search) {
                    $q->where('nama', 'like', '%' . $search . '%') // Cari berdasarkan nama
                      ->orWhere('email', 'like', '%' . $search . '%'); // Cari berdasarkan email
                });
            })
            ->with(['pesanan' => function ($query) {
                // Memuat pesanan dengan kolom yang diperlukan dan urutkan berdasarkan tanggal_dipesan
                $query->select('id', 'user_id', 'status', 'tanggal_dipesan')
                      ->orderBy('tanggal_dipesan', 'desc');
            }])
            ->get(['id', 'nama', 'email']);  // Menampilkan hanya nama, email, dan id pelanggan

        return response()->json($pelanggans);  // Mengembalikan hasil pencarian dalam format JSON
    }

    // Menampilkan data pelanggan berdasarkan ID dan pesanan mereka
    public function show($id)
    {
        // Mengambil data pelanggan berdasarkan ID dan memuat pesanan mereka
        $pelanggan = User::where('role_id', 1)
            ->where('id', $id)
            ->with(['pesanan' => function ($query) {
                $query->select('id', 'user_id', 'status', 'tanggal_dipesan')
                      ->orderBy('tanggal_dipesan', 'desc');
            }])
            ->first(['id', 'nama', 'email']);  // Menampilkan nama, email, dan id pelanggan

        if (!$pelanggan) {
            return response()->json(['message' => 'Pelanggan tidak ditemukan'], 404);
        }

        return response()->json($pelanggan);  // Mengembalikan data pelanggan berdasarkan ID
    }
}
