<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProsesProduksiController extends Controller
{
    protected $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = rtrim(env('API_URL', config('app.url')), '/');
    }
    
    /**
     * Menampilkan halaman proses produksi aktif
     */
    public function index(Request $request)
    {
        try {
            $operator_id = $request->get('operator_id', '');
            $mesin_id = $request->get('mesin_id', '');
            $status_proses = $request->get('status_proses', '');
            
            // Ambil data dari API
            $response = $this->sendApiRequest('get', '/proses-produksi/aktif', [
                'operator_id' => $operator_id,
                'mesin_id' => $mesin_id,
                'status_proses' => $status_proses
            ]);
            
            if (!($response['success'] ?? false)) {
                return redirect()->back()->with('error', $response['message'] ?? 'Gagal memuat data proses produksi');
            }
            
            $proses_produksi = $response['proses_produksi'] ?? [];
            
            // Ambil daftar operator dan mesin untuk filter
            $operatorsResponse = $this->sendApiRequest('get', '/operators', ['status' => 'aktif']);
            $mesinsResponse = $this->sendApiRequest('get', '/mesins');
            
            $operators = ($operatorsResponse['success'] ?? false) ? ($operatorsResponse['operators'] ?? []) : [];
            $mesins = ($mesinsResponse['success'] ?? false) ? ($mesinsResponse['mesins'] ?? []) : [];
            
            return view('admin.proses-produksi.index', compact(
                'proses_produksi', 
                'operators', 
                'mesins',
                'operator_id',
                'mesin_id',
                'status_proses'
            ));
        } catch (\Exception $e) {
            Log::error('Error pada halaman proses produksi: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data');
        }
    }
    
    /**
     * Menampilkan proses produksi berdasarkan status
     */
    public function showByStatus(Request $request, $status)
    {
        try {
            if (!in_array($status, ['Mulai', 'Sedang Dikerjakan', 'Pause', 'Selesai'])) {
                return redirect()->route('admin.proses-produksi.index')
                    ->with('error', 'Status tidak valid');
            }
            
            $start_date = $request->get('start_date', '');
            $end_date = $request->get('end_date', '');
            
            // Parameter tambahan untuk filter
            $params = [
                'sort_by' => $request->get('sort_by', ''),
                'sort_direction' => $request->get('sort_direction', '')
            ];
            
            // Tambahkan filter tanggal jika status = Selesai
            if ($status == 'Selesai') {
                $params['date_range'] = true;
                $params['start_date'] = $start_date;
                $params['end_date'] = $end_date;
            }
            
            $response = $this->sendApiRequest('get', "/proses-produksi/status/{$status}", $params);
            
            if (!($response['success'] ?? false)) {
                return redirect()->route('admin.proses-produksi.index')
                    ->with('error', $response['message'] ?? 'Gagal memuat data proses produksi');
            }
            
            $proses_produksi = $response['proses_produksi'] ?? [];
            
            return view('admin.proses-produksi.status', compact(
                'proses_produksi', 
                'status',
                'start_date',
                'end_date'
            ));
        } catch (\Exception $e) {
            Log::error('Error pada halaman proses produksi berdasarkan status: ' . $e->getMessage());
            return redirect()->route('admin.proses-produksi.index')
                ->with('error', 'Terjadi kesalahan saat memuat data');
        }
    }
    
    /**
     * Menampilkan detail proses produksi
     */
    public function show($id)
    {
        try {
            $response = $this->sendApiRequest('get', "/proses-produksi/{$id}");
            
            if (!($response['success'] ?? false)) {
                return redirect()->route('admin.proses-produksi.index')
                    ->with('error', $response['message'] ?? 'Proses produksi tidak ditemukan');
            }
            
            $proses = $response['proses_pesanan'] ?? [];
            
            return view('admin.proses-produksi.show', compact('proses'));
        } catch (\Exception $e) {
            Log::error('Error pada halaman detail proses produksi: ' . $e->getMessage());
            return redirect()->route('admin.proses-produksi.index')
                ->with('error', 'Terjadi kesalahan saat memuat detail proses produksi');
        }
    }
    
    /**
     * Mengubah status proses produksi
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status_proses' => 'required|in:Mulai,Sedang Dikerjakan,Pause,Selesai',
                'catatan' => 'nullable|string'
            ]);
            
            $response = $this->sendApiRequest('put', "/proses-produksi/{$id}/status", [
                'status_proses' => $request->status_proses,
                'catatan' => $request->catatan
            ]);
            
            if ($response['success'] ?? false) {
                return redirect()->back()->with('success', 'Status proses produksi berhasil diperbarui');
            }
            
            return redirect()->back()->with('error', $response['message'] ?? 'Gagal memperbarui status proses produksi');
        } catch (\Exception $e) {
            Log::error('Error pada update status proses produksi: ' . $e->getMessage());
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