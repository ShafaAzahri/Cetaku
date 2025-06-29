<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class PesananWebController extends Controller
{
    protected $apiBaseUrl;

    public function __construct()
    {
        // Pastikan URL API sesuai dengan konfigurasi Anda
        $this->apiBaseUrl = rtrim(env('API_URL', config('app.url')), '/');
    }

    /**
     * Menampilkan halaman daftar pesanan user
     */
    public function index(Request $request)
    {
        $token = session('api_token'); // Ambil token autentikasi dari session

        // Cek apakah user sudah login (API token tersedia)
        if (!$token) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }

        $status = $request->query('status'); // Ambil filter status dari query string

        try {
            $queryParams = [];

            // Tambahkan parameter status ke query - API akan handle logika "Semua"
            if ($status) {
                $queryParams['status'] = $status;
            }

            // Kirim request GET ke API pesanan dengan token autentikasi
            $response = Http::withToken($token)
                ->get("{$this->apiBaseUrl}/pesanan", $queryParams);  // API endpoint

            if ($response->successful()) {
                $responseData = $response->json();
                
                // Pastikan response memiliki struktur yang benar
                if ($responseData['status'] === 'success') {
                    $pesanans = $responseData['data']; // Ambil data pesanan
                    return view('user.pesanan', compact('pesanans', 'status'));
                }
                
                return redirect()->back()->with('error', 'Format response tidak valid');
            }

            // Handle error dari API
            $errorData = $response->json();
            $message = $errorData['message'] ?? 'Gagal mengambil data pesanan';
            return redirect()->back()->with('error', $message);

        } catch (\Exception $e) {
            // Handle error saat request ke API
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan halaman detail pesanan berdasarkan ID
     */
    public function show(Request $request)
    {
        $token = session('api_token'); // Ambil token autentikasi dari session

        // Cek apakah user sudah login (API token tersedia)
        if (!$token) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }

        $pesananId = $request->query('id'); // Ambil ID pesanan dari query string

        if (!$pesananId) {
            return redirect()->back()->with('error', 'ID pesanan tidak ditemukan.');
        }

        try {
            // Kirim request GET ke API untuk mendapatkan detail pesanan
            $response = Http::withToken($token)
                ->get("{$this->apiBaseUrl}/pesanan/{$pesananId}");  // API endpoint dengan ID pesanan

            if ($response->successful()) {
                $responseData = $response->json();

                if ($responseData['status'] === 'success') {
                    $pesanan = $responseData['data'];  // Ambil data pesanan
                    return view('user.detail-pesanan', compact('pesanan'));
                }

                return redirect()->back()->with('error', 'Format response tidak valid.');
            }

            // Handle error dari API
            $errorData = $response->json();
            $message = $errorData['message'] ?? 'Gagal mengambil detail pesanan.';
            return redirect()->back()->with('error', $message);

        } catch (\Exception $e) {
            // Handle error saat request ke API
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
