<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PesananManagerController extends Controller
{
    protected $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = rtrim(env('API_URL', config('app.url')), '/');
    }
    
    /**
     * Menampilkan halaman daftar pesanan
     */
    public function index(Request $request)
    {
        try {
            $status = $request->get('status', 'Semua Status');
            $search = $request->get('search', '');
            $dariTanggal = $request->get('dari_tanggal', '');
            $sampaiTanggal = $request->get('sampai_tanggal', '');
            $perPage = $request->get('per_page', 10);
            
            // Ambil data dari API
            $response = $this->sendApiRequest('get', '/admin/pesanan', [
                'status' => $status,
                'search' => $search,
                'dari_tanggal' => $dariTanggal,
                'sampai_tanggal' => $sampaiTanggal,
                'per_page' => $perPage
            ]);
            
            if (!($response['success'] ?? false)) {
                return redirect()->back()->with('error', $response['message'] ?? 'Gagal memuat data pesanan');
            }
            
            $pesanans = $response['pesanans'];
            $statusOptions = $response['status_options'] ?? ['Pemesanan', 'Dikonfirmasi', 'Sedang Diproses', 'Menunggu Pengambilan', 'Sedang Dikirim', 'Selesai', 'Dibatalkan'];
            
            // Ambil data statistik
            $statsResponse = $this->sendApiRequest('get', '/admin/pesanan/statistics');
            $stats = ($statsResponse['success'] ?? false) ? $statsResponse['statistics'] : null;
            
            return view('admin.pesanan.index', compact(
                'pesanans', 
                'status',
                'search',
                'dariTanggal',
                'sampaiTanggal',
                'statusOptions',
                'stats'
            ));
        } catch (\Exception $e) {
            Log::error('Error pada halaman daftar pesanan: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data');
        }
    }
    
    /**
     * Menampilkan detail pesanan
     */
