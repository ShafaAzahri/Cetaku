<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PesananController extends Controller
{
    private readonly string $apiUrl;
    
    public function __construct()
    {
        $this->apiUrl = rtrim(config('app.api_url', config('app.url')), '/') . '/api';
    }
    
    /**
     * Menampilkan daftar pesanan
     */
    public function index(Request $request)
    {
        try {
            $params = $this->buildFilterParams($request);
            $response = $this->callApi('GET', '/pesanan', $params);
            
            $data = [
                'pesanan' => $this->formatPesananList($response['data'] ?? []),
                'pagination' => $this->extractPagination($response),
                'filters' => $request->only(['status', 'search', 'start_date', 'end_date']),
                'perPage' => $request->get('perpage', 10)
            ];
            
            return view('admin.pesanan.index', $data);
        } catch (\Exception $e) {
            Log::error('Error loading pesanan list: ' . $e->getMessage());
            return view('admin.pesanan.index')
                ->with('error', 'Gagal memuat daftar pesanan: ' . $e->getMessage());
        }
    }
    
    /**
     * Menampilkan detail pesanan
     */
    public function show($id)
    {
        try {
            $pesanan = $this->callApi('GET', "/pesanan/{$id}")['data'];
            $operators = $this->callApi('GET', '/operator/list')['data'] ?? [];
            $mesins = $this->callApi('GET', '/mesin/list')['data'] ?? [];
            
            return view('admin.pesanan.show', [
                'pesanan' => $this->formatPesananDetail($pesanan),
                'operators' => $operators,
                'mesins' => $mesins,
                'statusList' => $this->getStatusOptions()
            ]);
        } catch (\Exception $e) {
            Log::error("Error loading pesanan {$id}: " . $e->getMessage());
            return redirect()->route('admin.pesanan.index')
                ->with('error', 'Pesanan tidak ditemukan');
        }
    }
    
    /**
     * Menampilkan detail produk dalam pesanan
     */
    public function getDetailProduk($id, $produkId)
    {
        try {
            $response = $this->callApi('GET', "/pesanan/{$id}/produk/{$produkId}");
            $data = $response['data'];
            
            return view('admin.pesanan.detail-produk', [
                'pesanan' => $data['pesanan'],
                'produk' => $data['product'],
                'alamat' => $data['alamat'],
                'pelanggan' => $data['customer']
            ]);
        } catch (\Exception $e) {
            Log::error("Error loading product detail: " . $e->getMessage());
            return redirect()->route('admin.pesanan.show', $id)
                ->with('error', 'Detail produk tidak ditemukan');
        }
    }
    
    /**
     * Konfirmasi pesanan
     */
    public function konfirmasi($id)
    {
        try {
            $pesanan = $this->callApi('GET', "/pesanan/{$id}")['data'];
            
            if ($pesanan['status'] !== 'Pemesanan') {
                return redirect()->route('admin.pesanan.show', $id)
                    ->with('error', 'Pesanan ini tidak dapat dikonfirmasi');
            }
            
            return view('admin.pesanan.konfirmasi', ['pesanan' => $pesanan]);
        } catch (\Exception $e) {
            return $this->handleException($e, 'admin.pesanan.index');
        }
    }
    
    /**
     * Proses konfirmasi pesanan
     */
    public function prosesKonfirmasi(Request $request, $id)
    {
        $request->validate([
            'catatan' => 'nullable|string|max:500'
        ]);
        
        try {
            $this->callApi('PUT', "/pesanan/{$id}/status", [
                'status' => 'Dikonfirmasi',
                'catatan' => $request->catatan
            ]);
            
            return redirect()->route('admin.pesanan.show', $id)
                ->with('success', 'Pesanan berhasil dikonfirmasi');
        } catch (\Exception $e) {
            return $this->handleException($e, 'admin.pesanan.show', $id);
        }
    }
    
    /**
     * Halaman proses produksi
     */
    public function proses($id)
    {
        try {
            $pesanan = $this->callApi('GET', "/pesanan/{$id}")['data'];
            $operators = $this->callApi('GET', '/operator/list')['data'] ?? [];
            $mesins = $this->callApi('GET', '/mesin/list')['data'] ?? [];
            
            if (!in_array($pesanan['status'], ['Dikonfirmasi', 'Sedang Diproses'])) {
                return redirect()->route('admin.pesanan.show', $id)
                    ->with('error', 'Pesanan tidak dapat diproses');
            }
            
            return view('admin.pesanan.proses', [
                'pesanan' => $pesanan,
                'operators' => $operators,
                'mesins' => $mesins
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e, 'admin.pesanan.show', $id);
        }
    }
    
    /**
     * Proses produksi pesanan
     */
    public function prosesPrint(Request $request, $id)
    {
        $request->validate([
            'operator_id' => 'required|exists:operators,id',
            'mesin_id' => 'required|exists:mesins,id',
            'detail_pesanan_id' => 'nullable|exists:detail_pesanans,id',
            'catatan' => 'nullable|string|max:500'
        ]);
        
        try {
            // Jika tidak ada detail_pesanan_id, ambil semua detail pesanan
            if (!$request->detail_pesanan_id) {
                $pesanan = $this->callApi('GET', "/pesanan/{$id}")['data'];
                
                foreach ($pesanan['detailPesanans'] as $detail) {
                    try {
                        $this->callApi('POST', "/pesanan/{$id}/proses", [
                            'detail_pesanan_id' => $detail['id'],
                            'operator_id' => $request->operator_id,
                            'mesin_id' => $request->mesin_id,
                            'catatan' => $request->catatan
                        ]);
                    } catch (\Exception $e) {
                        Log::warning("Failed to assign process for detail {$detail['id']}: " . $e->getMessage());
                    }
                }
            } else {
                $this->callApi('POST', "/pesanan/{$id}/proses", $request->all());
            }
            
            return redirect()->route('admin.pesanan.show', $id)
                ->with('success', 'Pesanan berhasil masuk proses produksi');
        } catch (\Exception $e) {
            return $this->handleException($e, 'admin.pesanan.proses', $id);
        }
    }
    
    /**
     * Update status pesanan
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', array_keys($this->getStatusOptions())),
            'catatan' => 'nullable|string|max:500'
        ]);
        
        try {
            $this->callApi('PUT', "/pesanan/{$id}/status", $request->only(['status', 'catatan']));
            
            return redirect()->route('admin.pesanan.show', $id)
                ->with('success', 'Status pesanan berhasil diperbarui');
        } catch (\Exception $e) {
            return $this->handleException($e, 'admin.pesanan.show', $id);
        }
    }
    
    /**
     * Konfirmasi pengambilan
     */
    public function confirmPickup($id)
    {
        return $this->confirmAction($id, 'confirm-pickup', '/pesanan/{id}/konfirmasi-pengambilan');
    }
    
    /**
     * Konfirmasi pengiriman
     */
    public function kirim($id)
    {
        try {
            $pesanan = $this->callApi('GET', "/pesanan/{$id}")['data'];
            
            if ($pesanan['status'] !== 'Sedang Diproses' || $pesanan['metode_pengambilan'] !== 'antar') {
                return redirect()->route('admin.pesanan.show', $id)
                    ->with('error', 'Pesanan tidak dapat dikirim');
            }
            
            return view('admin.pesanan.kirim', ['pesanan' => $pesanan]);
        } catch (\Exception $e) {
            return $this->handleException($e, 'admin.pesanan.show', $id);
        }
    }
    
    /**
     * Proses pengiriman
     */
    public function prosesKirim(Request $request, $id)
    {
        $request->validate([
            'no_resi' => 'nullable|string|max:50',
            'catatan' => 'nullable|string|max:500'
        ]);
        
        try {
            $this->callApi('POST', "/pesanan/{$id}/konfirmasi-pengiriman", $request->all());
            
            return redirect()->route('admin.pesanan.show', $id)
                ->with('success', 'Pesanan berhasil dikirim');
        } catch (\Exception $e) {
            return $this->handleException($e, 'admin.pesanan.show', $id);
        }
    }
    
    /**
     * Konfirmasi penerimaan
     */
    public function confirmDelivery($id)
    {
        return $this->confirmAction($id, 'confirm-delivery', '/pesanan/{id}/konfirmasi-penerimaan');
    }
    
    /**
     * Batal pesanan
     */
    public function cancel(Request $request, $id)
    {
        try {
            $this->callApi('PUT', "/pesanan/{$id}/status", [
                'status' => 'Dibatalkan',
                'catatan' => $request->input('alasan', 'Dibatalkan oleh admin')
            ]);
            
            return redirect()->route('admin.pesanan.index')
                ->with('success', 'Pesanan berhasil dibatalkan');
        } catch (\Exception $e) {
            return $this->handleException($e, 'admin.pesanan.show', $id);
        }
    }
    
    /**
     * Upload desain
     */
    public function uploadDesain(Request $request, $id)
    {
        $request->validate([
            'produk_id' => 'required|integer',
            'tipe' => 'required|in:customer,final',
            'desain' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'catatan' => 'nullable|string|max:500'
        ]);
        
        try {
            $file = $request->file('desain');
            $fileName = $this->generateDesainFileName($id, $request->produk_id, $request->tipe, $file);
            $file->storeAs('public/desain', $fileName);
            
            $this->callApi('POST', "/pesanan/{$id}/desain", [
                'detail_pesanan_id' => $request->produk_id,
                'tipe' => $request->tipe === 'customer' ? 'upload_desain' : 'desain_revisi',
                'file_name' => $fileName,
                'catatan' => $request->catatan
            ]);
            
            return redirect()->route('admin.pesanan.detail-produk', ['id' => $id, 'produk_id' => $request->produk_id])
                ->with('success', 'Desain berhasil diupload');
        } catch (\Exception $e) {
            return $this->handleException($e, 'admin.pesanan.detail-produk', ['id' => $id, 'produk_id' => $request->produk_id]);
        }
    }
    
    // Private Helper Methods
    
    private function callApi(string $method, string $endpoint, ?array $data = null)
    {
        $response = Http::withToken(session('api_token'))
            ->timeout(30)
            ->$method($this->apiUrl . $endpoint, $data);
        
        if (!$response->successful()) {
            throw new \Exception($response->json('message', 'API Error'));
        }
        
        return $response->json();
    }
    
    private function buildFilterParams(Request $request): array
    {
        $params = [];
        
        if ($request->filled('status') && $request->status !== 'all') {
            $params['status'] = $request->status;
        }
        
        if ($request->filled('search')) {
            $params['search'] = $request->search;
        }
        
        if ($request->filled('start_date')) {
            $params['start_date'] = $request->start_date;
        }
        
        if ($request->filled('end_date')) {
            $params['end_date'] = $request->end_date;
        }
        
        if ($request->has('perpage')) {
            $params['paginate'] = 'true';
            $params['per_page'] = $request->perpage;
        }
        
        return $params;
    }
    
    private function formatPesananList(array $pesanan): array
    {
        return array_map(function ($item) {
            $totalHarga = array_sum(array_column($item['detailPesanans'] ?? [], 'total_harga'));
            $firstDetail = $item['detailPesanans'][0] ?? null;
            $produkNama = $firstDetail['custom']['item']['nama_item'] ?? 'Produk tidak diketahui';
            
            return [
                'id' => $item['id'],
                'tanggal' => date('Y-m-d', strtotime($item['tanggal_dipesan'] ?? now())),
                'pelanggan' => $item['user']['nama'] ?? 'Unknown',
                'status' => $item['status'],
                'metode' => $item['metode_pengambilan'] === 'ambil' ? 'Ambil di Tempat' : 'Dikirim',
                'produk' => $produkNama,
                'total' => $totalHarga,
                'detail_pesanans' => $item['detailPesanans']
            ];
        }, $pesanan);
    }
    
    private function formatPesananDetail(array $pesanan): array
    {
        $formatted = [
            'id' => $pesanan['id'],
            'tanggal' => date('Y-m-d', strtotime($pesanan['tanggal_dipesan'] ?? now())),
            'pelanggan' => $pesanan['user']['nama'] ?? 'Unknown',
            'pelanggan_id' => $pesanan['user']['id'] ?? null,
            'status' => $pesanan['status'],
            'metode' => $pesanan['metode_pengambilan'] === 'ambil' ? 'Ambil di Tempat' : 'Dikirim',
            'alamat' => $this->getAlamat($pesanan),
            'total' => array_sum(array_column($pesanan['detailPesanans'] ?? [], 'total_harga')),
            'estimasi_selesai' => $this->getEstimasiSelesai($pesanan),
            'dengan_jasa_edit' => $this->hasJasaEdit($pesanan),
            'catatan' => $pesanan['catatan'] ?? 'Tidak ada catatan',
            'produk_items' => $this->formatProdukItems($pesanan['detailPesanans'] ?? [])
        ];
        
        return $formatted;
    }
    
    private function getAlamat(array $pesanan): string
    {
        $alamats = $pesanan['user']['alamats'] ?? [];
        return !empty($alamats) ? $alamats[0]['alamat_lengkap'] : 'Belum ada alamat';
    }
    
    private function getEstimasiSelesai(array $pesanan): string
    {
        if (!isset($pesanan['estimasi_waktu'])) {
            return 'Belum ditentukan';
        }
        
        return date('Y-m-d', strtotime('+' . $pesanan['estimasi_waktu'] . ' days'));
    }
    
    private function hasJasaEdit(array $pesanan): bool
    {
        $details = $pesanan['detailPesanans'] ?? [];
        return count(array_filter($details, fn($d) => $d['tipe_desain'] === 'dibuatkan')) > 0;
    }
    
    private function formatProdukItems(array $detailPesanans): array
    {
        return array_map(function ($detail) {
            $custom = $detail['custom'] ?? [];
            $item = $custom['item'] ?? null;
            $ukuran = $custom['ukuran'] ?? null;
            $bahan = $custom['bahan'] ?? null;
            $jenis = $custom['jenis'] ?? null;
            
            return [
                'id' => $detail['id'],
                'nama' => $item ? $item['nama_item'] : 'Produk tidak diketahui',
                'bahan' => $bahan ? $bahan['nama_bahan'] : 'Unknown',
                'ukuran' => $ukuran ? $ukuran['size'] : 'Unknown',
                'jumlah' => $detail['jumlah'],
                'harga_satuan' => $custom ? $custom['harga'] : 0,
                'subtotal' => $detail['total_harga'],
                'desain_customer' => $detail['upload_desain'] ?? null,
                'desain_final' => $detail['desain_revisi'] ?? null,
                'detail' => [
                    'jenis' => $jenis ? $jenis['kategori'] : 'Unknown',
                    'gambar' => $item && $item['gambar'] ? $item['gambar'] : null,
                    'catatan' => $detail['catatan'] ?? 'Tidak ada catatan khusus.'
                ]
            ];
        }, $detailPesanans);
    }
    
    private function extractPagination(array $response): array
    {
        if (!isset($response['data']['data'])) {
            return ['current_page' => 1, 'last_page' => 1, 'total' => count($response['data'] ?? [])];
        }
        
        return [
            'current_page' => $response['data']['current_page'] ?? 1,
            'last_page' => $response['data']['last_page'] ?? 1,
            'per_page' => $response['data']['per_page'] ?? 10,
            'total' => $response['data']['total'] ?? 0
        ];
    }
    
    private function getStatusOptions(): array
    {
        return [
            'Pemesanan' => 'Pemesanan',
            'Dikonfirmasi' => 'Dikonfirmasi',
            'Sedang Diproses' => 'Sedang Diproses',
            'Menunggu Pengambilan' => 'Menunggu Pengambilan',
            'Sedang Dikirim' => 'Sedang Dikirim',
            'Selesai' => 'Selesai',
            'Dibatalkan' => 'Dibatalkan'
        ];
    }
    
    private function generateDesainFileName($pesananId, $produkId, $tipe, $file): string
    {
        return 'desain_' . $tipe . '_pesanan_' . $pesananId . '_produk_' . $produkId . '_' . time() . '.' . $file->getClientOriginalExtension();
    }
    
    private function confirmAction($id, $viewAction, $apiEndpoint)
    {
        try {
            $pesanan = $this->callApi('GET', "/pesanan/{$id}")['data'];
            
            $statusMap = [
                'confirm-pickup' => 'Menunggu Pengambilan',
                'confirm-delivery' => 'Sedang Dikirim'
            ];
            
            if (!isset($statusMap[$viewAction]) || $pesanan['status'] !== $statusMap[$viewAction]) {
                return redirect()->route('admin.pesanan.show', $id)
                    ->with('error', 'Pesanan tidak dapat diproses untuk tindakan ini');
            }
            
            $this->callApi('POST', str_replace('{id}', $id, $apiEndpoint));
            
            return redirect()->route('admin.pesanan.index')
                ->with('success', 'Tindakan berhasil dilakukan');
        } catch (\Exception $e) {
            return $this->handleException($e, 'admin.pesanan.show', $id);
        }
    }
    
    private function handleException(\Exception $e, string $route, $params = null)
    {
        Log::error($e->getMessage());
        
        $url = is_array($params) ? route($route, $params) : route($route, $params);
        return redirect($url)->with('error', $e->getMessage());
    }
}