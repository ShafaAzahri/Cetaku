<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;


class PesananController extends Controller
{
    protected $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = rtrim(env('API_URL', config('app.url')), '/');
    }

    /**
     * Menampilkan daftar pesanan
     */
    public function index(Request $request)
    {
        try {
            Log::info('Trying to fetch pesanan data from API', [
                'api_url' => $this->apiBaseUrl,
                'token' => Session::has('api_token') ? 'exists' : 'missing'
            ]);

            // Siapkan parameter untuk API
            $params = [];
            
            // Tambahkan perpage dari request
            $perPage = $request->input('perpage', 10);
            $params['per_page'] = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;
            
            // Tambahkan filter
            if ($request->has('status') && $request->status != 'all') {
                $params['status'] = $request->status;
            }
            
            // Tambahkan pencarian
            if ($request->has('search') && !empty($request->search)) {
                $params['search'] = $request->search;
            }
            
            // Tambahkan sortir
            $params['sort_by'] = $request->input('sort_by', 'created_at');
            $params['sort_order'] = $request->input('sort_order', 'desc');
            
            // Tambahkan halaman
            $params['page'] = $request->input('page', 1);
            
            // Panggil API untuk mendapatkan daftar pesanan
            $response = Http::withToken(session('api_token'))
                ->timeout(30) // Tambah timeout untuk menghindari request timeout
                ->get($this->apiBaseUrl . '/pesanan', $params);
            
            Log::info('API response received', [
                'status' => $response->status(),
                'successful' => $response->successful(),
                'body_preview' => substr($response->body(), 0, 200) . '...'
            ]);

            if (!$response->successful()) {
                $errorMessage = $response->json()['message'] ?? 'Gagal memuat daftar pesanan';
                Log::error('API request failed', [
                    'error' => $errorMessage,
                    'status' => $response->status()
                ]);
                
                return view('admin.pesanan.index', [
                    'pesanan' => collect([]), 
                    'pagination' => null,
                    'perPage' => $perPage,
                    'operators' => [],
                    'mesins' => []
                ])->with('error', $errorMessage);
            }
            
            $responseData = $response->json();
            $data = $responseData['data'];
            
            // Jika data di-paginasi oleh API
            if (isset($data['current_page'])) {
                $pesananData = collect($data['data']);
                $pagination = [
                    'current_page' => $data['current_page'],
                    'last_page' => $data['last_page'],
                    'per_page' => $data['per_page'],
                    'total' => $data['total']
                ];
            } else {
                // Jika data tidak di-paginasi
                $pesananData = collect($data);
                $pagination = null;
            }
            
            // Ambil daftar operator dan mesin untuk modal proses cetak
            $operatorsResponse = Http::withToken(session('api_token'))
                ->get($this->apiBaseUrl . '/operator/list');
                
            $mesinsResponse = Http::withToken(session('api_token'))
                ->get($this->apiBaseUrl . '/mesin/list');
                
            $operators = $operatorsResponse->successful() ? $operatorsResponse->json()['data'] : [];
            $mesins = $mesinsResponse->successful() ? $mesinsResponse->json()['data'] : [];
            
            // Transform data pesanan untuk tampilan
            $pesanan = $pesananData->map(function($item) {
                $totalHarga = 0;
                $produkNama = 'Unknown Product';
                
                // Dapatkan detail produk dari detail pesanan pertama
                if (!empty($item['detail_pesanans'])) {
                    $detailPesanan = $item['detail_pesanans'][0];
                    $produkNama = $detailPesanan['custom']['item']['nama_item'] ?? 'Unknown Product';
                    
                    // Hitung total harga dari semua detail pesanan
                    foreach ($item['detail_pesanans'] as $detail) {
                        $totalHarga += $detail['total_harga'];
                    }
                }
                
                return [
                    'id' => $item['id'],
                    'tanggal' => isset($item['tanggal_dipesan']) ? date('Y-m-d', strtotime($item['tanggal_dipesan'])) : date('Y-m-d'),
                    'pelanggan' => $item['user']['nama'] ?? 'Unknown',
                    'status' => $item['status'],
                    'metode' => $item['metode_pengambilan'] == 'ambil' ? 'Ambil di Tempat' : 'Dikirim',
                    'produk' => $produkNama,
                    'total' => $totalHarga,
                    'detail_pesanans' => $item['detail_pesanans']
                ];
            });
            
            Log::info('Data successfully transformed', [
                'pesanan_count' => $pesanan->count(),
                'pagination' => $pagination ? 'exists' : 'null'
            ]);
            
            return view('admin.pesanan.index', compact('pesanan', 'pagination', 'perPage', 'operators', 'mesins'));
        } catch (\Exception $e) {
            Log::error('Error saat memuat daftar pesanan: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return view('admin.pesanan.index', [
                'pesanan' => collect([]),
                'pagination' => null,
                'perPage' => $request->input('perpage', 10),
                'operators' => [],
                'mesins' => []
            ])->with('error', 'Terjadi kesalahan saat memuat data pesanan: ' . $e->getMessage());
        }
    }
    
    /**
     * Menampilkan detail pesanan
     */
    public function show($id)
    {
        try {
            // Panggil API untuk detail pesanan
            $response = Http::withToken(session('api_token'))
                ->get($this->apiBaseUrl . "/pesanan/{$id}");
            
            if (!$response->successful()) {
                $errorMessage = $response->json()['message'] ?? 'Gagal memuat detail pesanan';
                return redirect()->route('admin.pesanan.index')
                    ->with('error', $errorMessage);
            }
            
            $responseData = $response->json();
            $pesananData = $responseData['data'];
            
            // Transform data pesanan untuk template
            $pesananFormatted = [
                'id' => $pesananData['id'],
                'tanggal' => date('Y-m-d', strtotime($pesananData['tanggal_dipesan'] ?? 'now')),
                'pelanggan' => $pesananData['user']['nama'] ?? 'Unknown',
                'pelanggan_id' => $pesananData['user']['id'] ?? null,
                'status' => $pesananData['status'],
                'alamat' => !empty($pesananData['user']['alamats']) ? $pesananData['user']['alamats'][0]['alamat_lengkap'] : 'Belum ada alamat',
                'metode' => $pesananData['metode_pengambilan'] == 'ambil' ? 'Ambil di Tempat' : 'Dikirim',
                'total' => array_sum(array_column($pesananData['detail_pesanans'], 'total_harga')),
                'estimasi_selesai' => $pesananData['estimasi_waktu'] ? date('Y-m-d', strtotime('+' . $pesananData['estimasi_waktu'] . ' days')) : 'Belum ditentukan',
                'dengan_jasa_edit' => count(array_filter($pesananData['detail_pesanans'], function($detail) {
                    return $detail['tipe_desain'] === 'dibuatkan';
                })) > 0,
                'catatan' => $pesananData['catatan'] ?? 'Tidak ada catatan',
                'produk_items' => []
            ];
            
            // Tambahkan detail produk
            foreach ($pesananData['detail_pesanans'] as $detail) {
                $custom = $detail['custom'] ?? [];
                $item = $custom['item'] ?? [];
                $ukuran = $custom['ukuran'] ?? [];
                $bahan = $custom['bahan'] ?? [];
                $jenis = $custom['jenis'] ?? [];
                
                $produkItem = [
                    'id' => $detail['id'],
                    'nama' => $item['nama_item'] ?? 'Produk tidak diketahui',
                    'bahan' => $bahan['nama_bahan'] ?? 'Unknown',
                    'ukuran' => $ukuran['size'] ?? 'Unknown',
                    'jumlah' => $detail['jumlah'],
                    'harga_satuan' => $custom['harga'] ?? 0,
                    'subtotal' => $detail['total_harga'],
                    'desain_customer' => $detail['upload_desain'] ?? null,
                    'desain_final' => $detail['desain_revisi'] ?? null,
                    'detail' => [
                        'jenis' => $jenis['kategori'] ?? 'Unknown',
                        'gambar' => isset($item['gambar']) ? $item['gambar'] : null,
                        'catatan' => $detail['catatan'] ?? 'Tidak ada catatan khusus.'
                    ]
                ];
                
                $pesananFormatted['produk_items'][] = $produkItem;
            }
            
            return view('admin.pesanan.show', ['pesanan' => $pesananFormatted]);
        } catch (\Exception $e) {
            Log::error('Error saat memuat detail pesanan: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return redirect()->route('admin.pesanan.index')
                ->with('error', "Terjadi kesalahan saat memuat detail pesanan #$id: " . $e->getMessage());
        }
    }
    
    /**
     * Menampilkan detail produk dalam pesanan
     */
    public function getDetailProduk($id, $produkId)
    {
        try {
            // Panggil API untuk detail produk
            $response = Http::withToken(session('api_token'))
                ->get($this->apiBaseUrl . "/pesanan/{$id}/produk/{$produkId}");
            
            if (!$response->successful()) {
                $errorMessage = $response->json()['message'] ?? 'Gagal memuat data detail produk';
                return redirect()->route('admin.pesanan.show', $id)
                    ->with('error', $errorMessage);
            }
            
            $data = $response->json()['data'];
            $productDetail = $data['product'];
            $customer = $data['customer'];
            $alamat = $data['alamat'];
            
            // Format data untuk view
            $produk = [
                'id' => $productDetail['id'],
                'nama' => $productDetail['nama_item'] ?? 'Produk tidak diketahui',
                'jenis' => $productDetail['jenis'] ?? 'Unknown',
                'bahan' => $productDetail['bahan'] ?? 'Unknown',
                'ukuran' => $productDetail['ukuran'] ?? 'Unknown',
                'jumlah' => $productDetail['jumlah'],
                'harga' => $productDetail['harga_satuan'],
                'subtotal' => $productDetail['total_harga'],
                'tipe_desain' => $productDetail['tipe_desain'],
                'biaya_desain' => $productDetail['biaya_desain'],
                'catatan' => $productDetail['catatan'] ?? 'Tidak ada catatan khusus',
                'gambar_url' => $productDetail['gambar'] ?? asset('images/no-image.png'),
                'desain_customer_url' => $productDetail['upload_desain'],
                'desain_final_url' => $productDetail['desain_revisi'],
            ];
            
            // Perlu membuat objek Pesanan untuk template 
            // (agar bisa mengakses route dinamis, $pesanan->id)
            $pesanan = new \stdClass();
            $pesanan->id = $id;
            
            return view('admin.pesanan.detail-produk', [
                'pesanan' => $pesanan,
                'produk' => $produk,
                'alamat' => $alamat ? $alamat['alamat_lengkap'] : 'Belum ada alamat',
                'pelanggan' => $customer
            ]);
        } catch (\Exception $e) {
            Log::error('Error saat memuat detail produk: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return redirect()->route('admin.pesanan.show', $id)
                ->with('error', "Terjadi kesalahan saat memuat detail produk: " . $e->getMessage());
        }
    }
    
    /**
     * Mengubah status pesanan
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'status' => 'required|string|in:Pemesanan,Dikonfirmasi,Sedang Diproses,Menunggu Pengambilan,Sedang Dikirim,Selesai,Dibatalkan',
                'catatan' => 'nullable|string|max:255',
            ]);
            
            // Panggil API untuk update status
            $response = Http::withToken(session('api_token'))
                ->put($this->apiBaseUrl . "/pesanan/{$id}/status", [
                    'status' => $request->status,
                    'catatan' => $request->catatan
                ]);
                
            if (!$response->successful()) {
                $errorData = $response->json();
                $errorMessage = $errorData['message'] ?? 'Gagal mengubah status pesanan';
                
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage
                    ], $response->status());
                }
                
                return redirect()->back()->with('error', $errorMessage);
            }
            
            $responseData = $response->json();
            
            // Log perubahan status
            Log::info('Status pesanan diubah', [
                'id' => $id,
                'status' => $request->status,
                'admin_id' => session('user')['id'] ?? null
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Status pesanan #$id berhasil diubah menjadi $request->status",
                    'status' => $request->status,
                    'badgeClass' => $this->getBadgeClassForStatus($request->status)
                ]);
            }
            
            return redirect()->route('admin.pesanan.show', $id)
                ->with('success', "Status pesanan #$id berhasil diubah menjadi $request->status");
        } catch (\Exception $e) {
            Log::error('Error saat update status pesanan: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => "Terjadi kesalahan saat mengubah status pesanan: " . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', "Terjadi kesalahan saat mengubah status pesanan: " . $e->getMessage());
        }
    }
    
    /**
     * Upload desain untuk produk dalam pesanan
     */
    public function uploadDesain(Request $request, $id)
    {
        // Validasi input
        $validated = $request->validate([
            'desain' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'produk_id' => 'required|integer',
            'tipe' => 'required|in:customer,final',
            'catatan' => 'nullable|string|max:255',
        ]);
        
        try {
            // Upload file
            if ($request->hasFile('desain') && $request->file('desain')->isValid()) {
                $file = $request->file('desain');
                $fileName = 'desain_' . $request->tipe . '_pesanan_' . $id . '_produk_' . $request->produk_id . '_' . time() . '.' . $file->getClientOriginalExtension();
                
                // Pastikan direktori ada
                $directoryPath = storage_path('app/public/desain');
                if (!file_exists($directoryPath)) {
                    mkdir($directoryPath, 0755, true);
                }
                
                // Simpan file
                $path = $request->file('desain')->storeAs('public/desain', $fileName);
                
                // Siapkan tipe untuk API
                $tipeAPI = $request->tipe === 'customer' ? 'upload_desain' : 'desain_revisi';
                
                // Panggil API untuk update desain
                $response = Http::withToken(session('api_token'))
                    ->post($this->apiBaseUrl . "/pesanan/{$id}/desain", [
                        'detail_pesanan_id' => $request->produk_id,
                        'tipe' => $tipeAPI,
                        'file_name' => $fileName,
                        'catatan' => $request->catatan
                    ]);
                
                if (!$response->successful()) {
                    // Hapus file jika API gagal
                    if (Storage::exists($path)) {
                        Storage::delete($path);
                    }
                    
                    $errorMessage = $response->json()['message'] ?? 'Gagal mengupload desain';
                    
                    if ($request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => $errorMessage
                        ], $response->status());
                    }
                    
                    return redirect()->back()->with('error', $errorMessage);
                }
                
                $responseData = $response->json();
                
                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Desain berhasil diupload',
                        'file_name' => $fileName,
                        'file_url' => asset('storage/desain/' . $fileName)
                    ]);
                }
                
                return redirect()->route('admin.pesanan.detail-produk', ['id' => $id, 'produk_id' => $request->produk_id])
                    ->with('success', 'Desain berhasil diupload');
            }
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada file yang diupload'
                ], 400);
            }
            
            return redirect()->back()->with('error', 'Tidak ada file yang diupload');
        } catch (\Exception $e) {
            Log::error('Error saat upload desain: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => "Terjadi kesalahan saat mengupload desain: " . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', "Terjadi kesalahan saat mengupload desain: " . $e->getMessage());
        }
    }
    
    /**
     * Proses cetak pesanan
     */
    public function processPrint(Request $request, $id)
    {
        try {
            // Panggil API untuk proses cetak
            $response = Http::withToken(session('api_token'))
                ->post($this->apiBaseUrl . "/pesanan/{$id}/proses", [
                    'detail_pesanan_id' => $request->detail_pesanan_id,
                    'operator_id' => $request->operator_id,
                    'mesin_id' => $request->mesin_id,
                    'catatan' => $request->catatan
                ]);
            
            if (!$response->successful()) {
                $errorMessage = $response->json()['message'] ?? 'Gagal memproses cetak pesanan';
                return redirect()->back()->with('error', $errorMessage);
            }
            
            return redirect()->route('admin.pesanan.show', $id)
                ->with('success', "Pesanan #$id berhasil masuk proses cetak");
        } catch (\Exception $e) {
            Log::error('Error saat proses cetak: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return redirect()->back()
                ->with('error', "Terjadi kesalahan saat memproses cetak: " . $e->getMessage());
        }
    }
    
    /**
     * Konfirmasi pengambilan pesanan
     */
    public function confirmPickup(Request $request, $id)
    {
        try {
            // Panggil API untuk konfirmasi pengambilan
            $response = Http::withToken(session('api_token'))
                ->post($this->apiBaseUrl . "/pesanan/{$id}/konfirmasi-pengambilan", [
                    'catatan' => $request->catatan
                ]);
            
            if (!$response->successful()) {
                $errorMessage = $response->json()['message'] ?? 'Gagal mengkonfirmasi pengambilan pesanan';
                
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage
                    ], $response->status());
                }
                
                return redirect()->back()->with('error', $errorMessage);
            }
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Pengambilan pesanan #$id berhasil dikonfirmasi"
                ]);
            }
            
            return redirect()->route('admin.pesanan.index')
                ->with('success', "Pengambilan pesanan #$id berhasil dikonfirmasi");
        } catch (\Exception $e) {
            Log::error('Error saat konfirmasi pengambilan: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => "Terjadi kesalahan saat mengkonfirmasi pengambilan: " . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', "Terjadi kesalahan saat mengkonfirmasi pengambilan: " . $e->getMessage());
        }
    }
    
    /**
     * Konfirmasi pengiriman pesanan
     */
    public function confirmShipment(Request $request, $id)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'no_resi' => 'nullable|string|max:50',
                'catatan' => 'nullable|string|max:255',
            ]);
            
            // Panggil API untuk konfirmasi pengiriman
            $response = Http::withToken(session('api_token'))
                ->post($this->apiBaseUrl . "/pesanan/{$id}/konfirmasi-pengiriman", [
                    'no_resi' => $request->no_resi,
                    'catatan' => $request->catatan
                ]);
            
            if (!$response->successful()) {
                $errorMessage = $response->json()['message'] ?? 'Gagal mengkonfirmasi pengiriman pesanan';
                return redirect()->back()->with('error', $errorMessage);
            }
            
            return redirect()->route('admin.pesanan.index')
                ->with('success', "Pengiriman pesanan #$id berhasil dikonfirmasi");
        } catch (\Exception $e) {
            Log::error('Error saat konfirmasi pengiriman: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return redirect()->back()
                ->with('error', "Terjadi kesalahan saat mengkonfirmasi pengiriman: " . $e->getMessage());
        }
    }
    
    /**
     * Konfirmasi penerimaan pesanan
     */
    public function confirmReceived(Request $request, $id)
    {
        try {
            // Panggil API untuk konfirmasi penerimaan
            $response = Http::withToken(session('api_token'))
                ->post($this->apiBaseUrl . "/pesanan/{$id}/konfirmasi-penerimaan", [
                    'catatan' => $request->catatan
                ]);
            
            if (!$response->successful()) {
                $errorMessage = $response->json()['message'] ?? 'Gagal mengkonfirmasi penerimaan pesanan';
                
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage
                    ], $response->status());
                }
                
                return redirect()->back()->with('error', $errorMessage);
            }
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Penerimaan pesanan #$id berhasil dikonfirmasi"
                ]);
            }
            
            return redirect()->route('admin.pesanan.index')
                ->with('success', "Penerimaan pesanan #$id berhasil dikonfirmasi");
        } catch (\Exception $e) {
            Log::error('Error saat konfirmasi penerimaan: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => "Terjadi kesalahan saat mengkonfirmasi penerimaan: " . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', "Terjadi kesalahan saat mengkonfirmasi penerimaan: " . $e->getMessage());
        }
    }
    
    /**
     * Mencetak invoice pesanan
     */
    public function printInvoice($id)
    {
        try {
            // Panggil API untuk mendapatkan detail pesanan untuk invoice
            $response = Http::withToken(session('api_token'))
                ->get($this->apiBaseUrl . "/pesanan/{$id}");
            
            if (!$response->successful()) {
                $errorMessage = $response->json()['message'] ?? 'Gagal memuat data pesanan untuk invoice';
                return redirect()->route('admin.pesanan.show', $id)
                    ->with('error', $errorMessage);
            }
            
            $responseData = $response->json();
            $pesananData = $responseData['data'];
            
            // Format data untuk view invoice
            $invoice = [
                'id' => $pesananData['id'],
                'tanggal' => date('Y-m-d', strtotime($pesananData['tanggal_dipesan'] ?? 'now')),
                'pelanggan' => $pesananData['user']['nama'] ?? 'Unknown',
                'alamat' => !empty($pesananData['user']['alamats']) ? $pesananData['user']['alamats'][0]['alamat_lengkap'] : 'Belum ada alamat',
                'metode' => $pesananData['metode_pengambilan'] == 'ambil' ? 'Ambil di Tempat' : 'Dikirim',
                'status' => $pesananData['status'],
                'total' => array_sum(array_column($pesananData['detail_pesanans'], 'total_harga')),
                'produk_items' => []
            ];
            
            // Tambahkan detail produk untuk invoice
            foreach ($pesananData['detail_pesanans'] as $detail) {
                $custom = $detail['custom'] ?? [];
                $item = $custom['item'] ?? [];
                $ukuran = $custom['ukuran'] ?? [];
                $bahan = $custom['bahan'] ?? [];
                
                $produkItem = [
                    'nama' => $item['nama_item'] ?? 'Produk tidak diketahui',
                    'bahan' => $bahan['nama_bahan'] ?? 'Unknown',
                    'ukuran' => $ukuran['size'] ?? 'Unknown',
                    'jumlah' => $detail['jumlah'],
                    'harga_satuan' => $custom['harga'] ?? 0,
                    'subtotal' => $detail['total_harga'],
                ];
                
                $invoice['produk_items'][] = $produkItem;
            }
            
            return view('admin.pesanan.invoice', ['pesananData' => $invoice]);
        } catch (\Exception $e) {
            Log::error('Error saat mencetak invoice: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return redirect()->route('admin.pesanan.show', $id)
                ->with('error', "Terjadi kesalahan saat mencetak invoice: " . $e->getMessage());
        }
    }
    
    /**
    * Helper method untuk mendapatkan kelas badge berdasarkan status
    */
   private function getBadgeClassForStatus($status)
   {
       switch($status) {
           case 'Pemesanan':
               return 'bg-warning text-dark';
           case 'Dikonfirmasi':
               return 'bg-info text-white';
           case 'Sedang Diproses':
               return 'bg-primary text-white';
           case 'Menunggu Pengambilan':
               return 'bg-indigo text-white';
           case 'Sedang Dikirim':
               return 'bg-info text-white';
           case 'Selesai':
               return 'bg-success text-white';
           case 'Dibatalkan':
               return 'bg-danger text-white';
           default:
               return 'bg-secondary text-white';
       }
   }
   
   /**
    * Cancel pesanan
    */
   public function cancel(Request $request, $id)
   {
       try {
           // Validasi input
           $validated = $request->validate([
               'alasan' => 'nullable|string|max:255',
           ]);
           
           // Panggil API untuk update status
           $response = Http::withToken(session('api_token'))
               ->put($this->apiBaseUrl . "/pesanan/{$id}/status", [
                   'status' => 'Dibatalkan',
                   'catatan' => 'Dibatalkan: ' . ($request->alasan ?? 'Tidak ada alasan')
               ]);
               
           if (!$response->successful()) {
               $errorMessage = $response->json()['message'] ?? 'Gagal membatalkan pesanan';
               return redirect()->back()->with('error', $errorMessage);
           }
           
           return redirect()->route('admin.pesanan.index')
               ->with('success', "Pesanan #$id berhasil dibatalkan");
       } catch (\Exception $e) {
           Log::error('Error saat membatalkan pesanan: ' . $e->getMessage(), [
               'file' => $e->getFile(),
               'line' => $e->getLine()
           ]);
           
           return redirect()->back()
               ->with('error', "Terjadi kesalahan saat membatalkan pesanan: " . $e->getMessage());
       }
   }
   
   /**
    * Get order statistics
    */
   public function statistics()
   {
       try {
           // Panggil API untuk mendapatkan statistik pesanan
           $response = Http::withToken(session('api_token'))
               ->get($this->apiBaseUrl . "/pesanan/statistik");
           
           if (!$response->successful()) {
               $errorMessage = $response->json()['message'] ?? 'Gagal memuat statistik pesanan';
               return response()->json([
                   'success' => false,
                   'message' => $errorMessage
               ], $response->status());
           }
           
           $responseData = $response->json();
           $data = $responseData['data'];
           
           return response()->json([
               'success' => true,
               'data' => $data,
               'message' => 'Statistik pesanan berhasil dimuat'
           ]);
       } catch (\Exception $e) {
           Log::error('Error saat memuat statistik pesanan: ' . $e->getMessage(), [
               'file' => $e->getFile(),
               'line' => $e->getLine()
           ]);
           
           return response()->json([
               'success' => false,
               'message' => "Terjadi kesalahan saat memuat statistik pesanan: " . $e->getMessage()
           ], 500);
       }
   }
   
   /**
    * Display search form and results for orders
    */
   public function search(Request $request)
   {
       try {
           $searchTerm = $request->input('q');
           
           if (empty($searchTerm)) {
               return view('admin.pesanan.search', [
                   'results' => collect([]),
                   'searchTerm' => '',
                   'hasSearch' => false
               ]);
           }
           
           // Call API to search for orders
           $response = Http::withToken(session('api_token'))
               ->get($this->apiBaseUrl . "/pesanan", [
                   'search' => $searchTerm
               ]);
           
           if (!$response->successful()) {
               $errorMessage = $response->json()['message'] ?? 'Gagal mencari pesanan';
               return view('admin.pesanan.search', [
                   'results' => collect([]),
                   'searchTerm' => $searchTerm,
                   'hasSearch' => true,
                   'error' => $errorMessage
               ]);
           }
           
           $responseData = $response->json();
           $data = $responseData['data'];
           
           // Transform data for view
           $results = collect($data)->map(function($item) {
               return [
                   'id' => $item['id'],
                   'tanggal' => isset($item['tanggal_dipesan']) ? date('Y-m-d', strtotime($item['tanggal_dipesan'])) : date('Y-m-d'),
                   'pelanggan' => $item['user']['nama'] ?? 'Unknown',
                   'status' => $item['status'],
                   'total' => array_sum(array_column($item['detail_pesanans'] ?? [], 'total_harga')),
               ];
           });
           
           return view('admin.pesanan.search', [
               'results' => $results,
               'searchTerm' => $searchTerm,
               'hasSearch' => true
           ]);
       } catch (\Exception $e) {
           Log::error('Error saat mencari pesanan: ' . $e->getMessage(), [
               'file' => $e->getFile(),
               'line' => $e->getLine()
           ]);
           
           return view('admin.pesanan.search', [
               'results' => collect([]),
               'searchTerm' => $searchTerm ?? '',
               'hasSearch' => !empty($searchTerm),
               'error' => "Terjadi kesalahan saat mencari pesanan: " . $e->getMessage()
           ]);
       }
   }
}