// Di PesananManagerController.php
public function show($id)
{
    try {
        // Ambil detail pesanan dari API
        $response = $this->sendApiRequest('get', "/admin/pesanan/{$id}");
        
        if (!($response['success'] ?? false)) {
            return redirect()->route('admin.pesanan.index')
                ->with('error', $response['message'] ?? 'Pesanan tidak ditemukan');
        }
        
        $pesanan = $response['pesanan'];
        $statusOptions = $response['status_options'] ?? [];
        
        // Ambil daftar mesin dan operator dari response API
        $mesinList = $response['available_machines'] ?? [];
        $operatorList = $response['active_operators'] ?? [];
        
        // Ambil biaya desain (kode yang sudah ada)
        $biayaDesainResponse = $this->sendApiRequest('get', '/biaya-desains');
        $biayaDesain = 0;
        
        if (($biayaDesainResponse['success'] ?? false) && 
            isset($biayaDesainResponse['biaya_desains']) && 
            count($biayaDesainResponse['biaya_desains']) > 0) {
            $biayaDesain = $biayaDesainResponse['biaya_desains'][0]['biaya'] ?? 0;
        }
        
        return view('admin.pesanan.show.show', compact(
            'pesanan',
            'statusOptions',
            'mesinList',
            'operatorList',
            'biayaDesain'
        ));
    } catch (\Exception $e) {
        Log::error('Error pada halaman detail pesanan: ' . $e->getMessage());
        return redirect()->route('admin.pesanan.index')
            ->with('error', 'Terjadi kesalahan saat memuat detail pesanan');
    }
}
    
    /**
     * Update status pesanan
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|string',
                'catatan' => 'nullable|string'
            ]);
            
            $response = $this->sendApiRequest('put', "/admin/pesanan/{$id}/status", $request->all());
            
            if ($response['success'] ?? false) {
                return redirect()->back()->with('success', 'Status pesanan berhasil diperbarui');
            }
            
            return redirect()->back()->with('error', $response['message'] ?? 'Gagal memperbarui status pesanan');
        } catch (\Exception $e) {
            Log::error('Error pada update status pesanan: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui status');
        }
    }
    
    /**
     * Menugaskan proses produksi
     */
    public function assignProduction(Request $request, $id)
    {
        try {
            $request->validate([
                'detail_pesanan_id' => 'required',
                'mesin_id' => 'required',
                'operator_id' => 'required',
                'catatan' => 'nullable|string'
            ]);
            
            $response = $this->sendApiRequest('post', "/admin/pesanan/{$id}/assign-production", $request->all());
            
            if ($response['success'] ?? false) {
                return redirect()->back()->with('success', 'Proses produksi berhasil ditugaskan');
            }
            
            return redirect()->back()->with('error', $response['message'] ?? 'Gagal menugaskan proses produksi');
        } catch (\Exception $e) {
            Log::error('Error pada assign produksi: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menugaskan proses produksi');
        }
    }
    
    /**
     * Menyelesaikan proses produksi
     */
    public function completeProduction(Request $request, $id)
    {
        try {
            $request->validate([
                'proses_pesanan_id' => 'required',
                'catatan' => 'nullable|string'
            ]);
            
            $response = $this->sendApiRequest('post', "/admin/pesanan/{$id}/complete-production", $request->all());
            
            if ($response['success'] ?? false) {
                return redirect()->back()->with('success', 'Proses produksi berhasil diselesaikan');
            }
            
            return redirect()->back()->with('error', $response['message'] ?? 'Gagal menyelesaikan proses produksi');
        } catch (\Exception $e) {
            Log::error('Error pada complete produksi: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyelesaikan proses produksi');
        }
    }
    
    /**
     * Konfirmasi pengiriman pesanan
     */
    public function confirmShipment(Request $request, $id)
    {
        try {
            $request->validate([
                'ekspedisi_id' => 'required',
                'nomor_resi' => 'nullable|string|max:100',
                'catatan' => 'nullable|string'
            ]);
            
            $response = $this->sendApiRequest('post', "/admin/pesanan/{$id}/confirm-shipment", $request->all());
            
            if ($response['success'] ?? false) {
                return redirect()->back()->with('success', 'Pengiriman pesanan berhasil dikonfirmasi');
            }
            
            return redirect()->back()->with('error', $response['message'] ?? 'Gagal mengkonfirmasi pengiriman');
        } catch (\Exception $e) {
            Log::error('Error pada konfirmasi pengiriman: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengkonfirmasi pengiriman');
        }
    }
    
    /**
     * Konfirmasi pengambilan pesanan
     */
    public function confirmPickup($id)
    {
        try {
            $response = $this->sendApiRequest('post', "/admin/pesanan/{$id}/confirm-pickup");
            
            if ($response['success'] ?? false) {
                return redirect()->back()->with('success', 'Pengambilan pesanan berhasil dikonfirmasi');
            }
            
            return redirect()->back()->with('error', $response['message'] ?? 'Gagal mengkonfirmasi pengambilan');
        } catch (\Exception $e) {
            Log::error('Error pada konfirmasi pengambilan: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengkonfirmasi pengambilan');
        }
    }
    
    /**
     * Upload desain
     */
    public function uploadDesain(Request $request, $id)
    {
        try {
            $request->validate([
                'detail_pesanan_id' => 'required',
                'desain' => 'required|file|mimes:jpeg,png,jpg,pdf,ai,psd|max:10240',
                'tipe' => 'required|in:desain_toko,revisi'
            ]);
            
            // Untuk upload file, kita perlu mengirim dengan pendekatan multipart
            $token = session('api_token');
            
            $response = Http::withToken($token)
                ->timeout(30)
                ->attach(
                    'desain',
                    file_get_contents($request->file('desain')->getRealPath()),
                    $request->file('desain')->getClientOriginalName()
                )
                ->post($this->apiBaseUrl . "/admin/pesanan/{$id}/upload-desain", [
                    'detail_pesanan_id' => $request->detail_pesanan_id,
                    'tipe' => $request->tipe
                ]);
            
            $responseData = $response->json();
            
            if ($responseData['success'] ?? false) {
                return redirect()->back()->with('success', 'Desain berhasil diupload');
            }
            
            return redirect()->back()->with('error', $responseData['message'] ?? 'Gagal mengupload desain');
        } catch (\Exception $e) {
            Log::error('Error pada upload desain: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengupload desain');
        }
    }
    
    /**
     * Batalkan pesanan
     */
    public function cancelOrder(Request $request, $id)
    {
        try {
            $request->validate([
                'alasan_batal' => 'nullable|string'
            ]);
            
            $response = $this->sendApiRequest('post', "/admin/pesanan/{$id}/cancel", $request->all());
            
            if ($response['success'] ?? false) {
                return redirect()->route('admin.pesanan.index')->with('success', 'Pesanan berhasil dibatalkan');
            }
            
            return redirect()->back()->with('error', $response['message'] ?? 'Gagal membatalkan pesanan');
        } catch (\Exception $e) {
            Log::error('Error pada pembatalan pesanan: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat membatalkan pesanan');
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