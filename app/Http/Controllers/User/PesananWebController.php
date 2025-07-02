<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
    $token = session('api_token');

    Log::info('Memasuki fungsi show() PesananWebController', [
        'token_exist' => $token ? true : false,
        'request_id' => $request->query('id'),
        'is_ajax' => $request->ajax(),
        'wants_json' => $request->wantsJson()
    ]);

    if (!$token) {
        Log::warning('Token tidak ditemukan dalam session saat akses detail pesanan');
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Silakan login terlebih dahulu'
            ], 401);
        }
        return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
    }

    $pesananId = $request->query('id');

    if (!$pesananId) {
        Log::error('ID pesanan tidak ditemukan dalam query parameter');
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'error',
                'message' => 'ID pesanan tidak ditemukan'
            ], 400);
        }
        return redirect()->back()->with('error', 'ID pesanan tidak ditemukan.');
    }

    try {
        Log::info('Mengirim request ke API detail pesanan', [
            'url' => "{$this->apiBaseUrl}/pesanan/$pesananId",
            'token_length' => strlen($token)
        ]);

        $response = Http::withToken($token)
            ->timeout(30) // Add timeout
            ->get("{$this->apiBaseUrl}/pesanan/{$pesananId}");

        Log::info('Response dari API', [
            'status' => $response->status(),
            'response_size' => strlen($response->body()),
            'content_type' => $response->header('Content-Type')
        ]);

        if ($response->successful()) {
            $responseData = $response->json();

            if (isset($responseData['status']) && $responseData['status'] === 'success') {
                $pesanan = $responseData['data'];
                Log::info('Berhasil mengambil detail pesanan', [
                    'pesanan_id' => $pesananId,
                    'pesanan_status' => $pesanan['status'] ?? 'unknown'
                ]);

                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'status' => 'success',
                        'data' => $pesanan
                    ]);
                }

                return view('user.detail-pesanan', compact('pesanan'));
            }

            Log::error('Format response dari API tidak valid', [
                'responseData' => $responseData,
                'expected_status' => 'success'
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Format response tidak valid'
                ], 500);
            }
            return redirect()->back()->with('error', 'Format response tidak valid.');
        }

        $errorMessage = $response->json('message') ?? 'Gagal mengambil detail pesanan.';
        Log::error('API mengembalikan error', [
            'status' => $response->status(),
            'message' => $errorMessage,
            'response_body' => $response->body()
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'error',
                'message' => $errorMessage
            ], $response->status());
        }
        return redirect()->back()->with('error', $errorMessage);

    } catch (\Exception $e) {
        Log::critical('Exception saat request ke API', [    
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        $errorMessage = 'Terjadi kesalahan: ' . $e->getMessage();
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'error',
                'message' => $errorMessage
            ], 500);
        }
        return redirect()->back()->with('error', $errorMessage);
    }
}


    public function cancel($id)
{
    $token = session('api_token');

    if (!$token) {
        return response()->json(['status' => 'error', 'message' => 'Silakan login terlebih dahulu.'], 401);
    }

    try {
        $response = Http::withToken($token)->delete("{$this->apiBaseUrl}/pesanan/{$id}/cancel");

        if ($response->successful()) {
            return response()->json(['status' => 'success', 'message' => 'Pesanan berhasil dibatalkan.']);
        }

        $message = $response->json('message') ?? 'Gagal membatalkan pesanan';
        return response()->json(['status' => 'error', 'message' => $message]);

    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
    }
}
}