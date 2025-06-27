<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class PesananWebController extends Controller
{
    protected $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = rtrim(env('API_URL', config('app.url')), '/');
    }

    /**
     * Menampilkan halaman daftar pesanan user
     */
    public function index(Request $request)
    {
        $token = session('api_token');

        if (!$token) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }

        $status = $request->query('status');

        try {
            $queryParams = [];

            // Tambahkan status jika diisi
            if ($status) {
                $queryParams['status'] = $status;
            }

            $response = Http::withToken($token)->get("{$this->apiBaseUrl}/pesanan", $queryParams);

            if ($response->successful()) {
                $pesanans = $response->json('data');
                return view('user.pesanan', compact('pesanans', 'status'));
            }

            $message = $response->json('message') ?? 'Gagal mengambil data pesanan';
            return redirect()->back()->with('error', $message);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
