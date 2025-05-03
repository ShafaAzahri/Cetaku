<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Http\Controllers\API\ItemApiController;
use Illuminate\Support\Facades\Route;

class ProductManagerController extends Controller
{
    protected $apiBaseUrl;
    public function __construct()
    {
        // Gunakan API_URL dari .env, bukan app.url
        $this->apiBaseUrl = rtrim(env('API_URL', 'http://127.0.0.1:8001'), '/');
        
        Log::debug('API Base URL Configuration', [
            'api_base_url' => $this->apiBaseUrl,
            'api_items_url' => $this->apiBaseUrl . '/api/items',
            'app_url' => config('app.url'),
            'env_api_url' => env('API_URL')
        ]);
    }
    
    /**
     * Tampilkan halaman product manager
     */
    public function index()
    {
            $activeTab = request('tab', 'items');
            $items = [];
            
            try {
                if ($activeTab === 'items') {
                    // Buat instance controller API dan panggil metode index
                    $apiController = new ItemApiController();
                    $response = $apiController->index();
                    
                    // Ambil data dari response JSON
                    $data = $response->getData(true);
                    $items = $data['items'] ?? [];
                    
                    Log::info('Berhasil mengambil data dari API controller', ['count' => count($items)]);
                }
            } catch (\Exception $e) {
                Log::error('Error mengambil data: ' . $e->getMessage());
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
            Log::debug('Attempting to store item via API controller', [
                'data' => $request->except('gambar'),
                'has_file' => $request->hasFile('gambar')
            ]);
            
            // Buat instance controller API dan panggil metode store
            $apiController = new ItemApiController();
            $response = $apiController->store($request);
            
            // Ambil data dari response JSON
            $data = $response->getData(true);
            
            if ($data['success'] ?? false) {
                Log::info('Item berhasil disimpan via API controller', ['item' => $data['item'] ?? []]);
                return redirect()->route('admin.product-manager', ['tab' => 'items'])
                    ->with('success', 'Item berhasil ditambahkan');
            } else {
                throw new \Exception('API error: ' . ($data['message'] ?? 'Unknown error'));
            }
        } catch (\Exception $e) {
            Log::error('Error menyimpan item: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
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
            Log::debug('Attempting to update item via API controller', [
                'item_id' => $id,
                'data' => $request->except('gambar'),
                'has_file' => $request->hasFile('gambar')
            ]);
            
            // Buat instance controller API dan panggil metode update
            $apiController = new ItemApiController();
            $response = $apiController->update($request, $id);
            
            // Ambil data dari response JSON
            $data = $response->getData(true);
            
            if ($data['success'] ?? false) {
                Log::info('Item berhasil diperbarui via API controller', ['item_id' => $id]);
                return redirect()->route('admin.product-manager', ['tab' => 'items'])
                    ->with('success', 'Item berhasil diperbarui');
            } else {
                throw new \Exception('API error: ' . ($data['message'] ?? 'Unknown error'));
            }
        } catch (\Exception $e) {
            Log::error('Error memperbarui item: ' . $e->getMessage(), [
                'item_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified item
     */
    public function destroyItem($id)
    {
        try {
            Log::debug('Attempting to delete item via API controller', [
                'item_id' => $id
            ]);
            
            // Buat instance controller API dan panggil metode destroy
            $apiController = new ItemApiController();
            $response = $apiController->destroy($id);
            
            // Ambil data dari response JSON
            $data = $response->getData(true);
            
            if ($data['success'] ?? false) {
                Log::info('Item berhasil dihapus via API controller', ['item_id' => $id]);
                return redirect()->route('admin.product-manager', ['tab' => 'items'])
                    ->with('success', 'Item berhasil dihapus');
            } else {
                throw new \Exception('API error: ' . ($data['message'] ?? 'Unknown error'));
            }
        } catch (\Exception $e) {
            Log::error('Error menghapus item: ' . $e->getMessage(), [
                'item_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}