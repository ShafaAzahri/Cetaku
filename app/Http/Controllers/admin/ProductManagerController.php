<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Item;
use App\Models\Bahan;
use App\Models\Ukuran;
use App\Models\Jenis;
use App\Models\BiayaDesain;
use App\Models\ItemBahan;
use App\Models\ItemUkuran;
use App\Models\ItemJenis;

class ProductManagerController extends Controller
{
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
            // Selalu muat dropdown items untuk modals (langsung dari database)
            $data['itemsDropdown'] = Item::orderBy('nama_item')->get()->toArray();
            
            // Hanya muat data berdasarkan tab aktif
            switch ($activeTab) {
                case 'items':
                    // Query database langsung
                    $query = Item::query();
                    
                    // Search by name
                    if ($request->has('search')) {
                        $search = $request->input('search');
                        $query->where('nama_item', 'LIKE', "%{$search}%");
                    }
                    
                    // Sort
                    $sortField = $request->input('sort_by', 'id');
                    $sortDirection = $request->input('sort_direction', 'desc');
                    $query->orderBy($sortField, $sortDirection);
                    
                    // Pagination
                    $perPage = $request->input('per_page', 10);
                    $data['items'] = $query->paginate($perPage);
                    
                    // Count total items for dashboard stats
                    $data['itemsTotal'] = Item::count();
                    break;
                    
                case 'bahans':
                    // Query database langsung
                    $query = Bahan::query();
                    
                    // Search by name
                    if ($request->has('search')) {
                        $search = $request->input('search');
                        $query->where('nama_bahan', 'LIKE', "%{$search}%");
                    }
                    
                    // Sort
                    $sortField = $request->input('sort_by', 'id');
                    $sortDirection = $request->input('sort_direction', 'desc');
                    $query->orderBy($sortField, $sortDirection);
                    
                    // Pagination
                    $perPage = $request->input('per_page', 10);
                    $bahans = $query->paginate($perPage);
                    
                    // Load associated items
                    $bahans->each(function ($bahan) {
                        $bahan->load('items');
                    });
                    
                    $data['bahans'] = $bahans;
                    $data['bahansTotal'] = Bahan::count();
                    break;
                    
                case 'ukurans':
                    // Query database langsung
                    $query = Ukuran::query();
                    
                    // Search by size
                    if ($request->has('search')) {
                        $search = $request->input('search');
                        $query->where('size', 'LIKE', "%{$search}%");
                    }
                    
                    // Sort
                    $sortField = $request->input('sort_by', 'id');
                    $sortDirection = $request->input('sort_direction', 'desc');
                    $query->orderBy($sortField, $sortDirection);
                    
                    // Pagination
                    $perPage = $request->input('per_page', 10);
                    $ukurans = $query->paginate($perPage);
                    
                    // Load associated items
                    $ukurans->each(function ($ukuran) {
                        $ukuran->load('items');
                    });
                    
                    $data['ukurans'] = $ukurans;
                    $data['ukuransTotal'] = Ukuran::count();
                    break;
                    
                case 'jenis':
                    // Query database langsung
                    $query = Jenis::query();
                    
                    // Search by category
                    if ($request->has('search')) {
                        $search = $request->input('search');
                        $query->where('kategori', 'LIKE', "%{$search}%");
                    }
                    
                    // Sort
                    $sortField = $request->input('sort_by', 'id');
                    $sortDirection = $request->input('sort_direction', 'desc');
                    $query->orderBy($sortField, $sortDirection);
                    
                    // Pagination
                    $perPage = $request->input('per_page', 10);
                    $jenis = $query->paginate($perPage);
                    
                    // Load associated items
                    $jenis->each(function ($jenisItem) {
                        $jenisItem->load('items');
                    });
                    
                    $data['jenis'] = $jenis;
                    $data['jenisTotal'] = Jenis::count();
                    break;
                    
                case 'biaya-desain':
                    // Query database langsung
                    $query = BiayaDesain::query();
                    
                    // Search by description
                    if ($request->has('search')) {
                        $search = $request->input('search');
                        $query->where('deskripsi', 'LIKE', "%{$search}%");
                    }
                    
                    // Sort
                    $sortField = $request->input('sort_by', 'id');
                    $sortDirection = $request->input('sort_direction', 'desc');
                    $query->orderBy($sortField, $sortDirection);
                    
                    // Pagination
                    $perPage = $request->input('per_page', 10);
                    $data['biayaDesains'] = $query->paginate($perPage);
                    $data['biayaDesainsTotal'] = BiayaDesain::count();
                    break;
            }
            
