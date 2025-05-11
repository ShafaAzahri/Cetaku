<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
        
        return view('admin.product-manager', $data);
    }
    
    private function fetchData($endpoint)
    {
        try {
            $token = session('api_token');
            
            Log::debug('ProductManager: Fetching data from API', [
                'endpoint' => $endpoint, 
                'url' => $this->apiBaseUrl . $endpoint,
                'has_token' => !empty($token)
            ]);
            
            $response = Http::withToken($token)->get($this->apiBaseUrl . $endpoint);
            
            if ($response->successful()) {
                $data = $response->json();
                
                // Log full response untuk debugging
                Log::debug('ProductManager: API full response', [
                    'endpoint' => $endpoint,
                    'response' => $data
                ]);
                
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
                    
                    // Jika masih tidak ditemukan, log key yang tersedia
                    Log::warning('ProductManager: Biaya desain key not found in response', [
                        'available_keys' => array_keys($data)
                    ]);
                    
                    return [];
                }
                
                // Default handling untuk endpoint lain
                $key = basename($endpoint);
                return $data[$key] ?? [];
            } else {
                Log::warning('ProductManager: API response not successful', [
                    'endpoint' => $endpoint,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
            }
            
            return [];
        } catch (\Exception $e) {
            Log::error('ProductManager: Error fetching data from API', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
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
            Log::debug('ProductManager: Sending ' . strtoupper($method) . ' request to API', [
                'endpoint' => $endpoint,
                'url' => $this->apiBaseUrl . $endpoint,
                'has_file' => $hasFile,
                'has_token' => !empty($token)
            ]);
            
            if ($hasFile && $request && $request->hasFile('gambar')) {
                // For file uploads, we need to use a multipart form request
                $response = Http::withToken($token)
                    ->timeout(30) // Increase timeout for file uploads
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
            
            Log::debug('ProductManager: API response', [
                'endpoint' => $endpoint,
                'status' => $response->status(),
                'success' => $responseData['success'] ?? false,
                'message' => $responseData['message'] ?? 'No message'
            ]);
            
            return $responseData;
        } catch (\Exception $e) {
            Log::error('ProductManager: Error sending request to API', [
                'endpoint' => $endpoint,
                'method' => $method,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghubungi server: ' . $e->getMessage()
            ];
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
        
        if ($request->hasFile('gambar')) {
            Log::debug('ProductManager: File information', [
                'original_name' => $request->file('gambar')->getClientOriginalName(),
                'mime_type' => $request->file('gambar')->getMimeType(),
                'size' => $request->file('gambar')->getSize(),
                'error' => $request->file('gambar')->getError()
            ]);
        }
        
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
        // Validasi input terlebih dahulu
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
            Log::info('ProductManager: Request untuk menghapus item diterima', ['id' => $id]);
            
            $response = $this->sendApiRequest('delete', "/items/{$id}");
            
            if ($response['success'] ?? false) {
                Log::info('ProductManager: Item berhasil dihapus', ['id' => $id]);
                return redirect()->route('admin.product-manager', ['tab' => 'items'])
                    ->with('success', 'Item berhasil dihapus');
            }
            
            Log::warning('ProductManager: Gagal menghapus item', [
                'id' => $id, 
                'message' => $response['message'] ?? 'Tidak ada pesan error'
            ]);
            
            return redirect()->back()
                ->with('error', $response['message'] ?? 'Terjadi kesalahan saat menghapus item');
        } catch (\Exception $e) {
            Log::error('ProductManager: Exception pada destroyItem', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            
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
            Log::info('ProductManager: Request untuk menghapus bahan diterima', ['id' => $id]);
            
            $response = $this->sendApiRequest('delete', "/bahans/{$id}");
            
            if ($response['success'] ?? false) {
                Log::info('ProductManager: Bahan berhasil dihapus', ['id' => $id]);
                return redirect()->route('admin.product-manager', ['tab' => 'bahan'])
                    ->with('success', 'Bahan berhasil dihapus');
            }
            
            Log::warning('ProductManager: Gagal menghapus bahan', [
                'id' => $id, 
                'message' => $response['message'] ?? 'Tidak ada pesan error'
            ]);
            
            return redirect()->back()
                ->with('error', $response['message'] ?? 'Terjadi kesalahan saat menghapus bahan');
        } catch (\Exception $e) {
            Log::error('ProductManager: Exception pada destroyBahan', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            
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
            Log::info('ProductManager: Request untuk menghapus jenis diterima', ['id' => $id]);
            
            $response = $this->sendApiRequest('delete', "/jenis/{$id}");
            
            if ($response['success'] ?? false) {
                Log::info('ProductManager: Jenis berhasil dihapus', ['id' => $id]);
                return redirect()->route('admin.product-manager', ['tab' => 'jenis'])
                    ->with('success', 'Jenis berhasil dihapus');
            }
            
            Log::warning('ProductManager: Gagal menghapus jenis', [
                'id' => $id, 
                'message' => $response['message'] ?? 'Tidak ada pesan error'
            ]);
            
            return redirect()->back()
                ->with('error', $response['message'] ?? 'Terjadi kesalahan saat menghapus jenis');
        } catch (\Exception $e) {
            Log::error('ProductManager: Exception pada destroyJenis', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            
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
            Log::info('ProductManager: Request untuk menghapus ukuran diterima', ['id' => $id]);
            
            $response = $this->sendApiRequest('delete', "/ukurans/{$id}");
            
            if ($response['success'] ?? false) {
                Log::info('ProductManager: Ukuran berhasil dihapus', ['id' => $id]);
                return redirect()->route('admin.product-manager', ['tab' => 'ukuran'])
                    ->with('success', 'Ukuran berhasil dihapus');
            }
            
            Log::warning('ProductManager: Gagal menghapus ukuran', [
                'id' => $id, 
                'message' => $response['message'] ?? 'Tidak ada pesan error'
            ]);
            
            return redirect()->back()
                ->with('error', $response['message'] ?? 'Terjadi kesalahan saat menghapus ukuran');
        } catch (\Exception $e) {
            Log::error('ProductManager: Exception pada destroyUkuran', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            
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
            Log::info('ProductManager: Request untuk menghapus biaya desain diterima', ['id' => $id]);
            
            $response = $this->sendApiRequest('delete', "/biaya-desains/{$id}");
            
            if ($response['success'] ?? false) {
                Log::info('ProductManager: Biaya desain berhasil dihapus', ['id' => $id]);
                return redirect()->route('admin.product-manager', ['tab' => 'biaya-desain'])
                    ->with('success', 'Biaya desain berhasil dihapus');
            }
            
            Log::warning('ProductManager: Gagal menghapus biaya desain', [
                'id' => $id, 
                'message' => $response['message'] ?? 'Tidak ada pesan error'
            ]);
            
            return redirect()->back()
                ->with('error', $response['message'] ?? 'Terjadi kesalahan saat menghapus biaya desain');
        } catch (\Exception $e) {
            Log::error('ProductManager: Exception pada destroyBiayaDesain', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan tak terduga: ' . $e->getMessage());
        }
    }
}