<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Admin\ItemViewController;
use Illuminate\Support\Facades\Http; // Tambahkan import ini

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
            return redirect()->route('login')->with('error', 'Sesi Anda telah berakhir. Silakan login kembali.');
        }
        
        // Check user role
        $user = session('user');
        if (!isset($user['role']) || ($user['role'] !== 'admin' && $user['role'] !== 'super_admin')) {
            return redirect()->route('login')->with('error', 'Anda tidak memiliki akses ke halaman ini');
        }
        
        $activeTab = $request->get('tab', 'items');
        
        // Set basic data untuk view
        $data = [
            'activeTab' => $activeTab,
        ];
        
        try {
            // Selalu muat dropdown items untuk modals
            $dropdownResponse = $this->itemViewController->getItemsDropdown();
            
            // Jika response adalah instance dari JsonResponse, dapatkan original content
            if (method_exists($dropdownResponse, 'getOriginalContent')) {
                $dropdownData = $dropdownResponse->getOriginalContent();
            } else {
                $dropdownData = $dropdownResponse;
            }
            
            if (isset($dropdownData['success']) && $dropdownData['success']) {
                $data['itemsDropdown'] = $dropdownData['data'] ?? [];
            } else {
                Log::warning('Failed to load items dropdown', [
                    'response' => $dropdownData
                ]);
                $data['itemsDropdown'] = [];
            }
            
            // Hanya muat data untuk tab items
            if ($activeTab == 'items') {
                $itemsResponse = $this->itemViewController->getItems($request);
                
                // Jika response adalah instance dari JsonResponse, dapatkan original content
                if (method_exists($itemsResponse, 'getOriginalContent')) {
                    $itemsData = $itemsResponse->getOriginalContent();
                } else {
                    $itemsData = $itemsResponse;
                }
                
                if (isset($itemsData['success']) && $itemsData['success']) {
                    $data['items'] = $itemsData['data'];
                } else {
                    Log::warning('Failed to load items', [
                        'response' => $itemsData
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
                // Clear cache after successful operation
                $this->itemViewController->clearCache();
                
                return redirect()->route('admin.product-manager', ['tab' => 'items'])
                    ->with('success', 'Produk berhasil ditambahkan');
            } else {
                Log::error('API Error storeItem: ' . $response->body());
                return redirect()->back()
                    ->with('error', 'Gagal menambahkan produk: ' . ($response->json()['message'] ?? 'Unknown error'))
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
                // Clear cache after successful operation
                $this->itemViewController->clearCache();
                
                return redirect()->route('admin.product-manager', ['tab' => 'items'])
                    ->with('success', 'Produk berhasil diperbarui');
            } else {
                Log::error('API Error updateItem: ' . $response->body());
                return redirect()->back()
                    ->with('error', 'Gagal memperbarui produk: ' . ($response->json()['message'] ?? 'Unknown error'))
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
     * Delete an item.
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
                // Clear cache after successful operation
                $this->itemViewController->clearCache();
                
                return redirect()->route('admin.product-manager', ['tab' => 'items'])
                    ->with('success', 'Produk berhasil dihapus');
            } else {
                Log::error('API Error destroyItem: ' . $response->body());
                return redirect()->back()
                    ->with('error', 'Gagal menghapus produk: ' . ($response->json()['message'] ?? 'Unknown error'));
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
            
            // Get item data
            $itemResponse = $this->itemViewController->getItem($id);
            
            // Jika response adalah instance dari JsonResponse, dapatkan original content
            if (method_exists($itemResponse, 'getOriginalContent')) {
                $itemData = $itemResponse->getOriginalContent();
            } else {
                $itemData = $itemResponse;
            }
            
            if (isset($itemData['success']) && $itemData['success']) {
                return view('admin.product-manager.edit-item', [
                    'item' => $itemData['data']
                ]);
            } else {
                Log::error('API Error editItem: ' . json_encode($itemData));
                return redirect()->route('admin.product-manager', ['tab' => 'items'])
                    ->with('error', 'Gagal mengambil data produk: ' . ($itemData['message'] ?? 'Unknown error'));
            }
            
        } catch (\Exception $e) {
            Log::error('Error in editItem: ' . $e->getMessage());
            return redirect()->route('admin.product-manager', ['tab' => 'items'])
                ->with('error', 'Terjadi kesalahan saat mengambil data produk: ' . $e->getMessage());
        }
    }
}