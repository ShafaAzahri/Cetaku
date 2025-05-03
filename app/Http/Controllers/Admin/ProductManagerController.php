<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage; // Tambahkan ini

class ProductManagerController extends Controller
{
    protected $apiUrl;
    protected $apiToken;
    
    public function __construct()
    {
        $this->apiUrl = config('app.url') . '/api';
        $this->apiToken = session('api_token');
    }
    
    /**
     * Tampilkan halaman product manager
     */
    public function index()
    {
        $activeTab = request('tab', 'items');
        
        // Hanya muat data jika tab aktif adalah 'items'
        $items = $activeTab === 'items' ? $this->getItems() : [];
        
        return view('admin.product-manager', compact('activeTab', 'items'));
    }
    
    /**
     * Ambil data item dari API atau langsung dari database jika API gagal
     */
    private function getItems()
    {
        try {
            // Coba akses API terlebih dahulu
            $response = Http::withToken($this->apiToken)
                ->get($this->apiUrl . '/items');
            
            if ($response->successful()) {
                return $response->json('items', []);
            } else {
                Log::error('Gagal mengambil data item dari API', [
                    'status' => $response->status(),
                    'response' => $response->json()
                ]);
                
                // Fallback: ambil langsung dari database jika API gagal
                Log::info('Menggunakan fallback: ambil data item langsung dari database');
                $items = Item::all()->toArray();
                return $items;
            }
        } catch (\Exception $e) {
            Log::error('Error saat mengambil data item: ' . $e->getMessage());
            
            // Fallback: ambil langsung dari database jika API gagal
            Log::info('Menggunakan fallback: ambil data item langsung dari database');
            $items = Item::all()->toArray();
            return $items;
        }
    }
    
