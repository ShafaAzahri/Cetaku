<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    protected $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = rtrim(env('API_URL', config('app.url')), '/');
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
                return redirect()->route('welcome')->with('error', 'Produk tidak ditemukan');
            }
            
            $item = $itemResponse->json()['item'] ?? null;
            
            if (!$item) {
                return redirect()->route('welcome')->with('error', 'Produk tidak ditemukan');
            }

            // Ambil data bahan yang tersedia untuk item ini
            $bahansResponse = Http::timeout(10)->get($this->apiBaseUrl . '/bahans');
            $allBahans = $bahansResponse->successful() ? ($bahansResponse->json()['bahans'] ?? []) : [];
            
            // Filter bahan yang terkait dengan item ini
            $availableBahans = collect($allBahans)->filter(function($bahan) use ($item) {
                $itemIds = collect($bahan['items'] ?? [])->pluck('id')->toArray();
                return in_array($item['id'], $itemIds);
            })->values()->toArray();

            // Ambil data jenis yang tersedia untuk item ini
            $jenisResponse = Http::timeout(10)->get($this->apiBaseUrl . '/jenis');
            $allJenis = $jenisResponse->successful() ? ($jenisResponse->json()['jenis'] ?? []) : [];
            
            // Filter jenis yang terkait dengan item ini
            $availableJenis = collect($allJenis)->filter(function($jenis) use ($item) {
                $itemIds = collect($jenis['items'] ?? [])->pluck('id')->toArray();
                return in_array($item['id'], $itemIds);
            })->values()->toArray();

            // Ambil data ukuran yang tersedia untuk item ini
            $ukuransResponse = Http::timeout(10)->get($this->apiBaseUrl . '/ukurans');
            $allUkurans = $ukuransResponse->successful() ? ($ukuransResponse->json()['ukurans'] ?? []) : [];
            
            // Filter ukuran yang terkait dengan item ini
            $availableUkurans = collect($allUkurans)->filter(function($ukuran) use ($item) {
                $itemIds = collect($ukuran['items'] ?? [])->pluck('id')->toArray();
                return in_array($item['id'], $itemIds);
            })->values()->toArray();

            // Cek apakah pengguna sudah login
            $user = session()->has('user') ? session('user') : null;
            
            return view('user.product-detail', [
                'item' => $item,
                'bahans' => $availableBahans,
                'jenis' => $availableJenis,
                'ukurans' => $availableUkurans,
                'user' => $user
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error showing product detail: ' . $e->getMessage());
            return redirect()->route('welcome')->with('error', 'Terjadi kesalahan saat memuat detail produk');
        }
    }
}