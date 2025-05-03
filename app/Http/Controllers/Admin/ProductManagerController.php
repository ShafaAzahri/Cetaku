<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\MultipartStream;

class ProductManagerController extends Controller
{
    protected $apiBaseUrl;
    protected $client;
    
    public function __construct()
    {
        $this->apiBaseUrl = config('app.url') . '/api';
        $this->client = new Client([
            'base_uri' => $this->apiBaseUrl,
            'timeout' => 30,
        ]);
    }
    
    /**
     * Tampilkan halaman product manager
     */
    public function index()
    {
        $activeTab = request('tab', 'items');
        
        // Hanya muat data jika tab aktif adalah 'items'
        $items = [];
        
        try {
            if ($activeTab === 'items') {
                // Log untuk debugging
                Log::debug('Requesting API URL: ' . $this->apiBaseUrl . '/items');
                
                // Gunakan Guzzle untuk request
                $response = $this->client->request('GET', '/items');
                
                if ($response->getStatusCode() == 200) {
                    $data = json_decode($response->getBody(), true);
                    $items = $data['items'] ?? [];
                    Log::info('Berhasil mengambil data dari API', ['count' => count($items)]);
                } else {
                    Log::error('Gagal mengambil data dari API', [
                        'status' => $response->getStatusCode(),
                        'body' => $response->getBody()->getContents()
                    ]);
                    
                    // Fallback: ambil langsung dari database
                    Log::info('Menggunakan fallback: ambil data item langsung dari database');
                    $items = Item::all()->toArray();
                }
            }
        } catch (RequestException $e) {
            Log::error('Error Guzzle mengambil data: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            // Fallback: ambil langsung dari database
            Log::info('Menggunakan fallback: ambil data item langsung dari database');
            $items = Item::all()->toArray();
        }
        
        return view('admin.product-manager', compact('activeTab', 'items'));
    }
    
    /**
     * Store a newly created item
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
            // Log debugging info
            Log::debug('Attempting to store item via API with Guzzle', [
                'api_url' => $this->apiBaseUrl . '/items',
                'has_file' => $request->hasFile('gambar')
            ]);
            
            // Persiapkan multipart data untuk Guzzle
            $multipartData = [
                [
                    'name' => 'nama_item',
                    'contents' => $request->nama_item
                ],
                [
                    'name' => 'deskripsi',
                    'contents' => $request->deskripsi ?? ''
                ],
                [
                    'name' => 'harga_dasar',
                    'contents' => $request->harga_dasar
                ]
            ];
            
            // Tambahkan file jika ada
            if ($request->hasFile('gambar')) {
                $file = $request->file('gambar');
                $multipartData[] = [
                    'name' => 'gambar',
                    'contents' => fopen($file->getPathname(), 'r'),
                    'filename' => $file->getClientOriginalName(),
                    'headers' => [
                        'Content-Type' => $file->getMimeType()
                    ]
                ];
            }
            
            // Kirim permintaan dengan Guzzle
            $response = $this->client->request('POST', '/items', [
                'multipart' => $multipartData
            ]);
            
            if ($response->getStatusCode() == 200 || $response->getStatusCode() == 201) {
                $data = json_decode($response->getBody(), true);
                Log::info('Item berhasil disimpan via API', ['item' => $data['item'] ?? []]);
                return redirect()->route('admin.product-manager', ['tab' => 'items'])
                    ->with('success', 'Item berhasil ditambahkan');
            } else {
                Log::error('Gagal menyimpan item via API', [
                    'status' => $response->getStatusCode(),
                    'body' => $response->getBody()->getContents()
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
                    $gambarName = 'product-images/' . Str::slug($request->nama_item) . '_' . time() . '.' . $gambar->getClientOriginalExtension();
                    $gambar->storeAs('public', $gambarName);
                    $item->gambar = $gambarName;
                }
                
                $item->save();
                
                return redirect()->route('admin.product-manager', ['tab' => 'items'])
                    ->with('success', 'Item berhasil ditambahkan (via fallback)');
            }
        } catch (RequestException $e) {
            Log::error('Error Guzzle menyimpan item: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            // Fallback: simpan langsung ke database
            try {
                $item = new Item();
                $item->nama_item = $request->nama_item;
                $item->deskripsi = $request->deskripsi;
                $item->harga_dasar = $request->harga_dasar;
                
                // Upload gambar jika ada
                if ($request->hasFile('gambar')) {
                    $gambar = $request->file('gambar');
                    $gambarName = 'product-images/' . Str::slug($request->nama_item) . '_' . time() . '.' . $gambar->getClientOriginalExtension();
                    $gambar->storeAs('public', $gambarName);
                    $item->gambar = $gambarName;
                }
                
                $item->save();
                
                return redirect()->route('admin.product-manager', ['tab' => 'items'])
                    ->with('success', 'Item berhasil ditambahkan (via fallback)');
            } catch (\Exception $e2) {
                Log::error('Error fallback menyimpan item: ' . $e2->getMessage());
                return redirect()->back()
                    ->with('error', 'Terjadi kesalahan: ' . $e2->getMessage())
                    ->withInput();
            }
        }
    }
    
    /**
     * Update the specified item
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
            // Persiapkan multipart data untuk Guzzle
            $multipartData = [
                [
                    'name' => 'nama_item',
                    'contents' => $request->nama_item
                ],
                [
                    'name' => 'deskripsi',
                    'contents' => $request->deskripsi ?? ''
                ],
                [
                    'name' => 'harga_dasar',
                    'contents' => $request->harga_dasar
                ],
                [
                    'name' => '_method',
                    'contents' => 'PUT'
                ]
            ];
            
            // Tambahkan file jika ada
            if ($request->hasFile('gambar')) {
                $file = $request->file('gambar');
                $multipartData[] = [
                    'name' => 'gambar',
                    'contents' => fopen($file->getPathname(), 'r'),
                    'filename' => $file->getClientOriginalName(),
                    'headers' => [
                        'Content-Type' => $file->getMimeType()
                    ]
                ];
            }
            
            // Kirim permintaan dengan Guzzle
            $response = $this->client->request('POST', '/items/' . $id, [
                'multipart' => $multipartData
            ]);
            
            if ($response->getStatusCode() == 200) {
                $data = json_decode($response->getBody(), true);
                Log::info('Item berhasil diperbarui via API', ['item_id' => $id]);
                return redirect()->route('admin.product-manager', ['tab' => 'items'])
                    ->with('success', 'Item berhasil diperbarui');
            } else {
                Log::error('Gagal memperbarui item via API', [
                    'status' => $response->getStatusCode(),
                    'body' => $response->getBody()->getContents()
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
                    $gambarName = 'product-images/' . Str::slug($request->nama_item) . '_' . time() . '.' . $gambar->getClientOriginalExtension();
                    $gambar->storeAs('public', $gambarName);
                    $item->gambar = $gambarName;
                }
                
                $item->save();
                
                return redirect()->route('admin.product-manager', ['tab' => 'items'])
                    ->with('success', 'Item berhasil diperbarui (via fallback)');
            }
        } catch (RequestException $e) {
            Log::error('Error Guzzle memperbarui item: ' . $e->getMessage(), [
                'item_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            // Fallback: update langsung di database
            try {
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
                    $gambarName = 'product-images/' . Str::slug($request->nama_item) . '_' . time() . '.' . $gambar->getClientOriginalExtension();
                    $gambar->storeAs('public', $gambarName);
                    $item->gambar = $gambarName;
                }
                
                $item->save();
                
                return redirect()->route('admin.product-manager', ['tab' => 'items'])
                    ->with('success', 'Item berhasil diperbarui (via fallback)');
            } catch (\Exception $e2) {
                Log::error('Error fallback memperbarui item: ' . $e2->getMessage());
                return redirect()->back()
                    ->with('error', 'Terjadi kesalahan: ' . $e2->getMessage())
                    ->withInput();
            }
        }
    }

    /**
     * Remove the specified item
     */
    public function destroyItem($id)
    {
        try {
            // Kirim permintaan hapus dengan Guzzle
            $response = $this->client->request('DELETE', '/items/' . $id);
            
            if ($response->getStatusCode() == 200) {
                Log::info('Item berhasil dihapus via API', ['item_id' => $id]);
                return redirect()->route('admin.product-manager', ['tab' => 'items'])
                    ->with('success', 'Item berhasil dihapus');
            } else {
                Log::error('Gagal menghapus item via API', [
                    'status' => $response->getStatusCode(),
                    'body' => $response->getBody()->getContents()
                ]);
                
                // Fallback: hapus langsung di database
                Log::info('Menggunakan fallback: hapus item langsung di database');
                $item = Item::find($id);
                
                if (!$item) {
                    return redirect()->route('admin.product-manager', ['tab' => 'items'])
                        ->with('error', 'Item tidak ditemukan');
                }
                
                // Hapus gambar jika ada
                if ($item->gambar) {
                    Storage::delete('public/' . $item->gambar);
                }
                
                $item->delete();
                
                return redirect()->route('admin.product-manager', ['tab' => 'items'])
                    ->with('success', 'Item berhasil dihapus (via fallback)');
            }
        } catch (RequestException $e) {
            Log::error('Error Guzzle menghapus item: ' . $e->getMessage(), [
                'item_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            // Fallback: hapus langsung di database
            try {
                $item = Item::find($id);
                
                if (!$item) {
                    return redirect()->route('admin.product-manager', ['tab' => 'items'])
                        ->with('error', 'Item tidak ditemukan');
                }
                
                // Hapus gambar jika ada
                if ($item->gambar) {
                    Storage::delete('public/' . $item->gambar);
                }
                
                $item->delete();
                
                return redirect()->route('admin.product-manager', ['tab' => 'items'])
                    ->with('success', 'Item berhasil dihapus (via fallback)');
            } catch (\Exception $e2) {
                Log::error('Error fallback menghapus item: ' . $e2->getMessage());
                return redirect()->back()
                    ->with('error', 'Terjadi kesalahan: ' . $e2->getMessage());
            }
        }
    }
}