    /**
     * Simpan item baru
     */
    public function storeItem(Request $request)
    {
        $validatedData = $request->validate([
            'nama_item' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga_dasar' => 'required|numeric|min:0',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        try {
            // Coba simpan via API dengan file gambar
            $response = null;
            
            if ($request->hasFile('gambar')) {
                $response = Http::withToken($this->apiToken)
                    ->attach('gambar', file_get_contents($request->file('gambar')), $request->file('gambar')->getClientOriginalName())
                    ->post($this->apiUrl . '/items', [
                        'nama_item' => $request->nama_item,
                        'deskripsi' => $request->deskripsi,
                        'harga_dasar' => $request->harga_dasar,
                    ]);
            } else {
                $response = Http::withToken($this->apiToken)
                    ->post($this->apiUrl . '/items', $validatedData);
            }
            
            if ($response && $response->successful()) {
                return redirect()->route('admin.product-manager', ['tab' => 'items'])
                    ->with('success', 'Item berhasil ditambahkan');
            } else {
                Log::error('Gagal menyimpan item via API', [
                    'status' => $response ? $response->status() : 'No response',
                    'response' => $response ? $response->json() : null
                ]);
                
                // Fallback: simpan langsung ke database
                Log::info('Menggunakan fallback: simpan item langsung ke database');
                $item = new Item();
                $item->nama_item = $request->nama_item;
                $item->deskripsi = $request->deskripsi;
                $item->harga_dasar = $request->harga_dasar;
                
                // Upload gambar jika ada
                if ($request->hasFile('gambar')) {
                    $gambar = $request->file('gambar');
                    $gambarName = 'product-images/' . time() . '_' . $gambar->getClientOriginalName();
                    $gambar->storeAs('public', $gambarName);
                    $item->gambar = $gambarName;
                }
                
                $item->save();
                
                return redirect()->route('admin.product-manager', ['tab' => 'items'])
                    ->with('success', 'Item berhasil ditambahkan (via fallback)');
            }
        } catch (\Exception $e) {
            Log::error('Error saat menambah item: ' . $e->getMessage());
            
            // Fallback: simpan langsung ke database
            try {
                Log::info('Menggunakan fallback: simpan item langsung ke database');
                $item = new Item();
                $item->nama_item = $request->nama_item;
                $item->deskripsi = $request->deskripsi;
                $item->harga_dasar = $request->harga_dasar;
                
                // Upload gambar jika ada
                if ($request->hasFile('gambar')) {
                    $gambar = $request->file('gambar');
                    $gambarName = 'product-images/' . time() . '_' . $gambar->getClientOriginalName();
                    $gambar->storeAs('public', $gambarName);
                    $item->gambar = $gambarName;
                }
                
                $item->save();
                
                return redirect()->route('admin.product-manager', ['tab' => 'items'])
                    ->with('success', 'Item berhasil ditambahkan (via fallback)');
            } catch (\Exception $e2) {
                Log::error('Error saat fallback menambah item: ' . $e2->getMessage());
                return redirect()->back()
                    ->with('error', 'Terjadi kesalahan: ' . $e2->getMessage())
                    ->withInput();
            }
        }
    }
    
    /**
     * Update item
     */
    public function updateItem(Request $request, $id)
    {
        $validatedData = $request->validate([
            'nama_item' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga_dasar' => 'required|numeric|min:0',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        try {
            // Coba update via API dengan file gambar
            $response = null;
            
            if ($request->hasFile('gambar')) {
                $response = Http::withToken($this->apiToken)
                    ->attach('gambar', file_get_contents($request->file('gambar')), $request->file('gambar')->getClientOriginalName())
                    ->post($this->apiUrl . '/items/' . $id, array_merge($validatedData, ['_method' => 'PUT']));
            } else {
                $response = Http::withToken($this->apiToken)
                    ->put($this->apiUrl . '/items/' . $id, $validatedData);
            }
            
            if ($response && $response->successful()) {
                return redirect()->route('admin.product-manager', ['tab' => 'items'])
                    ->with('success', 'Item berhasil diperbarui');
            } else {
                Log::error('Gagal memperbarui item via API', [
                    'status' => $response ? $response->status() : 'No response',
                    'response' => $response ? $response->json() : null
                ]);
                
                // Fallback: update langsung di database
                Log::info('Menggunakan fallback: update item langsung di database');
                $item = Item::find($id);
                
                if (!$item) {
                    return redirect()->route('admin.product-manager', ['tab' => 'items'])
                        ->with('error', 'Item tidak ditemukan');
                }
                
                $item->nama_item = $request->nama_item;
                $item->deskripsi = $request->deskripsi;
                $item->harga_dasar = $request->harga_dasar;
                
                // Upload gambar baru jika ada
                if ($request->hasFile('gambar')) {
                    // Hapus gambar lama jika ada
                    if ($item->gambar) {
                        Storage::delete('public/' . $item->gambar);
                    }
                    
                    $gambar = $request->file('gambar');
                    $gambarName = 'product-images/' . time() . '_' . $gambar->getClientOriginalName();
                    $gambar->storeAs('public', $gambarName);
                    $item->gambar = $gambarName;
                }
                
                $item->save();
                
                return redirect()->route('admin.product-manager', ['tab' => 'items'])
                    ->with('success', 'Item berhasil diperbarui (via fallback)');
            }
        } catch (\Exception $e) {
            Log::error('Error saat memperbarui item: ' . $e->getMessage());
            
            // Fallback: update langsung di database
            try {
                Log::info('Menggunakan fallback: update item langsung di database');
                $item = Item::find($id);
                
                if (!$item) {
                    return redirect()->route('admin.product-manager', ['tab' => 'items'])
                        ->with('error', 'Item tidak ditemukan');
                }
                
                $item->nama_item = $request->nama_item;
                $item->deskripsi = $request->deskripsi;
                $item->harga_dasar = $request->harga_dasar;
                
                // Upload gambar baru jika ada
                if ($request->hasFile('gambar')) {
                    // Hapus gambar lama jika ada
                    if ($item->gambar) {
                        Storage::delete('public/' . $item->gambar);
                    }
                    
                    $gambar = $request->file('gambar');
                    $gambarName = 'product-images/' . time() . '_' . $gambar->getClientOriginalName();
                    $gambar->storeAs('public', $gambarName);
                    $item->gambar = $gambarName;
                }
                
                $item->save();
                
                return redirect()->route('admin.product-manager', ['tab' => 'items'])
                    ->with('success', 'Item berhasil diperbarui (via fallback)');
            } catch (\Exception $e2) {
                Log::error('Error saat fallback memperbarui item: ' . $e2->getMessage());
                return redirect()->back()
                    ->with('error', 'Terjadi kesalahan: ' . $e2->getMessage())
                    ->withInput();
            }
        }
    }
}