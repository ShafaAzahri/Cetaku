<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MesinController extends Controller
{
    protected $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = rtrim(env('API_URL', config('app.url')), '/');
    }
    
    /**
     * Menampilkan halaman daftar mesin
     */
    public function index(Request $request)
    {
        try {
            $status = $request->get('status', '');
            $tipe = $request->get('tipe', '');
            $search = $request->get('search', '');
            
            // Ambil data dari API
            $response = $this->sendApiRequest('get', '/mesins', [
                'status' => $status,
                'tipe' => $tipe,
                'search' => $search
            ]);
            
            if (!($response['success'] ?? false)) {
                return redirect()->back()->with('error', $response['message'] ?? 'Gagal memuat data mesin');
            }
            
            $mesins = $response['mesins'] ?? [];
            
            return view('admin.mesin.index', compact('mesins', 'status', 'tipe', 'search'));
        } catch (\Exception $e) {
            Log::error('Error pada halaman daftar mesin: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data');
        }
    }
    
    /**
     * Menampilkan detail mesin
     */
    public function show($id)
    {
        try {
            // Ambil detail mesin dari API
            $response = $this->sendApiRequest('get', "/mesins/{$id}");
            
            if (!($response['success'] ?? false)) {
                return redirect()->route('admin.mesins.index')
                    ->with('error', $response['message'] ?? 'Mesin tidak ditemukan');
            }
            
            $mesin = $response['mesin'] ?? [];
            
            // Ambil riwayat penggunaan mesin
            $historyResponse = $this->sendApiRequest('get', "/mesins/{$id}/history");
            $usage_history = ($historyResponse['success'] ?? false) 
                ? ($historyResponse['usage_history'] ?? []) 
                : [];
            
            return view('admin.mesin.show', compact('mesin', 'usage_history'));
        } catch (\Exception $e) {
            Log::error('Error pada halaman detail mesin: ' . $e->getMessage());
            return redirect()->route('admin.mesins.index')
                ->with('error', 'Terjadi kesalahan saat memuat detail mesin');
        }
    }
    
    /**
     * Mengubah status mesin
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:aktif,digunakan,maintenance',
            ]);
            
            $response = $this->sendApiRequest('put', "/mesins/{$id}/status", [
                'status' => $request->status
            ]);
            
            if ($response['success'] ?? false) {
                return redirect()->back()->with('success', 'Status mesin berhasil diperbarui');
            }
            
            return redirect()->back()->with('error', $response['message'] ?? 'Gagal memperbarui status mesin');
        } catch (\Exception $e) {
            Log::error('Error pada update status mesin: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui status');
        }
    }
    
    /**
     * Helper: Mengirim permintaan API dengan token otentikasi
     *
     * @param string $method Metode HTTP (get, post, put, delete)
     * @param string $endpoint Endpoint API
     * @param array $data Data yang dikirim (opsional)
     * @return array Respons dalam format array
     */
    protected function sendApiRequest($method, $endpoint, $data = [])
    {
        try {
            $token = session('api_token');
            
            Log::debug('Mengirim permintaan API', [
                'method' => $method,
                'endpoint' => $endpoint,
                'has_token' => !empty($token)
            ]);
            
            $response = Http::withToken($token)
                ->withHeaders([
                    'Accept' => 'application/json'
                ])
                ->$method($this->apiBaseUrl . $endpoint, $data);
            
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Error mengirim permintaan API: ' . $e->getMessage(), [
                'method' => $method,
                'endpoint' => $endpoint
            ]);
            
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan komunikasi dengan server'
            ];
        }
    }
}