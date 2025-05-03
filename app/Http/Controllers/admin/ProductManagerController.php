<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Admin\ItemViewController;

class ProductManagerController extends Controller
{
    protected $itemViewController;
    
    /**
     * Constructor
     */
    public function __construct(ItemViewController $itemViewController)
    {
        $this->itemViewController = $itemViewController;
    }
    
    /**
     * Display product manager page dengan fokus pada Item
     */
    public function index(Request $request)
    {
        // Log info
        Log::info('ProductManager accessed', [
            'user_id' => session('user.id') ?? 'unknown',
            'active_tab' => $request->get('tab', 'items')
        ]);
        
        // Check access before continuing
        if (!session()->has('api_token') || !session()->has('user')) {
            Log::warning('ProductManager access denied: No token or user in session');
            return redirect()->route('login')->with('error', 'Sesi Anda telah berakhir. Silakan login kembali.');
        }
        
        // Check user role
        $user = session('user');
        if (!isset($user['role']) || ($user['role'] !== 'admin' && $user['role'] !== 'super_admin')) {
            Log::warning('ProductManager access denied: Invalid role', ['role' => $user['role'] ?? 'undefined']);
            return redirect()->route('login')->with('error', 'Anda tidak memiliki akses ke halaman ini');
        }
        
        $activeTab = $request->get('tab', 'items');
        
        // Set basic data untuk view
        $data = [
            'activeTab' => $activeTab,
        ];
        
        try {
            // Selalu muat dropdown items untuk modals
            Log::debug('Requesting items dropdown');
            $dropdownResponse = $this->itemViewController->getItemsDropdown();
            
            Log::debug('Dropdown response type', ['type' => gettype($dropdownResponse)]);
            
            if (isset($dropdownResponse['success']) && $dropdownResponse['success']) {
                $data['itemsDropdown'] = $dropdownResponse['data'] ?? [];
                Log::debug('Dropdown items loaded successfully', ['count' => count($data['itemsDropdown'])]);
            } else {
                Log::warning('Failed to load items dropdown', [
                    'success' => $dropdownResponse['success'] ?? false,
                    'message' => $dropdownResponse['message'] ?? 'Unknown error'
                ]);
                $data['itemsDropdown'] = [];
            }
            
            // Hanya muat data untuk tab items
            if ($activeTab == 'items') {
                Log::debug('Loading items data for active tab');
                $itemsResponse = $this->itemViewController->getItems($request);
                
                Log::debug('Items response', [
                    'type' => gettype($itemsResponse),
                    'success' => $itemsResponse['success'] ?? false,
                    'has_data' => isset($itemsResponse['data']),
                    'data_sample' => isset($itemsResponse['data']) ? json_encode(array_slice((array)$itemsResponse['data'], 0, 3)) : 'none'
                ]);
                
                if (isset($itemsResponse['success']) && $itemsResponse['success']) {
                    $data['items'] = $itemsResponse['data'];
                    Log::debug('Items data loaded successfully', [
                        'total' => $itemsResponse['data']['total'] ?? 'undefined',
                        'current_page' => $itemsResponse['data']['current_page'] ?? 'undefined',
                        'count' => isset($itemsResponse['data']['data']) ? count($itemsResponse['data']['data']) : 0
                    ]);
                } else {
                    Log::warning('Failed to load items', [
                        'success' => $itemsResponse['success'] ?? false,
                        'message' => $itemsResponse['message'] ?? 'Unknown error'
                    ]);
                    $data['items'] = [];
                }
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
     * Store a newly created item.
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
                Log::error('StoreItem failed: No token in session');
                return redirect()->route('login')->with('error', 'Session expired. Please login again.');
            }
            
            // Prepare form data
            $formData = [
                'nama_item' => $request->nama_item,
                'harga_dasar' => $request->harga_dasar,
                'deskripsi' => $request->deskripsi,
            ];
            
            Log::debug('Storing new item', [
                'nama_item' => $request->nama_item,
                'has_image' => $request->hasFile('gambar')
            ]);
            
            // Handle file upload if provided
            if ($request->hasFile('gambar')) {
                Log::debug('Processing image upload');
                $client = Http::withToken($token)
                    ->timeout(60) // Increase timeout to 60 seconds
                    ->asMultipart();
                $image = $request->file('gambar');
                
                $response = $client->attach(
                    'gambar', 
                    file_get_contents($image->getRealPath()),
                    $image->getClientOriginalName()
                )->post(config('app.url') . '/api/admin/items', $formData);
            } else {
                $response = Http::withToken($token)
                    ->timeout(60) // Increase timeout to 60 seconds
                    ->post(config('app.url') . '/api/admin/items', $formData);
            }
            
            if ($response->successful()) {
                Log::info('Item stored successfully');
                // Clear cache after successful operation
                $this->itemViewController->clearCache();
                
                return redirect()->route('admin.product-manager', ['tab' => 'items'])
                    ->with('success', 'Produk berhasil ditambahkan');
            } else {
                Log::error('API Error storeItem: ' . $response->body(), [
                    'status' => $response->status()
                ]);
                return redirect()->back()
                    ->with('error', 'Gagal menambahkan produk: ' . ($response->json()['message'] ?? 'Unknown error'))
                    ->withInput();
            }
            
        } catch (\Exception $e) {
            Log::error('Exception in storeItem: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan produk: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Update an existing item.
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
                Log::error('UpdateItem failed: No token in session');
                return redirect()->route('login')->with('error', 'Session expired. Please login again.');
            }
            
            // Prepare form data
            $formData = [
                'nama_item' => $request->nama_item,
                'harga_dasar' => $request->harga_dasar,
                'deskripsi' => $request->deskripsi,
            ];
            
            Log::debug('Updating item', [
                'id' => $id,
                'nama_item' => $request->nama_item,
                'has_image' => $request->hasFile('gambar')
            ]);
            
            // Handle file upload if provided
            if ($request->hasFile('gambar')) {
                Log::debug('Processing image upload for update');
                $client = Http::withToken($token)
                    ->timeout(60) // Increase timeout to 60 seconds
                    ->asMultipart();
                $image = $request->file('gambar');
                
                $response = $client->attach(
                    'gambar', 
                    file_get_contents($image->getRealPath()),
                    $image->getClientOriginalName()
                )->put(config('app.url') . '/api/admin/items/' . $id, $formData);
            } else {
                $response = Http::withToken($token)
                    ->timeout(60) // Increase timeout to 60 seconds
                    ->put(config('app.url') . '/api/admin/items/' . $id, $formData);
            }
            
            if ($response->successful()) {
                Log::info('Item updated successfully', ['id' => $id]);
                // Clear cache after successful operation
                $this->itemViewController->clearCache();
                
                return redirect()->route('admin.product-manager', ['tab' => 'items'])
                    ->with('success', 'Produk berhasil diperbarui');
            } else {
                Log::error('API Error updateItem: ' . $response->body(), [
                    'status' => $response->status(),
                    'id' => $id
                ]);
                return redirect()->back()
                    ->with('error', 'Gagal memperbarui produk: ' . ($response->json()['message'] ?? 'Unknown error'))
                    ->withInput();
            }
            
        } catch (\Exception $e) {
            Log::error('Exception in updateItem: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'id' => $id
            ]);
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memperbarui produk: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Delete an item.
     */
    public function destroyItem($id)
    {
        try {
            $token = session('api_token');
            
            if (!$token) {
                Log::error('DestroyItem failed: No token in session');
                return redirect()->route('login')->with('error', 'Session expired. Please login again.');
            }
            
            Log::debug('Deleting item', ['id' => $id]);
            $response = Http::withToken($token)
                ->timeout(60) // Increase timeout to 60 seconds
                ->delete(config('app.url') . '/api/admin/items/' . $id);
            
            if ($response->successful()) {
                Log::info('Item deleted successfully', ['id' => $id]);
                // Clear cache after successful operation
                $this->itemViewController->clearCache();
                
                return redirect()->route('admin.product-manager', ['tab' => 'items'])
                    ->with('success', 'Produk berhasil dihapus');
            } else {
                Log::error('API Error destroyItem: ' . $response->body(), [
                    'status' => $response->status(),
                    'id' => $id
                ]);
                return redirect()->back()
                    ->with('error', 'Gagal menghapus produk: ' . ($response->json()['message'] ?? 'Unknown error'));
            }
            
        } catch (\Exception $e) {
            Log::error('Exception in destroyItem: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'id' => $id
            ]);
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
                Log::error('EditItem failed: No token in session');
                return redirect()->route('login')->with('error', 'Session expired. Please login again.');
            }
            
            Log::debug('Fetching item for edit form', ['id' => $id]);
            $itemResponse = $this->itemViewController->getItem($id);
            
            if (isset($itemResponse['success']) && $itemResponse['success']) {
                Log::debug('Item fetched successfully for edit', ['id' => $id]);
                return view('admin.product-manager.edit-item', [
                    'item' => $itemResponse['data']
                ]);
            } else {
                Log::error('Failed to fetch item for edit', [
                    'id' => $id,
                    'message' => $itemResponse['message'] ?? 'Unknown error'
                ]);
                return redirect()->route('admin.product-manager', ['tab' => 'items'])
                    ->with('error', 'Gagal mengambil data produk: ' . ($itemResponse['message'] ?? 'Unknown error'));
            }
            
        } catch (\Exception $e) {
            Log::error('Exception in editItem: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'id' => $id
            ]);
            return redirect()->route('admin.product-manager', ['tab' => 'items'])
                ->with('error', 'Terjadi kesalahan saat mengambil data produk: ' . $e->getMessage());
        }
    }
}