            // Set total counts for stats
            $data['itemsTotal'] = $data['itemsTotal'] ?? Item::count();
            $data['bahansTotal'] = $data['bahansTotal'] ?? Bahan::count();
            $data['ukuransTotal'] = $data['ukuransTotal'] ?? Ukuran::count();
            $data['jenisTotal'] = $data['jenisTotal'] ?? Jenis::count();
            
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
            // Prepare item data
            $itemData = [
                'nama_item' => $request->nama_item,
                'harga_dasar' => $request->harga_dasar,
                'deskripsi' => $request->deskripsi,
            ];
            
            // Handle file upload if provided
            if ($request->hasFile('gambar')) {
                $gambar = $request->file('gambar');
                $path = $gambar->store('product-images', 'public');
                $itemData['gambar'] = $path;
            }
            
            // Create item directly in database
            $item = Item::create($itemData);
            
            Log::info('Item created successfully', ['item_id' => $item->id]);
            
            return redirect()->route('admin.product-manager', ['tab' => 'items'])
                ->with('success', 'Produk berhasil ditambahkan');
            
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
     * Show edit form for item
     */
    public function editItem($id)
    {
        try {
            // Find the item directly from database
            $item = Item::with(['bahans', 'ukurans', 'jenis'])->findOrFail($id);
            
            return view('admin.product-manager.edit-item', [
                'item' => $item
            ]);
            
        } catch (\Exception $e) {
            Log::error('Exception in editItem: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'id' => $id
            ]);
            return redirect()->route('admin.product-manager', ['tab' => 'items'])
                ->with('error', 'Terjadi kesalahan saat mengambil data produk: ' . $e->getMessage());
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
            // Find the item
            $item = Item::findOrFail($id);
            
            // Update item data
            $item->nama_item = $request->nama_item;
            $item->harga_dasar = $request->harga_dasar;
            $item->deskripsi = $request->deskripsi;
            
            // Handle file upload if provided
            if ($request->hasFile('gambar')) {
                // Delete old image if exists
                if ($item->gambar) {
                    Storage::disk('public')->delete($item->gambar);
                }
                
                $gambar = $request->file('gambar');
                $path = $gambar->store('product-images', 'public');
                $item->gambar = $path;
            }
            
            $item->save();
            
            Log::info('Item updated successfully', ['item_id' => $item->id]);
            
            return redirect()->route('admin.product-manager', ['tab' => 'items'])
                ->with('success', 'Produk berhasil diperbarui');
            
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
            // Find the item
            $item = Item::findOrFail($id);
            
            // Delete related records in pivot tables
            ItemBahan::where('item_id', $id)->delete();
            ItemUkuran::where('item_id', $id)->delete();
            ItemJenis::where('item_id', $id)->delete();
            
            // Delete image if exists
            if ($item->gambar) {
                Storage::disk('public')->delete($item->gambar);
            }
            
            // Delete the item
            $item->delete();
            
            Log::info('Item deleted successfully', ['item_id' => $id]);
            
            return redirect()->route('admin.product-manager', ['tab' => 'items'])
                ->with('success', 'Produk berhasil dihapus');
            
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
     * Store a newly created material (bahan).
     */
    public function storeBahan(Request $request)
    {
        $request->validate([
            'nama_bahan' => 'required|string|max:255',
            'biaya_tambahan' => 'required|numeric|min:0',
            'item_id' => 'required|exists:items,id',
        ]);
        
        try {
            // Create bahan
            $bahan = Bahan::create([
                'nama_bahan' => $request->nama_bahan,
                'biaya_tambahan' => $request->biaya_tambahan,
                'is_available' => $request->has('is_available') ? $request->is_available : true,
            ]);
            
            // Create association with item
            ItemBahan::create([
                'item_id' => $request->item_id,
                'bahan_id' => $bahan->id
            ]);
            
            Log::info('Bahan created successfully', ['bahan_id' => $bahan->id]);
            
            return redirect()->route('admin.product-manager', ['tab' => 'bahans'])
                ->with('success', 'Bahan berhasil ditambahkan');
            
        } catch (\Exception $e) {
            Log::error('Exception in storeBahan: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan bahan: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Show edit form for bahan
     */
    public function editBahan($id)
    {
        try {
            // Find the bahan directly from database
            $bahan = Bahan::with('items')->findOrFail($id);
            
            // Get items for dropdown
            $items = Item::orderBy('nama_item')->get();
            
            return view('admin.product-manager.edit-bahan', [
                'bahan' => $bahan,
                'items' => $items
            ]);
            
        } catch (\Exception $e) {
            Log::error('Exception in editBahan: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'id' => $id
            ]);
            return redirect()->route('admin.product-manager', ['tab' => 'bahans'])
                ->with('error', 'Terjadi kesalahan saat mengambil data bahan: ' . $e->getMessage());
        }
    }
    
    /**
     * Update an existing bahan.
     */
    public function updateBahan(Request $request, $id)
    {
        $request->validate([
            'nama_bahan' => 'required|string|max:255',
            'biaya_tambahan' => 'required|numeric|min:0',
            'item_id' => 'required|exists:items,id',
        ]);
        
        try {
            // Find the bahan
            $bahan = Bahan::findOrFail($id);
            
            // Update bahan data
            $bahan->nama_bahan = $request->nama_bahan;
            $bahan->biaya_tambahan = $request->biaya_tambahan;
            
            if ($request->has('is_available')) {
                $bahan->is_available = $request->is_available;
            }
            
            $bahan->save();
            
            // Update item association
            // First remove all existing associations
            ItemBahan::where('bahan_id', $bahan->id)->delete();
            
            // Create new association
            ItemBahan::create([
                'item_id' => $request->item_id,
                'bahan_id' => $bahan->id
            ]);
            
            Log::info('Bahan updated successfully', ['bahan_id' => $bahan->id]);
            
            return redirect()->route('admin.product-manager', ['tab' => 'bahans'])
                ->with('success', 'Bahan berhasil diperbarui');
            
        } catch (\Exception $e) {
            Log::error('Exception in updateBahan: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'id' => $id
            ]);
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memperbarui bahan: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Delete a bahan.
     */
    public function destroyBahan($id)
    {
        try {
            // Find the bahan
            $bahan = Bahan::findOrFail($id);
            
            // Delete associations in pivot table
            ItemBahan::where('bahan_id', $id)->delete();
            
            // Delete the bahan
            $bahan->delete();
            
            Log::info('Bahan deleted successfully', ['bahan_id' => $id]);
            
            return redirect()->route('admin.product-manager', ['tab' => 'bahans'])
                ->with('success', 'Bahan berhasil dihapus');
            
        } catch (\Exception $e) {
            Log::error('Exception in destroyBahan: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'id' => $id
            ]);
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menghapus bahan: ' . $e->getMessage());
        }
    }
    
    /**
     * Store a newly created ukuran.
     */
    public function storeUkuran(Request $request)
    {
        $request->validate([
            'size' => 'required|string|max:100',
            'faktor_harga' => 'required|numeric|min:0.1',
            'item_id' => 'required|exists:items,id',
        ]);
        
        try {
            // Create ukuran
            $ukuran = Ukuran::create([
                'size' => $request->size,
                'faktor_harga' => $request->faktor_harga,
            ]);
            
            // Create association with item
            ItemUkuran::create([
                'item_id' => $request->item_id,
                'ukuran_id' => $ukuran->id
            ]);
            
            Log::info('Ukuran created successfully', ['ukuran_id' => $ukuran->id]);
            
            return redirect()->route('admin.product-manager', ['tab' => 'ukurans'])
                ->with('success', 'Ukuran berhasil ditambahkan');
            
        } catch (\Exception $e) {
            Log::error('Exception in storeUkuran: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan ukuran: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Show edit form for ukuran
     */
    public function editUkuran($id)
    {
        try {
            // Find the ukuran directly from database
            $ukuran = Ukuran::with('items')->findOrFail($id);
            
            // Get items for dropdown
            $items = Item::orderBy('nama_item')->get();
            
            return view('admin.product-manager.edit-ukuran', [
                'ukuran' => $ukuran,
                'items' => $items
            ]);
            
        } catch (\Exception $e) {
            Log::error('Exception in editUkuran: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'id' => $id
            ]);
            return redirect()->route('admin.product-manager', ['tab' => 'ukurans'])
                ->with('error', 'Terjadi kesalahan saat mengambil data ukuran: ' . $e->getMessage());
        }
    }
    
    /**
     * Update an existing ukuran.
     */
    public function updateUkuran(Request $request, $id)
    {
        $request->validate([
            'size' => 'required|string|max:100',
            'faktor_harga' => 'required|numeric|min:0.1',
            'item_id' => 'required|exists:items,id',
        ]);
        
        try {
            // Find the ukuran
            $ukuran = Ukuran::findOrFail($id);
            
            // Update ukuran data
            $ukuran->size = $request->size;
            $ukuran->faktor_harga = $request->faktor_harga;
            $ukuran->save();
            
            // Update item association
            // First remove all existing associations
            ItemUkuran::where('ukuran_id', $ukuran->id)->delete();
            
            // Create new association
            ItemUkuran::create([
                'item_id' => $request->item_id,
                'ukuran_id' => $ukuran->id
            ]);
            
            Log::info('Ukuran updated successfully', ['ukuran_id' => $ukuran->id]);
            
            return redirect()->route('admin.product-manager', ['tab' => 'ukurans'])
                ->with('success', 'Ukuran berhasil diperbarui');
            
        } catch (\Exception $e) {
            Log::error('Exception in updateUkuran: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'id' => $id
            ]);
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memperbarui ukuran: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Delete a ukuran.
     */
    public function destroyUkuran($id)
    {
        try {
            // Find the ukuran
            $ukuran = Ukuran::findOrFail($id);
            
            // Delete associations in pivot table
            ItemUkuran::where('ukuran_id', $id)->delete();
            
            // Delete the ukuran
            $ukuran->delete();
            
            Log::info('Ukuran deleted successfully', ['ukuran_id' => $id]);
            
            return redirect()->route('admin.product-manager', ['tab' => 'ukurans'])
                ->with('success', 'Ukuran berhasil dihapus');
            
        } catch (\Exception $e) {
            Log::error('Exception in destroyUkuran: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'id' => $id
            ]);
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menghapus ukuran: ' . $e->getMessage());
        }
    }
    
    /**
     * Store a newly created jenis.
     */
    public function storeJenis(Request $request)
    {
        $request->validate([
            'kategori' => 'required|string|max:255',
            'biaya_tambahan' => 'required|numeric|min:0',
            'item_id' => 'required|exists:items,id',
        ]);
        
        try {
            // Create jenis
            $jenis = Jenis::create([
                'kategori' => $request->kategori,
                'biaya_tambahan' => $request->biaya_tambahan,
            ]);
            
            // Create association with item
            ItemJenis::create([
                'item_id' => $request->item_id,
                'jenis_id' => $jenis->id
            ]);
            
            Log::info('Jenis created successfully', ['jenis_id' => $jenis->id]);
            
            return redirect()->route('admin.product-manager', ['tab' => 'jenis'])
                ->with('success', 'Jenis berhasil ditambahkan');
            
        } catch (\Exception $e) {
            Log::error('Exception in storeJenis: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan jenis: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Show edit form for jenis
     */
    public function editJenis($id)
    {
        try {
            // Find the jenis directly from database
            $jenis = Jenis::with('items')->findOrFail($id);
            
            // Get items for dropdown
            $items = Item::orderBy('nama_item')->get();
            
            return view('admin.product-manager.edit-jenis', [
                'jenis' => $jenis,
                'items' => $items
            ]);
            
        } catch (\Exception $e) {
            Log::error('Exception in editJenis: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'id' => $id
            ]);
            return redirect()->route('admin.product-manager', ['tab' => 'jenis'])
                ->with('error', 'Terjadi kesalahan saat mengambil data jenis: ' . $e->getMessage());
        }
    }
    
    /**
     * Update an existing jenis.
     */
    public function updateJenis(Request $request, $id)
    {
        $request->validate([
            'kategori' => 'required|string|max:255',
            'biaya_tambahan' => 'required|numeric|min:0',
            'item_id' => 'required|exists:items,id',
        ]);
        
        try {
            // Find the jenis
            $jenis = Jenis::findOrFail($id);
            
            // Update jenis data
            $jenis->kategori = $request->kategori;
            $jenis->biaya_tambahan = $request->biaya_tambahan;
            $jenis->save();
            
            // Update item association
            // First remove all existing associations
            ItemJenis::where('jenis_id', $jenis->id)->delete();
            
            // Create new association
            ItemJenis::create([
                'item_id' => $request->item_id,
                'jenis_id' => $jenis->id
            ]);
            
            Log::info('Jenis updated successfully', ['jenis_id' => $jenis->id]);
            
            return redirect()->route('admin.product-manager', ['tab' => 'jenis'])
                ->with('success', 'Jenis berhasil diperbarui');
            
        } catch (\Exception $e) {
            Log::error('Exception in updateJenis: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'id' => $id
            ]);
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memperbarui jenis: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Delete a jenis.
     */
    public function destroyJenis($id)
    {
        try {
            // Find the jenis
            $jenis = Jenis::findOrFail($id);
            
            // Delete associations in pivot table
            ItemJenis::where('jenis_id', $id)->delete();
            
            // Delete the jenis
            $jenis->delete();
            
            Log::info('Jenis deleted successfully', ['jenis_id' => $id]);
            
            return redirect()->route('admin.product-manager', ['tab' => 'jenis'])
                ->with('success', 'Jenis berhasil dihapus');
            
        } catch (\Exception $e) {
            Log::error('Exception in destroyJenis: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'id' => $id
            ]);
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menghapus jenis: ' . $e->getMessage());
        }
    }
    
    /**
     * Store a newly created biaya desain.
     */
    public function storeBiayaDesain(Request $request)
    {
        $request->validate([
            'biaya' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
        ]);
        
        try {
            // Create biaya desain
            $biayaDesain = BiayaDesain::create([
                'biaya' => $request->biaya,
                'deskripsi' => $request->deskripsi,
            ]);
            
            Log::info('BiayaDesain created successfully', ['biaya_desain_id' => $biayaDesain->id]);
            
            return redirect()->route('admin.product-manager', ['tab' => 'biaya-desain'])
                ->with('success', 'Biaya Desain berhasil ditambahkan');
            
        } catch (\Exception $e) {
            Log::error('Exception in storeBiayaDesain: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan biaya desain: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Show edit form for biaya desain
     */
    public function editBiayaDesain($id)
    {
        try {
            // Find the biaya desain directly from database
            $biayaDesain = BiayaDesain::findOrFail($id);
            
            return view('admin.product-manager.edit-biaya-desain', [
                'biayaDesain' => $biayaDesain
            ]);
            
        } catch (\Exception $e) {
            Log::error('Exception in editBiayaDesain: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'id' => $id
            ]);
            return redirect()->route('admin.product-manager', ['tab' => 'biaya-desain'])
                ->with('error', 'Terjadi kesalahan saat mengambil data biaya desain: ' . $e->getMessage());
        }
    }
    
    /**
     * Update an existing biaya desain.
     */
    public function updateBiayaDesain(Request $request, $id)
    {
        $request->validate([
            'biaya' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
        ]);
        
        try {
            // Find the biaya desain
            $biayaDesain = BiayaDesain::findOrFail($id);
            
            // Update biaya desain data
            $biayaDesain->biaya = $request->biaya;
            $biayaDesain->deskripsi = $request->deskripsi;
            $biayaDesain->save();
            
            Log::info('BiayaDesain updated successfully', ['biaya_desain_id' => $biayaDesain->id]);
            
            return redirect()->route('admin.product-manager', ['tab' => 'biaya-desain'])
                ->with('success', 'Biaya Desain berhasil diperbarui');
            
        } catch (\Exception $e) {
            Log::error('Exception in updateBiayaDesain: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'id' => $id
            ]);
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memperbarui biaya desain: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Delete a biaya desain.
     */
    public function destroyBiayaDesain($id)
    {
        try {
            // Find the biaya desain
            $biayaDesain = BiayaDesain::findOrFail($id);
            
            // Delete the biaya desain
            $biayaDesain->delete();
            
            Log::info('BiayaDesain deleted successfully', ['biaya_desain_id' => $id]);
            
            return redirect()->route('admin.product-manager', ['tab' => 'biaya-desain'])
                ->with('success', 'Biaya Desain berhasil dihapus');
            
        } catch (\Exception $e) {
            Log::error('Exception in destroyBiayaDesain: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'id' => $id
            ]);
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menghapus biaya desain: ' . $e->getMessage());
        }
    }
}