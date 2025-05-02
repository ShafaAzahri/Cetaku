<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProductManagerController extends Controller
{
    // Cache TTL in seconds (15 minutes)
    protected $cacheTTL = 900;
    
    /**
     * Display product manager page with optimized data loading
     */
    public function index(Request $request)
    {
        // Only log important information
        Log::info('ProductManager accessed', [
            'user_id' => session('user.id') ?? 'unknown',
            'active_tab' => $request->get('tab', 'items')
        ]);
        
        // Check access before continuing
        if (!session()->has('api_token') || !session()->has('user')) {
            return redirect()->route('login')->with('error', 'Sesi Anda telah berakhir. Silakan login kembali.');
        }
        
        // Check user role
        $user = session('user');
        if (!isset($user['role']) || ($user['role'] !== 'admin' && $user['role'] !== 'super_admin')) {
            return redirect()->route('login')->with('error', 'Anda tidak memiliki akses ke halaman ini');
        }
        
        $activeTab = $request->get('tab', 'items');
        $token = session('api_token');
        $baseUrl = config('app.url');
        
        // Set basic data with items dropdown for modals
        $data = [
            'activeTab' => $activeTab,
            'itemsDropdown' => $this->getItemsDropdown($token, $baseUrl)
        ];
        
        // Only load data for the active tab to improve performance
        try {
            switch ($activeTab) {
                case 'items':
                    $data['items'] = $this->getItems($token, $baseUrl, $request);
                    break;
                case 'bahans':
                    $data['bahans'] = $this->getBahans($token, $baseUrl, $request);
                    break;
                case 'ukurans':
                    $data['ukurans'] = $this->getUkurans($token, $baseUrl, $request);
                    break;
                case 'jenis':
                    $data['jenis'] = $this->getJenis($token, $baseUrl, $request);
                    break;
                case 'biaya-desain':
                    $data['biayaDesain'] = $this->getBiayaDesain($token, $baseUrl, $request);
                    break;
            }
            
            return view('admin.product-manager', $data);
            
        } catch (\Exception $e) {
            Log::error('Error in ProductManagerController@index: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengambil data: ' . $e->getMessage());
        }
    }
    
    /**
     * Get items dropdown data (cached)
     */
    private function getItemsDropdown($token, $baseUrl)
    {
        try {
            return Cache::store('file')->remember('items_dropdown', $this->cacheTTL, function() use ($token, $baseUrl) {
                $response = Http::withToken($token)
                    ->get($baseUrl . '/api/admin/items/all');
                
                if ($response->successful()) {
                    return $response->json()['data'] ?? [];
                }
                
                Log::error('Error fetching items dropdown: ' . $response->body());
                return [];
            });
        } catch (\Exception $e) {
            // Fallback jika cache error
            Log::error('Cache error: ' . $e->getMessage());
            $response = Http::withToken($token)
                ->get($baseUrl . '/api/admin/items/all');
            
            if ($response->successful()) {
                return $response->json()['data'] ?? [];
            }
            
            return [];
        }
    }
    
    /**
     * Get items data (cached)
     */
    private function getItems($token, $baseUrl, $request)
    {
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search', '');
        $cacheKey = "items_p{$page}_pp{$perPage}_s" . md5($search);
        
        return Cache::remember($cacheKey, $this->cacheTTL, function() use ($token, $baseUrl, $page, $perPage, $search) {
            $queryParams = [
                'page' => $page,
                'per_page' => $perPage
            ];
            
            if (!empty($search)) {
                $queryParams['search'] = $search;
            }
            
            $response = Http::withToken($token)
                ->get($baseUrl . '/api/admin/items', $queryParams);
            
            if ($response->successful()) {
                $responseData = $response->json();
                return $responseData['data'] ?? [];
            }
            
            Log::error('Error fetching items: ' . $response->body());
            return [];
        });
    }
    
    /**
     * Get bahans data (cached)
     */
    private function getBahans($token, $baseUrl, $request)
    {
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search', '');
        $cacheKey = "bahans_p{$page}_pp{$perPage}_s" . md5($search);
        
        return Cache::remember($cacheKey, $this->cacheTTL, function() use ($token, $baseUrl, $page, $perPage, $search) {
            $queryParams = [
                'page' => $page,
                'per_page' => $perPage
            ];
            
            if (!empty($search)) {
                $queryParams['search'] = $search;
            }
            
            $response = Http::withToken($token)
                ->get($baseUrl . '/api/admin/bahans', $queryParams);
            
            if ($response->successful()) {
                $responseData = $response->json();
                return $responseData['data'] ?? [];
            }
            
            Log::error('Error fetching bahans: ' . $response->body());
            return [];
        });
    }
    
    /**
     * Get ukurans data (cached)
     */
    private function getUkurans($token, $baseUrl, $request)
    {
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search', '');
        $cacheKey = "ukurans_p{$page}_pp{$perPage}_s" . md5($search);
        
        return Cache::remember($cacheKey, $this->cacheTTL, function() use ($token, $baseUrl, $page, $perPage, $search) {
            $queryParams = [
                'page' => $page,
                'per_page' => $perPage
            ];
            
            if (!empty($search)) {
                $queryParams['search'] = $search;
            }
            
            $response = Http::withToken($token)
                ->get($baseUrl . '/api/admin/ukurans', $queryParams);
            
            if ($response->successful()) {
                $responseData = $response->json();
                return $responseData['data'] ?? [];
            }
            
            Log::error('Error fetching ukurans: ' . $response->body());
            return [];
        });
    }
    
    /**
     * Get jenis data (cached)
     */
    private function getJenis($token, $baseUrl, $request)
    {
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search', '');
        $cacheKey = "jenis_p{$page}_pp{$perPage}_s" . md5($search);
        
        return Cache::remember($cacheKey, $this->cacheTTL, function() use ($token, $baseUrl, $page, $perPage, $search) {
            $queryParams = [
                'page' => $page,
                'per_page' => $perPage
            ];
            
            if (!empty($search)) {
                $queryParams['search'] = $search;
            }
            
            $response = Http::withToken($token)
                ->get($baseUrl . '/api/admin/jenis', $queryParams);
            
            if ($response->successful()) {
                $responseData = $response->json();
                return $responseData['data'] ?? [];
            }
            
            Log::error('Error fetching jenis: ' . $response->body());
            return [];
        });
    }
    
    /**
     * Get biaya-desain data (cached)
     */
    private function getBiayaDesain($token, $baseUrl, $request)
    {
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search', '');
        $cacheKey = "biaya_desain_p{$page}_pp{$perPage}_s" . md5($search);
        
        return Cache::remember($cacheKey, $this->cacheTTL, function() use ($token, $baseUrl, $page, $perPage, $search) {
            $queryParams = [
                'page' => $page,
                'per_page' => $perPage
            ];
            
            if (!empty($search)) {
                $queryParams['search'] = $search;
            }
            
            $response = Http::withToken($token)
                ->get($baseUrl . '/api/admin/biaya-desain', $queryParams);
            
            if ($response->successful()) {
                $responseData = $response->json();
                return $responseData['data'] ?? [];
            }
            
            Log::error('Error fetching biaya-desain: ' . $response->body());
            return [];
        });
    }
    
    /**
     * Store a new item
     */
    public function storeItem(Request $request)
    {
        $request->validate([
            'nama_item' => 'required|string|max:255',
            'harga_dasar' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        try {
            $token = session('api_token');
            
            if (!$token) {
                return redirect()->route('login')->with('error', 'Session expired. Please login again.');
            }
            
            // Prepare form data
            $formData = [
                'nama_item' => $request->nama_item,
                'harga_dasar' => $request->harga_dasar,
                'deskripsi' => $request->deskripsi,
            ];
            
            // Handle file upload if provided
            if ($request->hasFile('gambar')) {
                $client = Http::withToken($token)->asMultipart();
                $image = $request->file('gambar');
                
                $response = $client->attach(
                    'gambar', 
                    file_get_contents($image->getRealPath()),
                    $image->getClientOriginalName()
                )->post(config('app.url') . '/api/admin/items', $formData);
            } else {
                $response = Http::withToken($token)->post(config('app.url') . '/api/admin/items', $formData);
            }
            
            if ($response->successful()) {
                // Clear relevant caches
                $this->clearItemsCache();
                
                return redirect()->route('admin.product-manager', ['tab' => 'items'])
                    ->with('success', 'Produk berhasil ditambahkan');
            } else {
                Log::error('API Error storeItem: ' . $response->body());
                return redirect()->back()
                    ->with('error', 'Gagal menambahkan produk: ' . $response->json()['message'] ?? 'Unknown error')
                    ->withInput();
            }
            
        } catch (\Exception $e) {
            Log::error('Error in storeItem: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan produk: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Update an existing item
     */
    public function updateItem(Request $request, $id)
    {
        $request->validate([
            'nama_item' => 'required|string|max:255',
            'harga_dasar' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        try {
            $token = session('api_token');
            
            if (!$token) {
                return redirect()->route('login')->with('error', 'Session expired. Please login again.');
            }
            
            // Prepare form data
            $formData = [
                'nama_item' => $request->nama_item,
                'harga_dasar' => $request->harga_dasar,
                'deskripsi' => $request->deskripsi,
            ];
            
            // Handle file upload if provided
            if ($request->hasFile('gambar')) {
                $client = Http::withToken($token)->asMultipart();
                $image = $request->file('gambar');
                
                $response = $client->attach(
                    'gambar', 
                    file_get_contents($image->getRealPath()),
                    $image->getClientOriginalName()
                )->put(config('app.url') . '/api/admin/items/' . $id, $formData);
            } else {
                $response = Http::withToken($token)->put(config('app.url') . '/api/admin/items/' . $id, $formData);
            }
            
            if ($response->successful()) {
                // Clear relevant caches
                $this->clearItemsCache();
                
                return redirect()->route('admin.product-manager', ['tab' => 'items'])
                    ->with('success', 'Produk berhasil diperbarui');
            } else {
                Log::error('API Error updateItem: ' . $response->body());
                return redirect()->back()
                    ->with('error', 'Gagal memperbarui produk: ' . $response->json()['message'] ?? 'Unknown error')
                    ->withInput();
            }
            
        } catch (\Exception $e) {
            Log::error('Error in updateItem: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memperbarui produk: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Delete an item
     */
    public function destroyItem($id)
    {
        try {
            $token = session('api_token');
            
            if (!$token) {
                return redirect()->route('login')->with('error', 'Session expired. Please login again.');
            }
            
            $response = Http::withToken($token)->delete(config('app.url') . '/api/admin/items/' . $id);
            
            if ($response->successful()) {
                // Clear relevant caches
                $this->clearItemsCache();
                
                return redirect()->route('admin.product-manager', ['tab' => 'items'])
                    ->with('success', 'Produk berhasil dihapus');
            } else {
                Log::error('API Error destroyItem: ' . $response->body());
                return redirect()->back()
                    ->with('error', 'Gagal menghapus produk: ' . $response->json()['message'] ?? 'Unknown error');
            }
            
        } catch (\Exception $e) {
            Log::error('Error in destroyItem: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menghapus produk: ' . $e->getMessage());
        }
    }
    
    /**
     * Store a new bahan
     */
    public function storeBahan(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'nama_bahan' => 'required|string|max:255',
            'biaya_tambahan' => 'required|numeric|min:0',
        ]);
        
        try {
            $token = session('api_token');
            
            if (!$token) {
                return redirect()->route('login')->with('error', 'Session expired. Please login again.');
            }
            
            $response = Http::withToken($token)
                ->post(config('app.url') . '/api/admin/bahans', [
                    'item_id' => $request->item_id,
                    'nama_bahan' => $request->nama_bahan,
                    'biaya_tambahan' => $request->biaya_tambahan,
                    'is_available' => $request->has('is_available') ? 1 : 0,
                ]);
            
            if ($response->successful()) {
                // Clear relevant caches
                $this->clearBahansCache();
                
                return redirect()->route('admin.product-manager', ['tab' => 'bahans'])
                    ->with('success', 'Bahan berhasil ditambahkan');
            } else {
                Log::error('API Error storeBahan: ' . $response->body());
                return redirect()->back()
                    ->with('error', 'Gagal menambahkan bahan: ' . $response->json()['message'] ?? 'Unknown error')
                    ->withInput();
            }
            
        } catch (\Exception $e) {
            Log::error('Error in storeBahan: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan bahan: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Update an existing bahan
     */
    public function updateBahan(Request $request, $id)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'nama_bahan' => 'required|string|max:255',
            'biaya_tambahan' => 'required|numeric|min:0',
        ]);
        
        try {
            $token = session('api_token');
            
            if (!$token) {
                return redirect()->route('login')->with('error', 'Session expired. Please login again.');
            }
            
            $response = Http::withToken($token)
                ->put(config('app.url') . '/api/admin/bahans/' . $id, [
                    'item_id' => $request->item_id,
                    'nama_bahan' => $request->nama_bahan,
                    'biaya_tambahan' => $request->biaya_tambahan,
                    'is_available' => $request->has('is_available') ? 1 : 0,
                ]);
            
            if ($response->successful()) {
                // Clear relevant caches
                $this->clearBahansCache();
                
                return redirect()->route('admin.product-manager', ['tab' => 'bahans'])
                    ->with('success', 'Bahan berhasil diperbarui');
            } else {
                Log::error('API Error updateBahan: ' . $response->body());
                return redirect()->back()
                    ->with('error', 'Gagal memperbarui bahan: ' . $response->json()['message'] ?? 'Unknown error')
                    ->withInput();
            }
            
        } catch (\Exception $e) {
            Log::error('Error in updateBahan: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memperbarui bahan: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Delete a bahan
     */
    public function destroyBahan($id)
    {
        try {
            $token = session('api_token');
            
            if (!$token) {
                return redirect()->route('login')->with('error', 'Session expired. Please login again.');
            }
            
            $response = Http::withToken($token)->delete(config('app.url') . '/api/admin/bahans/' . $id);
            
            if ($response->successful()) {
                // Clear relevant caches
                $this->clearBahansCache();
                
                return redirect()->route('admin.product-manager', ['tab' => 'bahans'])
                    ->with('success', 'Bahan berhasil dihapus');
            } else {
                Log::error('API Error destroyBahan: ' . $response->body());
                return redirect()->back()
                    ->with('error', 'Gagal menghapus bahan: ' . $response->json()['message'] ?? 'Unknown error');
            }
            
        } catch (\Exception $e) {
            Log::error('Error in destroyBahan: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menghapus bahan: ' . $e->getMessage());
        }
    }
    
    /**
     * Store a new ukuran
     */
    public function storeUkuran(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'size' => 'required|string|max:100',
            'faktor_harga' => 'required|numeric|min:0.1',
        ]);
        
        try {
            $token = session('api_token');
            
            if (!$token) {
                return redirect()->route('login')->with('error', 'Session expired. Please login again.');
            }
            
            $response = Http::withToken($token)
                ->post(config('app.url') . '/api/admin/ukurans', [
                    'item_id' => $request->item_id,
                    'size' => $request->size,
                    'faktor_harga' => $request->faktor_harga,
                ]);
            
            if ($response->successful()) {
                // Clear relevant caches
                $this->clearUkuransCache();
                
                return redirect()->route('admin.product-manager', ['tab' => 'ukurans'])
                    ->with('success', 'Ukuran berhasil ditambahkan');
            } else {
                Log::error('API Error storeUkuran: ' . $response->body());
                return redirect()->back()
                    ->with('error', 'Gagal menambahkan ukuran: ' . $response->json()['message'] ?? 'Unknown error')
                    ->withInput();
            }
            
        } catch (\Exception $e) {
            Log::error('Error in storeUkuran: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan ukuran: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Update an existing ukuran
     */
    public function updateUkuran(Request $request, $id)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'size' => 'required|string|max:100',
            'faktor_harga' => 'required|numeric|min:0.1',
        ]);
        
        try {
            $token = session('api_token');
            
            if (!$token) {
                return redirect()->route('login')->with('error', 'Session expired. Please login again.');
            }
            
            $response = Http::withToken($token)
                ->put(config('app.url') . '/api/admin/ukurans/' . $id, [
                    'item_id' => $request->item_id,
                    'size' => $request->size,
                    'faktor_harga' => $request->faktor_harga,
                ]);
            
            if ($response->successful()) {
                // Clear relevant caches
                $this->clearUkuransCache();
                
                return redirect()->route('admin.product-manager', ['tab' => 'ukurans'])
                    ->with('success', 'Ukuran berhasil diperbarui');
            } else {
                Log::error('API Error updateUkuran: ' . $response->body());
                return redirect()->back()
                    ->with('error', 'Gagal memperbarui ukuran: ' . $response->json()['message'] ?? 'Unknown error')
                    ->withInput();
            }
            
        } catch (\Exception $e) {
            Log::error('Error in updateUkuran: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memperbarui ukuran: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Delete a ukuran
     */
    public function destroyUkuran($id)
    {
        try {
            $token = session('api_token');
            
            if (!$token) {
                return redirect()->route('login')->with('error', 'Session expired. Please login again.');
            }
            
            $response = Http::withToken($token)->delete(config('app.url') . '/api/admin/ukurans/' . $id);
            
            if ($response->successful()) {
                // Clear relevant caches
                $this->clearUkuransCache();
                
                return redirect()->route('admin.product-manager', ['tab' => 'ukurans'])
                    ->with('success', 'Ukuran berhasil dihapus');
            } else {
                Log::error('API Error destroyUkuran: ' . $response->body());
                return redirect()->back()
                    ->with('error', 'Gagal menghapus ukuran: ' . $response->json()['message'] ?? 'Unknown error');
            }
            
        } catch (\Exception $e) {
            Log::error('Error in destroyUkuran: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menghapus ukuran: ' . $e->getMessage());
        }
    }
    
    /**
     * Store a new jenis
     */
    public function storeJenis(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'kategori' => 'required|string|max:255',
            'biaya_tambahan' => 'required|numeric|min:0',
        ]);
        
        try {
            $token = session('api_token');
            
            if (!$token) {
                return redirect()->route('login')->with('error', 'Session expired. Please login again.');
            }
            
            $response = Http::withToken($token)
                ->post(config('app.url') . '/api/admin/jenis', [
                    'item_id' => $request->item_id,
                    'kategori' => $request->kategori,
                    'biaya_tambahan' => $request->biaya_tambahan,
                ]);
            
            if ($response->successful()) {
                // Clear relevant caches
                $this->clearJenisCache();
                
                return redirect()->route('admin.product-manager', ['tab' => 'jenis'])
                    ->with('success', 'Jenis berhasil ditambahkan');
            } else {
                Log::error('API Error storeJenis: ' . $response->body());
                return redirect()->back()
                    ->with('error', 'Gagal menambahkan jenis: ' . $response->json()['message'] ?? 'Unknown error')
                    ->withInput();
            }
            
        } catch (\Exception $e) {
            Log::error('Error in storeJenis: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan jenis: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Update an existing jenis
     */
    public function updateJenis(Request $request, $id)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'kategori' => 'required|string|max:255',
            'biaya_tambahan' => 'required|numeric|min:0',
        ]);
        
        try {
            $token = session('api_token');
            
            if (!$token) {
                return redirect()->route('login')->with('error', 'Session expired. Please login again.');
            }
            
            $response = Http::withToken($token)
                ->put(config('app.url') . '/api/admin/jenis/' . $id, [
                    'item_id' => $request->item_id,
                    'kategori' => $request->kategori,
                    'biaya_tambahan' => $request->biaya_tambahan,
                ]);
            
            if ($response->successful()) {
                // Clear relevant caches
                $this->clearJenisCache();
                
                return redirect()->route('admin.product-manager', ['tab' => 'jenis'])
                    ->with('success', 'Jenis berhasil diperbarui');
            } else {
                Log::error('API Error updateJenis: ' . $response->body());
                return redirect()->back()
                    ->with('error', 'Gagal memperbarui jenis: ' . $response->json()['message'] ?? 'Unknown error')
                    ->withInput();
            }
            
        } catch (\Exception $e) {
            Log::error('Error in updateJenis: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memperbarui jenis: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Delete a jenis
     */
    public function destroyJenis($id)
    {
        try {
            $token = session('api_token');
            
            if (!$token) {
                return redirect()->route('login')->with('error', 'Session expired. Please login again.');
            }
            
            $response = Http::withToken($token)->delete(config('app.url') . '/api/admin/jenis/' . $id);
            
            if ($response->successful()) {
                // Clear relevant caches
                $this->clearJenisCache();
                
                return redirect()->route('admin.product-manager', ['tab' => 'jenis'])
                    ->with('success', 'Jenis berhasil dihapus');
            } else {
                Log::error('API Error destroyJenis: ' . $response->body());
                return redirect()->back()
                    ->with('error', 'Gagal menghapus jenis: ' . $response->json()['message'] ?? 'Unknown error');
            }
            
        } catch (\Exception $e) {
            Log::error('Error in destroyJenis: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menghapus jenis: ' . $e->getMessage());
        }
    }
    
    /**
     * Store a new biaya desain
     */
    public function storeBiayaDesain(Request $request)
    {
        $request->validate([
            'biaya' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
        ]);
        
        try {
            $token = session('api_token');
            
            if (!$token) {
                return redirect()->route('login')->with('error', 'Session expired. Please login again.');
            }
            
            $response = Http::withToken($token)
                ->post(config('app.url') . '/api/admin/biaya-desain', [
                    'biaya' => $request->biaya,
                    'deskripsi' => $request->deskripsi,
                ]);
            
            if ($response->successful()) {
                // Clear relevant caches
                $this->clearBiayaDesainCache();
                
                return redirect()->route('admin.product-manager', ['tab' => 'biaya-desain'])
                    ->with('success', 'Biaya Desain berhasil ditambahkan');
            } else {
                Log::error('API Error storeBiayaDesain: ' . $response->body());
                return redirect()->back()
                    ->with('error', 'Gagal menambahkan biaya desain: ' . $response->json()['message'] ?? 'Unknown error')
                    ->withInput();
            }
            
        } catch (\Exception $e) {
            Log::error('Error in storeBiayaDesain: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan biaya desain: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Update an existing biaya desain
     */
    public function updateBiayaDesain(Request $request, $id)
    {
        $request->validate([
            'biaya' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
        ]);
        
        try {
            $token = session('api_token');
            
            if (!$token) {
                return redirect()->route('login')->with('error', 'Session expired. Please login again.');
            }
            
            $response = Http::withToken($token)
                ->put(config('app.url') . '/api/admin/biaya-desain/' . $id, [
                    'biaya' => $request->biaya,
                    'deskripsi' => $request->deskripsi,
                ]);
            
            if ($response->successful()) {
                // Clear relevant caches
                $this->clearBiayaDesainCache();
                
                return redirect()->route('admin.product-manager', ['tab' => 'biaya-desain'])
                    ->with('success', 'Biaya Desain berhasil diperbarui');
            } else {
                Log::error('API Error updateBiayaDesain: ' . $response->body());
                return redirect()->back()
                    ->with('error', 'Gagal memperbarui biaya desain: ' . $response->json()['message'] ?? 'Unknown error')
                    ->withInput();
            }
            
        } catch (\Exception $e) {
            Log::error('Error in updateBiayaDesain: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memperbarui biaya desain: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Delete a biaya desain
     */
    public function destroyBiayaDesain($id)
    {
        try {
            $token = session('api_token');
            
            if (!$token) {
                return redirect()->route('login')->with('error', 'Session expired. Please login again.');
            }
            
            $response = Http::withToken($token)->delete(config('app.url') . '/api/admin/biaya-desain/' . $id);
            
            if ($response->successful()) {
                // Clear relevant caches
                $this->clearBiayaDesainCache();
                
                return redirect()->route('admin.product-manager', ['tab' => 'biaya-desain'])
                    ->with('success', 'Biaya Desain berhasil dihapus');
            } else {
                Log::error('API Error destroyBiayaDesain: ' . $response->body());
                return redirect()->back()
                    ->with('error', 'Gagal menghapus biaya desain: ' . $response->json()['message'] ?? 'Unknown error');
            }
            
        } catch (\Exception $e) {
            Log::error('Error in destroyBiayaDesain: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menghapus biaya desain: ' . $e->getMessage());
        }
    }
    
    /**
     * Show edit form for biaya desain
     */
    public function editBiayaDesain($id)
    {
        try {
            $token = session('api_token');
            
            if (!$token) {
                return redirect()->route('login')->with('error', 'Session expired. Please login again.');
            }
            
            $response = Http::withToken($token)->get(config('app.url') . '/api/admin/biaya-desain/' . $id);
            
            if ($response->successful()) {
                $biayaDesain = $response->json()['data'];
                return view('admin.product-manager.edit-biaya-desain', compact('biayaDesain'));
            } else {
                Log::error('API Error editBiayaDesain: ' . $response->body());
                return redirect()->back()
                    ->with('error', 'Gagal mendapatkan data biaya desain: ' . $response->json()['message'] ?? 'Unknown error');
            }
            
        } catch (\Exception $e) {
            Log::error('Error in editBiayaDesain: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mengambil data biaya desain: ' . $e->getMessage());
        }
    }
    
    /**
     * Clear items cache
     */
    private function clearItemsCache()
    {
        $this->forgetCacheKeysWithPrefix('items_');
        Cache::forget('items_dropdown');
    }
    
    /**
     * Clear bahans cache
     */
    private function clearBahansCache()
    {
        $this->forgetCacheKeysWithPrefix('bahans_');
    }
    
    /**
     * Clear ukurans cache
     */
    private function clearUkuransCache()
    {
        $this->forgetCacheKeysWithPrefix('ukurans_');
    }
    
    /**
     * Clear jenis cache
     */
    private function clearJenisCache()
    {
        $this->forgetCacheKeysWithPrefix('jenis_');
    }
    
    /**
     * Clear biaya desain cache
     */
    private function clearBiayaDesainCache()
    {
        $this->forgetCacheKeysWithPrefix('biaya_desain_');
    }
    
    /**
     * Helper method to clear caches by prefix
     */
    private function forgetCacheKeysWithPrefix($prefix)
    {
        // Simple implementation for file or array cache drivers
        if (Cache::getStore() instanceof \Illuminate\Cache\FileStore) {
            $cachePath = storage_path('framework/cache/data');
            $pattern = $cachePath . '/*' . $prefix . '*';
            
            foreach (glob($pattern) as $key) {
                @unlink($key);
            }
        } else {
            // Fallback for other cache drivers - less efficient
            Cache::flush();
        }
    }
}