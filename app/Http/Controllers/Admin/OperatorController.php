<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OperatorController extends Controller
{
    protected $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = rtrim(env('API_URL', config('app.url')), '/');
    }
    
    /**
     * Menampilkan halaman daftar operator
     */
    public function index(Request $request)
    {
        try {
            $status = $request->get('status', '');
            $posisi = $request->get('posisi', '');
            $search = $request->get('search', '');
            
            // Ambil data dari API
            $response = $this->sendApiRequest('get', '/operators', [
                'status' => $status,
                'posisi' => $posisi,
                'search' => $search
            ]);
            
            if (!($response['success'] ?? false)) {
                return redirect()->back()->with('error', $response['message'] ?? 'Gagal memuat data operator');
            }
            
            $operators = $response['operators'] ?? [];
            
            return view('admin.operator.index', compact('operators', 'status', 'posisi', 'search'));
        } catch (\Exception $e) {
            Log::error('Error pada halaman daftar operator: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data');
        }
    }
    
    /**
     * Menampilkan detail operator
     */
    public function show($id)
    {
        try {
            // Ambil detail operator dari API
            $response = $this->sendApiRequest('get', "/operators/{$id}");
            
            if (!($response['success'] ?? false)) {
                return redirect()->route('admin.operators.index')
                    ->with('error', $response['message'] ?? 'Operator tidak ditemukan');
            }
            
            $operator = $response['operator'] ?? [];
            
            // Ambil riwayat pekerjaan operator
            $historyResponse = $this->sendApiRequest('get', "/operators/{$id}/history");
            $history = ($historyResponse['success'] ?? false) 
                ? ($historyResponse['completed_assignments'] ?? []) 
                : [];
            $summary = ($historyResponse['success'] ?? false) 
                ? ($historyResponse['summary'] ?? []) 
                : [];
            
            return view('admin.operator.show', compact('operator', 'history', 'summary'));
        } catch (\Exception $e) {
            Log::error('Error pada halaman detail operator: ' . $e->getMessage());
            return redirect()->route('admin.operators.index')
                ->with('error', 'Terjadi kesalahan saat memuat detail operator');
        }
    }
    
    /**
     * Mengubah status operator
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:aktif,tidak_aktif',
            ]);
            
            $response = $this->sendApiRequest('put', "/operators/{$id}/status", [
                'status' => $request->status
            ]);
            
            if ($response['success'] ?? false) {
                return redirect()->back()->with('success', 'Status operator berhasil diperbarui');
            }
            
            return redirect()->back()->with('error', $response['message'] ?? 'Gagal memperbarui status operator');
        } catch (\Exception $e) {
            Log::error('Error pada update status operator: ' . $e->getMessage());
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