<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\DetailPesanan;

class ProductController extends Controller
{
    protected $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = rtrim(env('API_URL', config('app.url')), '/');
    }

    /**
     * Menampilkan daftar produk dengan fitur pengurutan dan filter kategori
     */
    public function index(Request $request)
    {
        try {
            $sortBy = $request->get('sort', 'terbaru');
            $kategoriNama = $request->get('kategori');
            
            // Ambil semua produk dari API
            $itemsResponse = Http::timeout(10)->get($this->apiBaseUrl . '/items');
            $items = $itemsResponse->successful() ? ($itemsResponse->json()['items'] ?? []) : [];
            
            // Ambil kategori untuk filter
            $kategorisResponse = Http::timeout(10)->get($this->apiBaseUrl . '/kategoris');
            $kategoris = $kategorisResponse->successful() ? ($kategorisResponse->json()['kategoris'] ?? []) : [];
            
            // Filter berdasarkan kategori jika ada
            if ($kategoriNama) {
                $items = collect($items)->filter(function($item) use ($kategoriNama) {
                    return isset($item['kategori']['nama_kategori']) && 
                           $item['kategori']['nama_kategori'] === $kategoriNama;
                })->values()->toArray();
            }
            
            // Terapkan pengurutan
            $sortedItems = $this->applySorting($items, $sortBy);
            
            return view('user.produk-all', [
                'items' => $sortedItems,
                'kategoris' => $kategoris,
                'kategoriNama' => $kategoriNama,
                'currentSort' => $sortBy
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error loading products: ' . $e->getMessage());
            return view('user.produk-all', [
                'items' => [],
                'kategoris' => [],
                'kategoriNama' => null,
                'currentSort' => 'terbaru',
                'error' => 'Terjadi kesalahan saat memuat produk'
            ]);
        }
    }

    /**
     * Menerapkan pengurutan pada array produk
     */
    private function applySorting($items, $sortBy)
    {
        $collection = collect($items);
        
        switch ($sortBy) {
            case 'harga_rendah':
                return $collection->sortBy('harga_dasar')->values()->toArray();
                
            case 'harga_tinggi':
                return $collection->sortByDesc('harga_dasar')->values()->toArray();
                
            case 'terlaris':
                // Asumsi ada field 'total_sold' atau 'popularity_score'
                return $collection->sortByDesc(function($item) {
                    return $item['total_sold'] ?? $item['popularity_score'] ?? 0;
                })->values()->toArray();
                
            case 'nama_az':
                return $collection->sortBy('nama_item')->values()->toArray();
                
            case 'nama_za':
                return $collection->sortByDesc('nama_item')->values()->toArray();
                
            case 'terbaru':
            default:
                // Asumsi ada field 'created_at' atau 'id' untuk menentukan yang terbaru
                return $collection->sortByDesc(function($item) {
                    return $item['created_at'] ?? $item['id'] ?? 0;
                })->values()->toArray();
        }
    }

    /**
     * Menampilkan detail produk dengan pilihan customization
     */
    public function show($id)
    {
        try {
            // Ambil detail item dari API
            $itemResponse = Http::timeout(10)->get($this->apiBaseUrl . "/items/{$id}");
            
            if (!$itemResponse->successful()) {
                return redirect()->route('produk-all')->with('error', 'Produk tidak ditemukan');
            }
            
            $item = $itemResponse->json()['item'] ?? null;
            
            if (!$item) {
                return redirect()->route('produk-all')->with('error', 'Produk tidak ditemukan');
            }

            // Ambil data bahan, jenis, ukuran (kode yang sudah ada)
            $bahansResponse = Http::timeout(10)->get($this->apiBaseUrl . '/bahans');
            $allBahans = $bahansResponse->successful() ? ($bahansResponse->json()['bahans'] ?? []) : [];
            
            $availableBahans = collect($allBahans)->filter(function($bahan) use ($item) {
                $itemIds = collect($bahan['items'] ?? [])->pluck('id')->toArray();
                return in_array($item['id'], $itemIds);
            })->values()->toArray();

            $jenisResponse = Http::timeout(10)->get($this->apiBaseUrl . '/jenis');
            $allJenis = $jenisResponse->successful() ? ($jenisResponse->json()['jenis'] ?? []) : [];
            
            $availableJenis = collect($allJenis)->filter(function($jenis) use ($item) {
                $itemIds = collect($jenis['items'] ?? [])->pluck('id')->toArray();
                return in_array($item['id'], $itemIds);
            })->values()->toArray();

            $ukuransResponse = Http::timeout(10)->get($this->apiBaseUrl . '/ukurans');
            $allUkurans = $ukuransResponse->successful() ? ($ukuransResponse->json()['ukurans'] ?? []) : [];
            
            $availableUkurans = collect($allUkurans)->filter(function($ukuran) use ($item) {
                $itemIds = collect($ukuran['items'] ?? [])->pluck('id')->toArray();
                return in_array($item['id'], $itemIds);
            })->values()->toArray();

            // TAMBAHAN: Ambil biaya desain dari API
            $biayaDesainResponse = Http::timeout(10)->get($this->apiBaseUrl . '/biaya-desains');
            $biayaDesain = 0;
            
            if ($biayaDesainResponse->successful()) {
                $biayaDesains = $biayaDesainResponse->json()['biaya_desains'] ?? [];
                if (!empty($biayaDesains)) {
                    // Ambil biaya desain pertama (atau bisa disesuaikan logic-nya)
                    $biayaDesain = $biayaDesains[0]['biaya'] ?? 0;
                }
            }

            // Cek apakah pengguna sudah login
            $user = session()->has('user') ? session('user') : null;
            
            // Ambil ulasan untuk produk ini
            $reviews = DetailPesanan::with('pesanan.user')
            ->whereHas('custom', function ($query) use ($id) {
            $query->where('item_id', $id); // sesuaikan jika nama field berbeda
            })
            ->whereNotNull('rating')
            ->whereNotNull('komentar')
            ->orderByDesc('reviewed_at')
            ->take(10)
            ->get();

            return view('user.product-detail', [
                'item' => $item,
                'bahans' => $availableBahans,
                'jenis' => $availableJenis,
                'ukurans' => $availableUkurans,
                'biaya_desain' => $biayaDesain,
                'user' => $user,
                'reviews' => $reviews,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error showing product detail: ' . $e->getMessage());
            return redirect()->route('produk-all')->with('error', 'Terjadi kesalahan saat memuat detail produk');
        }

        
    }
}