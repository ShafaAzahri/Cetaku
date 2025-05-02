<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /**
     * Display product manager page
     */
    public function index()
    {
        // Initialize empty arrays to prevent undefined variable errors
        $products = [];
        $materials = [];
        $sizes = [];
        $categories = [];
        $designCosts = [];
        
        try {
            $token = session('api_token');
            
            if (!$token) {
                return redirect()->route('login')->with('error', 'Session expired. Please login again.');
            }
            
            // Fetch products
            try {
                $response = Http::withToken($token)->get(config('app.url') . '/api/admin/items?per_page=100');
                if ($response->successful()) {
                    $data = $response->json();
                    $products = isset($data['data']['data']) ? $data['data']['data'] : [];
                }
            } catch (\Exception $e) {
                Log::error('Error fetching products: ' . $e->getMessage());
            }
            
            // Fetch materials
            try {
                $response = Http::withToken($token)->get(config('app.url') . '/api/admin/bahans?per_page=100');
                if ($response->successful()) {
                    $data = $response->json();
                    $materials = isset($data['data']['data']) ? $data['data']['data'] : [];
                }
            } catch (\Exception $e) {
                Log::error('Error fetching materials: ' . $e->getMessage());
            }
            
            // Fetch sizes
            try {
                $response = Http::withToken($token)->get(config('app.url') . '/api/admin/ukurans?per_page=100');
                if ($response->successful()) {
                    $data = $response->json();
                    $sizes = isset($data['data']['data']) ? $data['data']['data'] : [];
                }
            } catch (\Exception $e) {
                Log::error('Error fetching sizes: ' . $e->getMessage());
            }
            
            // Fetch categories
            try {
                $response = Http::withToken($token)->get(config('app.url') . '/api/admin/jenis?per_page=100');
                if ($response->successful()) {
                    $data = $response->json();
                    $categories = isset($data['data']['data']) ? $data['data']['data'] : [];
                }
            } catch (\Exception $e) {
                Log::error('Error fetching categories: ' . $e->getMessage());
            }
            
            // Fetch design costs
            try {
                $response = Http::withToken($token)->get(config('app.url') . '/api/admin/biaya-desain?per_page=100');
                if ($response->successful()) {
                    $data = $response->json();
                    $designCosts = isset($data['data']['data']) ? $data['data']['data'] : [];
                }
            } catch (\Exception $e) {
                Log::error('Error fetching design costs: ' . $e->getMessage());
            }
            
        } catch (\Exception $e) {
            Log::error('General error in product index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading product data');
        }
        
        // Return view with all data
        return view('admin.product-manager', compact(
            'products', 
            'materials', 
            'sizes', 
            'categories', 
            'designCosts'
        ));
    }
    
    /**
     * Store new product
     */
    public function storeProduct(Request $request)
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
            
            $client = Http::withToken($token);
            
            // If there's an image, use multipart form
            if ($request->hasFile('gambar')) {
                $image = $request->file('gambar');
                
                $client = Http::withToken($token)
                    ->attach(
                        'gambar',
                        file_get_contents($image->getRealPath()),
                        $image->getClientOriginalName()
                    );
            }
            
            // Send request with form data
            $response = $client->post(config('app.url') . '/api/admin/items', [
                [
                    'name' => 'nama_item',
                    'contents' => $request->nama_item
                ],
                [
                    'name' => 'harga_dasar',
                    'contents' => $request->harga_dasar
                ],
                [
                    'name' => 'deskripsi',
                    'contents' => $request->deskripsi ?? ''
                ]
            ]);
            
            if ($response->successful()) {
                return redirect()->route('admin.product-manager')
                    ->with('success', 'Produk berhasil ditambahkan');
            } else {
                Log::error('API Error: ' . $response->body());
                return redirect()->back()
                    ->with('error', 'Gagal menambahkan produk: ' . $response->json()['message'] ?? 'Unknown error')
                    ->withInput();
            }
            
        } catch (\Exception $e) {
            Log::error('Error storing product: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan produk: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Delete product
     */
    public function deleteProduct($id)
    {
        try {
            $token = session('api_token');
            
            if (!$token) {
                return redirect()->route('login')->with('error', 'Session expired. Please login again.');
            }
            
            $response = Http::withToken($token)
                ->delete(config('app.url') . '/api/admin/items/' . $id);
            
            if ($response->successful()) {
                return redirect()->route('admin.product-manager')
                    ->with('success', 'Produk berhasil dihapus');
            } else {
                return redirect()->back()
                    ->with('error', 'Gagal menghapus produk: ' . $response->json()['message'] ?? 'Unknown error');
            }
            
        } catch (\Exception $e) {
            Log::error('Error deleting product: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menghapus produk');
        }
    }
    
    /**
     * Store new material
     */
    public function storeMaterial(Request $request)
    {
        $request->validate([
            'item_id' => 'required|numeric',
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
                return redirect()->route('admin.product-manager')
                    ->with('success', 'Bahan berhasil ditambahkan')
                    ->with('active_tab', 'bahans');
            } else {
                Log::error('API Error: ' . $response->body());
                return redirect()->back()
                    ->with('error', 'Gagal menambahkan bahan: ' . $response->json()['message'] ?? 'Unknown error')
                    ->withInput();
            }
            
        } catch (\Exception $e) {
            Log::error('Error storing material: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan bahan')
                ->withInput();
        }
    }
    
    /**
     * Delete material
     */
    public function deleteMaterial($id)
    {
        try {
            $token = session('api_token');
            
            if (!$token) {
                return redirect()->route('login')->with('error', 'Session expired. Please login again.');
            }
            
            $response = Http::withToken($token)
                ->delete(config('app.url') . '/api/admin/bahans/' . $id);
            
            if ($response->successful()) {
                return redirect()->route('admin.product-manager')
                    ->with('success', 'Bahan berhasil dihapus')
                    ->with('active_tab', 'bahans');
            } else {
                return redirect()->back()
                    ->with('error', 'Gagal menghapus bahan: ' . $response->json()['message'] ?? 'Unknown error');
            }
            
        } catch (\Exception $e) {
            Log::error('Error deleting material: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menghapus bahan');
        }
    }
}