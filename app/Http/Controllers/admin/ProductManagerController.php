<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductManagerController extends Controller
{
    /**
     * Display product manager page
     */
    public function index(Request $request)
    {
        // Add debug logging
        Log::info('ProductManager accessed', [
            'user' => session('user'),
            'has_token' => session()->has('api_token'),
            'active_tab' => $request->get('tab', 'items')
        ]);
        
        $activeTab = $request->get('tab', 'items');
        
        try {
            $token = session('api_token');
            
            if (!$token) {
                Log::error('ProductManager: No API token in session');
                return redirect()->route('login')->with('error', 'Session expired. Please login again.');
            }
            
            // Initialize array to store data
            $data = [
                'activeTab' => $activeTab
            ];
            
            // Fetch data based on active tab
            switch ($activeTab) {
                case 'items':
                    $itemsResponse = Http::withToken($token)->get(config('app.url') . '/api/admin/items');
                    if ($itemsResponse->successful()) {
                        $data['items'] = $itemsResponse->json()['data'] ?? [];
                    } else {
                        Log::error('Error fetching items: ' . $itemsResponse->body());
                        $data['items'] = [];
                    }
                    break;
                    
                case 'bahans':
                    $bahansResponse = Http::withToken($token)->get(config('app.url') . '/api/admin/bahans');
                    if ($bahansResponse->successful()) {
                        $data['bahans'] = $bahansResponse->json()['data'] ?? [];
                    } else {
                        Log::error('Error fetching bahans: ' . $bahansResponse->body());
                        $data['bahans'] = [];
                    }
                    
                    // Also get items for dropdown
                    $itemsDropdownResponse = Http::withToken($token)->get(config('app.url') . '/api/admin/items/all');
                    if ($itemsDropdownResponse->successful()) {
                        $data['itemsDropdown'] = $itemsDropdownResponse->json()['data'] ?? [];
                    } else {
                        Log::error('Error fetching items dropdown: ' . $itemsDropdownResponse->body());
                        $data['itemsDropdown'] = [];
                    }
                    break;
                    
                case 'ukurans':
                    $ukuransResponse = Http::withToken($token)->get(config('app.url') . '/api/admin/ukurans');
                    if ($ukuransResponse->successful()) {
                        $data['ukurans'] = $ukuransResponse->json()['data'] ?? [];
                    } else {
                        Log::error('Error fetching ukurans: ' . $ukuransResponse->body());
                        $data['ukurans'] = [];
                    }
                    
                    // Also get items for dropdown
                    $itemsDropdownResponse = Http::withToken($token)->get(config('app.url') . '/api/admin/items/all');
                    if ($itemsDropdownResponse->successful()) {
                        $data['itemsDropdown'] = $itemsDropdownResponse->json()['data'] ?? [];
                    } else {
                        Log::error('Error fetching items dropdown: ' . $itemsDropdownResponse->body());
                        $data['itemsDropdown'] = [];
                    }
                    break;
                    
                case 'jenis':
                    $jenisResponse = Http::withToken($token)->get(config('app.url') . '/api/admin/jenis');
                    if ($jenisResponse->successful()) {
                        $data['jenis'] = $jenisResponse->json()['data'] ?? [];
                    } else {
                        Log::error('Error fetching jenis: ' . $jenisResponse->body());
                        $data['jenis'] = [];
                    }
                    
                    // Also get items for dropdown
                    $itemsDropdownResponse = Http::withToken($token)->get(config('app.url') . '/api/admin/items/all');
                    if ($itemsDropdownResponse->successful()) {
                        $data['itemsDropdown'] = $itemsDropdownResponse->json()['data'] ?? [];
                    } else {
                        Log::error('Error fetching items dropdown: ' . $itemsDropdownResponse->body());
                        $data['itemsDropdown'] = [];
                    }
                    break;
                    
                case 'biaya-desain':
                    $biayaDesainResponse = Http::withToken($token)->get(config('app.url') . '/api/admin/biaya-desain');
                    if ($biayaDesainResponse->successful()) {
                        $data['biayaDesain'] = $biayaDesainResponse->json()['data'] ?? [];
                    } else {
                        Log::error('Error fetching biaya desain: ' . $biayaDesainResponse->body());
                        $data['biayaDesain'] = [];
                    }
                    break;
            }
            
            Log::info('ProductManager rendering view with data', [
                'active_tab' => $activeTab,
                'has_data' => !empty($data)
            ]);
            
            return view('admin.product-manager', $data);
            
        } catch (\Exception $e) {
            Log::error('Error in ProductManagerController@index: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengambil data: ' . $e->getMessage());
        }
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
     * Show edit form for item
     */
    public function editItem($id)
    {
        try {
            $token = session('api_token');
            
            if (!$token) {
                return redirect()->route('login')->with('error', 'Session expired. Please login again.');
            }
            
            $response = Http::withToken($token)->get(config('app.url') . '/api/admin/items/' . $id);
            
            if ($response->successful()) {
                $item = $response->json()['data'];
                return view('admin.product-manager.edit-item', compact('item'));
            } else {
                Log::error('API Error editItem: ' . $response->body());
                return redirect()->back()
                    ->with('error', 'Gagal mendapatkan data produk: ' . $response->json()['message'] ?? 'Unknown error');
            }
            
        } catch (\Exception $e) {
            Log::error('Error in editItem: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mengambil data produk: ' . $e->getMessage());
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
     * Show edit form for bahan
     */
    public function editBahan($id)
    {
        try {
            $token = session('api_token');
            
            if (!$token) {
                return redirect()->route('login')->with('error', 'Session expired. Please login again.');
            }
            
            // Get bahan data
            $bahanResponse = Http::withToken($token)->get(config('app.url') . '/api/admin/bahans/' . $id);
            
            // Get items for dropdown
            $itemsResponse = Http::withToken($token)->get(config('app.url') . '/api/admin/items/all');
            
            if ($bahanResponse->successful() && $itemsResponse->successful()) {
                $bahan = $bahanResponse->json()['data'];
                $items = $itemsResponse->json()['data'];
                return view('admin.product-manager.edit-bahan', compact('bahan', 'items'));
            } else {
                Log::error('API Error editBahan: ' . $bahanResponse->body());
                return redirect()->back()
                    ->with('error', 'Gagal mendapatkan data bahan: ' . $bahanResponse->json()['message'] ?? 'Unknown error');
            }
            
        } catch (\Exception $e) {
            Log::error('Error in editBahan: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mengambil data bahan: ' . $e->getMessage());
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
     * Show edit form for item
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
     * Show edit form for ukuran
     */
    public function editUkuran($id)
    {
        try {
            $token = session('api_token');
            
            if (!$token) {
                return redirect()->route('login')->with('error', 'Session expired. Please login again.');
            }
            
            // Get ukuran data
            $ukuranResponse = Http::withToken($token)->get(config('app.url') . '/api/admin/ukurans/' . $id);
            
            // Get items for dropdown
            $itemsResponse = Http::withToken($token)->get(config('app.url') . '/api/admin/items/all');
            
            if ($ukuranResponse->successful() && $itemsResponse->successful()) {
                $ukuran = $ukuranResponse->json()['data'];
                $items = $itemsResponse->json()['data'];
                return view('admin.product-manager.edit-ukuran', compact('ukuran', 'items'));
            } else {
                Log::error('API Error editUkuran: ' . $ukuranResponse->body());
                return redirect()->back()
                    ->with('error', 'Gagal mendapatkan data ukuran: ' . $ukuranResponse->json()['message'] ?? 'Unknown error');
            }
            
        } catch (\Exception $e) {
            Log::error('Error in editUkuran: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mengambil data ukuran: ' . $e->getMessage());
        }
    }
    
    /**
     * Show edit form for jenis
     */
    public function editJenis($id)
    {
        try {
            $token = session('api_token');
            
            if (!$token) {
                return redirect()->route('login')->with('error', 'Session expired. Please login again.');
            }
            
            // Get jenis data
            $jenisResponse = Http::withToken($token)->get(config('app.url') . '/api/admin/jenis/' . $id);
            
            // Get items for dropdown
            $itemsResponse = Http::withToken($token)->get(config('app.url') . '/api/admin/items/all');
            
            if ($jenisResponse->successful() && $itemsResponse->successful()) {
                $jenis = $jenisResponse->json()['data'];
                $items = $itemsResponse->json()['data'];
                return view('admin.product-manager.edit-jenis', compact('jenis', 'items'));
            } else {
                Log::error('API Error editJenis: ' . $jenisResponse->body());
                return redirect()->back()
                    ->with('error', 'Gagal mendapatkan data jenis: ' . $jenisResponse->json()['message'] ?? 'Unknown error');
            }
            
        } catch (\Exception $e) {
            Log::error('Error in editJenis: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mengambil data jenis: ' . $e->getMessage());
        }
    }
}