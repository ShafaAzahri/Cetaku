<?php
namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PelangganController extends Controller
{
    public function index(Request $request)
    {
        // Mengambil nilai pencarian dari parameter query string
        $search = $request->get('search');  // Ambil nilai pencarian dari URL
        
        // Mengambil data pelanggan dengan role_id = 1 (user) dan memuat pesanan terkait
        $pelanggan = User::where('role_id', 1)  // Filter berdasarkan role_id = 1 (user)
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

        // Kirim data dan parameter pencarian ke view
        return view('admin.pelanggan.index', compact('pelanggan', 'search'));
    }
}
