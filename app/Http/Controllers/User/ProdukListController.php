<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProdukListController extends Controller
{
    protected $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = rtrim(env('API_URL', config('app.url')), '/');
    }

    public function index(Request $request)
    {
        $token = session('api_token'); // Ambil token dari session
        $kategoriNama = $request->query('kategori'); // Ambil filter kategori dari query

        // Ambil semua item dari API
        $itemsResponse = Http::withToken($token)->get($this->apiBaseUrl . '/items');
        $items = $itemsResponse->successful() ? ($itemsResponse->json('items') ?? []) : [];
        

        // Ambil semua kategori dari API dengan relasi items
        $kategoriResponse = Http::get($this->apiBaseUrl . '/kategoris');
        $kategoris = $kategoriResponse->successful() ? ($kategoriResponse->json('kategoris') ?? []) : [];

        // Filter item berdasarkan kategori jika ada filter ?kategori=
        if ($kategoriNama) {
            $kategori = collect($kategoris)->firstWhere('nama_kategori', $kategoriNama);

            if ($kategori) {
                // Gunakan endpoint khusus untuk mendapatkan items berdasarkan kategori
                $itemsByKategoriResponse = Http::withToken($token)->get($this->apiBaseUrl . "/kategoris/{$kategori['id']}/items");
                
                if ($itemsByKategoriResponse->successful()) {
                    $items = $itemsByKategoriResponse->json('items') ?? [];
                } else {
                    // Fallback: filter dari kategori yang sudah punya relasi items
                    $items = $kategori['items'] ?? [];
                }
            } else {
                $items = []; // Jika kategori tidak ditemukan
            }
        }

        // Debug: Tambahkan log untuk melihat struktur data (hapus setelah debugging)
        // \Log::info('Items structure:', ['items' => $items]);
        // \Log::info('Kategoris structure:', ['kategoris' => $kategoris]);

        // Kirim data ke view
        $sortBy = $request->query('sort', 'terbaru'); // ambil sort dari query
$items = $this->applySorting($items, $sortBy); // terapkan sorting
        return view('user.produk-all', [
    'items' => $items,
    'kategoris' => $kategoris,
    'kategoriNama' => $kategoriNama,
    'currentSort' => $sortBy
]);
    }

    /**
     * Filter items berdasarkan kategori ID dengan query database langsung
     * Method ini tidak lagi diperlukan karena kita menggunakan API endpoint
     */
    // private function filterItemsByKategori($items, $kategoriId)
    // {
    //     try {
    //         // Query langsung ke database untuk mendapatkan item_ids yang terkait dengan kategori
    //         $itemIds = \DB::table('kategori_items')
    //             ->where('kategori_id', $kategoriId)
    //             ->pluck('item_id')
    //             ->toArray();

    //         // Filter items berdasarkan ID yang ditemukan
    //         return collect($items)->filter(function ($item) use ($itemIds) {
    //             return in_array($item['id'], $itemIds);
    //         })->values()->toArray();
            
    //     } catch (\Exception $e) {
    //         \Log::error('Error filtering items by kategori: ' . $e->getMessage());
    //         return [];
    //     }
    // }

    private function applySorting($items, $sortBy)
{
    $collection = collect($items);

    switch ($sortBy) {
        case 'harga_rendah':
            return $collection->sortBy('harga_dasar')->values()->toArray();

        case 'harga_tinggi':
            return $collection->sortByDesc('harga_dasar')->values()->toArray();

        case 'terlaris':
            return $collection->sortByDesc(function ($item) {
                return $item['total_sold'] ?? 0;
            })->values()->toArray();

        case 'nama_az':
            return $collection->sortBy('nama_item')->values()->toArray();

        case 'nama_za':
            return $collection->sortByDesc('nama_item')->values()->toArray();

        case 'terbaru':
        default:
            return $collection->sortByDesc(function ($item) {
                return $item['created_at'] ?? $item['id'] ?? 0;
            })->values()->toArray();
    }
}

    public function show($id)
    {
        try {
            $itemResponse = Http::timeout(10)->get($this->apiBaseUrl . "/items/{$id}");

            if (!$itemResponse->successful()) {
                return redirect()->route('welcome')->with('error', 'Produk tidak ditemukan');
            }

            $item = $itemResponse->json()['item'] ?? null;

            if (!$item) {
                return redirect()->route('welcome')->with('error', 'Produk tidak ditemukan');
            }

            // Ambil data bahan, jenis, ukuran dari API
            $bahansResponse = Http::timeout(10)->get($this->apiBaseUrl . '/bahans');
            $allBahans = $bahansResponse->successful() ? ($bahansResponse->json()['bahans'] ?? []) : [];

            $availableBahans = collect($allBahans)->filter(function ($bahan) use ($item) {
                $itemIds = collect($bahan['items'] ?? [])->pluck('id')->toArray();
                return in_array($item['id'], $itemIds);
            })->values()->toArray();

            $jenisResponse = Http::timeout(10)->get($this->apiBaseUrl . '/jenis');
            $allJenis = $jenisResponse->successful() ? ($jenisResponse->json()['jenis'] ?? []) : [];

            $availableJenis = collect($allJenis)->filter(function ($jenis) use ($item) {
                $itemIds = collect($jenis['items'] ?? [])->pluck('id')->toArray();
                return in_array($item['id'], $itemIds);
            })->values()->toArray();

            $ukuransResponse = Http::timeout(10)->get($this->apiBaseUrl . '/ukurans');
            $allUkurans = $ukuransResponse->successful() ? ($ukuransResponse->json()['ukurans'] ?? []) : [];

            $availableUkurans = collect($allUkurans)->filter(function ($ukuran) use ($item) {
                $itemIds = collect($ukuran['items'] ?? [])->pluck('id')->toArray();
                return in_array($item['id'], $itemIds);
            })->values()->toArray();

            // Ambil biaya desain dari API
            $biayaDesainResponse = Http::timeout(10)->get($this->apiBaseUrl . '/biaya-desains');
            $biayaDesain = 0;

            if ($biayaDesainResponse->successful()) {
                $biayaDesains = $biayaDesainResponse->json()['biaya_desains'] ?? [];
                if (!empty($biayaDesains)) {
                    $biayaDesain = $biayaDesains[0]['biaya'] ?? 0;
                }
            }

            $user = session()->has('user') ? session('user') : null;

            return view('user.product-detail', [
                'item' => $item,
                'bahans' => $availableBahans,
                'jenis' => $availableJenis,
                'ukurans' => $availableUkurans,
                'biaya_desain' => $biayaDesain,
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return redirect()->route('welcome')->with('error', 'Terjadi kesalahan saat memuat detail produk');
        }
    }
}