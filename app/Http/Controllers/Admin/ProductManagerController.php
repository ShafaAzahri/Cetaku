<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProductManagerController extends Controller
{
    protected $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = rtrim(env('API_URL', config('app.url')), '/');
    }
    
    /**
     * Tampilkan halaman product manager
     */
    public function index()
    {
        $activeTab = request('tab', 'items');
        $data = ['activeTab' => $activeTab];
        
        // Selalu ambil data items untuk dropdown di modal
        $data['items'] = $this->fetchData('/items');
        
        // Ambil data sesuai tab yang aktif
        switch ($activeTab) {
            case 'kategori':
                $data['kategoris'] = $this->fetchData('/kategoris');
                break;
            case 'bahan':
                $data['bahans'] = $this->fetchData('/bahans');
                break;
            case 'jenis':
                $data['jenis_list'] = $this->fetchData('/jenis');
                break;
            case 'ukuran':
                $data['ukurans'] = $this->fetchData('/ukurans');
                break;
            case 'biaya-desain':
                $data['biaya_desains'] = $this->fetchData('/biaya-desains');
                break;
        }
        
        return view('admin.product.product-manager', $data);
    }
    
    /**
     * Fetch data from API
     */
    private function fetchData($endpoint)
    {
        try {
            $token = session('api_token');
            
            $response = Http::withToken($token)->get($this->apiBaseUrl . $endpoint);
            
            if ($response->successful()) {
                $data = $response->json();
                
                // Handle berbagai format key berdasarkan endpoint
                if ($endpoint === '/biaya-desains') {
                    // Coba beberapa kemungkinan key
                    if (isset($data['biaya_desains'])) {
                        return $data['biaya_desains'];
                    } elseif (isset($data['biayaDesains'])) {
                        return $data['biayaDesains'];
                    } elseif (isset($data['biaya-desains'])) {
                        return $data['biaya-desains'];
                    }
                    
                    return [];
                }
                
                // Default handling untuk endpoint lain
                $key = basename($endpoint);
                return $data[$key] ?? [];
            } else {
            }
            
            return [];
        } catch (\Exception $e) {
            return [];
        }
    }
    
    /**
     * Send API request with authentication
     */
    private function sendApiRequest($method, $endpoint, Request $request = null, $hasFile = false)
    {
        $token = session('api_token');
        try {
            if ($hasFile && $request && $request->hasFile('gambar')) {
                // For file uploads, we need to use a multipart form request
                $response = Http::withToken($token)
                    ->timeout(30)
                    ->withHeaders([
                        'Accept' => 'application/json'
                    ])
                    ->attach(
                        'gambar', 
                        file_get_contents($request->file('gambar')->getRealPath()),
                        $request->file('gambar')->getClientOriginalName()
                    )
                    ->$method($this->apiBaseUrl . $endpoint, $request->except('gambar'));
            } else {
                $data = $request ? $request->all() : [];
                $response = Http::withToken($token)
                    ->withHeaders([
                        'Accept' => 'application/json'
                    ])
                    ->$method($this->apiBaseUrl . $endpoint, $data);
            }
            $responseData = $response->json();
            return $responseData;
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghubungi server: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Menyimpan kategori baru
     */
    public function storeKategori(Request $request)
    {
        try {
            // Validasi lokal terlebih dahulu
            $request->validate([
                'nama_kategori' => 'required|string|max:255',
                'deskripsi' => 'nullable|string',
                'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'item_ids' => 'nullable|array',
                'item_ids.*' => 'exists:items,id'
            ]);
            
            // Untuk upload file, kita perlu mengirim dengan pendekatan multipart
            $token = session('api_token');
            
            if ($request->hasFile('gambar')) {
                $response = Http::withToken($token)
                    ->timeout(30)
                    ->attach(
                        'gambar', 
                        file_get_contents($request->file('gambar')->getRealPath()),
                        $request->file('gambar')->getClientOriginalName()
                    )
                    ->post($this->apiBaseUrl . '/kategoris', $request->except('gambar'));
            } else {
                $response = Http::withToken($token)
                    ->withHeaders([
                        'Accept' => 'application/json'
                    ])
                    ->post($this->apiBaseUrl . '/kategoris', $request->all());
            }
            
            $responseData = $response->json();
            
            if ($responseData['success'] ?? false) {
                return redirect()->route('admin.product-manager', ['tab' => 'kategori'])
                    ->with('success', 'Kategori berhasil ditambahkan');
            }
            
            return redirect()->back()
                ->with('error', $responseData['message'] ?? 'Terjadi kesalahan saat menyimpan kategori')
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan kategori: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Memperbarui kategori
     */
    public function updateKategori(Request $request, $id)
    {
        try {
            // Validasi lokal terlebih dahulu
            $request->validate([
                'nama_kategori' => 'required|string|max:255',
                'deskripsi' => 'nullable|string',
                'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'item_ids' => 'nullable|array',
                'item_ids.*' => 'exists:items,id'
            ]);
            
            // Untuk upload file, kita perlu mengirim dengan pendekatan multipart
            $token = session('api_token');
            
            if ($request->hasFile('gambar')) {
                $response = Http::withToken($token)
                    ->timeout(30)
                    ->attach(
                        'gambar', 
                        file_get_contents($request->file('gambar')->getRealPath()),
                        $request->file('gambar')->getClientOriginalName()
                    )
                    ->post($this->apiBaseUrl . "/kategoris/{$id}?_method=PUT", $request->except('gambar', '_method'));
            } else {
                $response = Http::withToken($token)
                    ->withHeaders([
                        'Accept' => 'application/json'
                    ])
                    ->put($this->apiBaseUrl . "/kategoris/{$id}", $request->except('_method'));
            }
            
            $responseData = $response->json();
            
            if ($responseData['success'] ?? false) {
                return redirect()->route('admin.product-manager', ['tab' => 'kategori'])
                    ->with('success', 'Kategori berhasil diperbarui');
            }
            
            return redirect()->back()
                ->with('error', $responseData['message'] ?? 'Terjadi kesalahan saat memperbarui kategori')
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memperbarui kategori: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Menghapus kategori
     */
    public function destroyKategori($id)
    {
        try {
            $token = session('api_token');
            
            $response = Http::withToken($token)
                ->withHeaders([
                    'Accept' => 'application/json'
                ])
                ->delete($this->apiBaseUrl . "/kategoris/{$id}");
            
            $responseData = $response->json();
            
            if ($responseData['success'] ?? false) {
                return redirect()->route('admin.product-manager', ['tab' => 'kategori'])
                    ->with('success', 'Kategori berhasil dihapus');
            }
            
            return redirect()->back()
                ->with('error', $responseData['message'] ?? 'Terjadi kesalahan saat menghapus kategori');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menghapus kategori: ' . $e->getMessage());
        }
    }
    
    /**
     * Item Methods
     */
    
    /**
     * Store a newly created item
     */
    public function storeItem(Request $request)
    {
        // Validasi input terlebih dahulu
        $request->validate([
            'nama_item' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga_dasar' => 'required|numeric|min:0',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        $response = $this->sendApiRequest('post', '/items', $request, true);
        if ($response['success'] ?? false) {
            return redirect()->route('admin.product-manager', ['tab' => 'items'])
                ->with('success', 'Item berhasil ditambahkan');
        }
        return redirect()->back()
            ->with('error', $response['message'] ?? 'Terjadi kesalahan')
            ->withInput();
    }

    /**
     * Update the specified item
     */
    public function updateItem(Request $request, $id)
    {
        $request->validate([
            'nama_item' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga_dasar' => 'required|numeric|min:0',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        $response = $this->sendApiRequest('post', "/items/{$id}", $request, true);
        if ($response['success'] ?? false) {
            return redirect()->route('admin.product-manager', ['tab' => 'items'])
                ->with('success', 'Item berhasil diperbarui');
        }
        return redirect()->back()
            ->with('error', $response['message'] ?? 'Terjadi kesalahan')
            ->withInput();
    }

    /**
     * Remove the specified item
     */
    public function destroyItem($id)
    {
        try {
            $response = $this->sendApiRequest('delete', "/items/{$id}");
            if ($response['success'] ?? false) {
                return redirect()->route('admin.product-manager', ['tab' => 'items'])
                    ->with('success', 'Item berhasil dihapus');
            }
            return redirect()->back()
                ->with('error', $response['message'] ?? 'Terjadi kesalahan saat menghapus item');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan tak terduga: ' . $e->getMessage());
        }
    }

    /**
     * Bahan Methods
     */

    public function storeBahan(Request $request)
    {
        $response = $this->sendApiRequest('post', '/bahans', $request);
        if ($response['success'] ?? false) {
            return redirect()->route('admin.product-manager', ['tab' => 'bahan'])
                ->with('success', 'Bahan berhasil ditambahkan');
        }
        return redirect()->back()
            ->with('error', $response['message'] ?? 'Terjadi kesalahan')
            ->withInput();
    }

    public function updateBahan(Request $request, $id)
    {
        $response = $this->sendApiRequest('put', "/bahans/{$id}", $request);
        if ($response['success'] ?? false) {
            return redirect()->route('admin.product-manager', ['tab' => 'bahan'])
                ->with('success', 'Bahan berhasil diperbarui');
        }
        return redirect()->back()
            ->with('error', $response['message'] ?? 'Terjadi kesalahan')
            ->withInput();
    }

    /**
     * Remove the specified bahan
     */
    public function destroyBahan($id)
    {
        try {
            $response = $this->sendApiRequest('delete', "/bahans/{$id}");
            if ($response['success'] ?? false) {
                return redirect()->route('admin.product-manager', ['tab' => 'bahan'])
                    ->with('success', 'Bahan berhasil dihapus');
            }
            return redirect()->back()
                ->with('error', $response['message'] ?? 'Terjadi kesalahan saat menghapus bahan');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan tak terduga: ' . $e->getMessage());
        }
    }

    /**
     * Jenis Methods
     */
    
    public function storeJenis(Request $request)
    {
        $response = $this->sendApiRequest('post', '/jenis', $request);
        if ($response['success'] ?? false) {
            return redirect()->route('admin.product-manager', ['tab' => 'jenis'])
                ->with('success', 'Jenis berhasil ditambahkan');
        }
        return redirect()->back()
            ->with('error', $response['message'] ?? 'Terjadi kesalahan')
            ->withInput();
    }

    public function updateJenis(Request $request, $id)
    {
        $response = $this->sendApiRequest('put', "/jenis/{$id}", $request);
        if ($response['success'] ?? false) {
            return redirect()->route('admin.product-manager', ['tab' => 'jenis'])
                ->with('success', 'Jenis berhasil diperbarui');
        }
        return redirect()->back()
            ->with('error', $response['message'] ?? 'Terjadi kesalahan')
            ->withInput();
    }

    /**
     * Remove the specified jenis
     */
    public function destroyJenis($id)
    {
        try {
            $response = $this->sendApiRequest('delete', "/jenis/{$id}");
            if ($response['success'] ?? false) {
                return redirect()->route('admin.product-manager', ['tab' => 'jenis'])
                    ->with('success', 'Jenis berhasil dihapus');
            }
            return redirect()->back()
                ->with('error', $response['message'] ?? 'Terjadi kesalahan saat menghapus jenis');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan tak terduga: ' . $e->getMessage());
        }
    }

    /**
     * Ukuran Methods
     */
    
    public function storeUkuran(Request $request)
    {
        $response = $this->sendApiRequest('post', '/ukurans', $request);
        if ($response['success'] ?? false) {
            return redirect()->route('admin.product-manager', ['tab' => 'ukuran'])
                ->with('success', 'Ukuran berhasil ditambahkan');
        }
        return redirect()->back()
            ->with('error', $response['message'] ?? 'Terjadi kesalahan')
            ->withInput();
    }

    public function updateUkuran(Request $request, $id)
    {
        $response = $this->sendApiRequest('put', "/ukurans/{$id}", $request);
        if ($response['success'] ?? false) {
            return redirect()->route('admin.product-manager', ['tab' => 'ukuran'])
                ->with('success', 'Ukuran berhasil diperbarui');
        }
        return redirect()->back()
            ->with('error', $response['message'] ?? 'Terjadi kesalahan')
            ->withInput();
    }

    /**
     * Remove the specified ukuran
     */
    public function destroyUkuran($id)
    {
        try {
            $response = $this->sendApiRequest('delete', "/ukurans/{$id}");
            if ($response['success'] ?? false) {
                return redirect()->route('admin.product-manager', ['tab' => 'ukuran'])
                    ->with('success', 'Ukuran berhasil dihapus');
            }
            return redirect()->back()
                ->with('error', $response['message'] ?? 'Terjadi kesalahan saat menghapus ukuran');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan tak terduga: ' . $e->getMessage());
        }
    }

    /**
     * Biaya Desain Methods
     */
    
    public function storeBiayaDesain(Request $request)
    {
        $response = $this->sendApiRequest('post', '/biaya-desains', $request);
        if ($response['success'] ?? false) {
            return redirect()->route('admin.product-manager', ['tab' => 'biaya-desain'])
                ->with('success', 'Biaya desain berhasil ditambahkan');
        }
        return redirect()->back()
            ->with('error', $response['message'] ?? 'Terjadi kesalahan')
            ->withInput();
    }

    public function updateBiayaDesain(Request $request, $id)
    {
        $response = $this->sendApiRequest('put', "/biaya-desains/{$id}", $request);
        if ($response['success'] ?? false) {
            return redirect()->route('admin.product-manager', ['tab' => 'biaya-desain'])
                ->with('success', 'Biaya desain berhasil diperbarui');
        }
        return redirect()->back()
            ->with('error', $response['message'] ?? 'Terjadi kesalahan')
            ->withInput();
    }

    /**
     * Remove the specified biaya desain
     */
    public function destroyBiayaDesain($id)
    {
        try {
            $response = $this->sendApiRequest('delete', "/biaya-desains/{$id}");
            if ($response['success'] ?? false) {
                return redirect()->route('admin.product-manager', ['tab' => 'biaya-desain'])
                    ->with('success', 'Biaya desain berhasil dihapus');
            }
            return redirect()->back()
                ->with('error', $response['message'] ?? 'Terjadi kesalahan saat menghapus biaya desain');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan tak terduga: ' . $e->getMessage());
        }
    }
}
