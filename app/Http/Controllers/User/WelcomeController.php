<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class WelcomeController extends Controller
{   
    protected $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = rtrim(env('API_URL', config('app.url')), '/');
    }

    /**
     * Menampilkan halaman welcome (landing page)
     */
    public function index()
    {
        // Ambil item terlaris dari API
        $terlaris = $this->fetchTerlaris();
        
        // Ambil kategori dari API
        $kategoris = $this->fetchKategoris();
        
        // Cek apakah pengguna sudah login
        if (session()->has('api_token') && session()->has('user')) {
            $user = session('user');
            $role = $user['role'] ?? 'user';
            
            // Redirect admin dan super_admin ke dashboard mereka
            if ($role === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($role === 'super_admin') {
                return redirect()->route('superadmin.dashboard');
            }
            
            // Untuk user biasa, tampilkan welcome page dengan data user
            return view('welcome', [
                'user' => $user, 
                'terlaris' => $terlaris,
                'kategoris' => $kategoris
            ]);
        }
        
        // Pengguna belum login
        return view('welcome', [
            'terlaris' => $terlaris,
            'kategoris' => $kategoris
        ]);
    }

    /**
     * Fetch item terlaris dari API
     */
    private function fetchTerlaris()
    {
        try {
            $response = Http::timeout(10)->get($this->apiBaseUrl . '/items', [
                'sort' => 'terlaris',
                'limit' => 4
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                return $data['items'] ?? [];
            }
            
            Log::warning('Failed to fetch terlaris items', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching terlaris items: ' . $e->getMessage());
        }
        
        return [];
    }

    /**
     * Fetch kategori dari API
     */
    private function fetchKategoris()
    {
        try {
            $response = Http::timeout(10)->get($this->apiBaseUrl . '/kategoris');
            
            if ($response->successful()) {
                $data = $response->json();
                
                // Ambil maksimal 5 kategori untuk ditampilkan
                $kategoris = collect($data['kategoris'] ?? [])->take(5);
                
                // Log untuk debugging
                Log::info('Categories fetched successfully', [
                    'count' => $kategoris->count(),
                    'categories' => $kategoris->pluck('nama_kategori')
                ]);
                
                return $kategoris->toArray();
            }
            
            Log::warning('Failed to fetch categories', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching categories: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
        }
        
        return [];
    }

    /**
     * Menampilkan halaman kategori dengan items
     */
    public function showKategori($id)
    {
        try {
            // Ambil detail kategori dan items di dalamnya
            $kategoriResponse = Http::timeout(10)->get($this->apiBaseUrl . "/kategoris/{$id}");
            $itemsResponse = Http::timeout(10)->get($this->apiBaseUrl . "/kategoris/{$id}/items");
            
            if (!$kategoriResponse->successful()) {
                return redirect()->route('welcome')->with('error', 'Kategori tidak ditemukan');
            }
            
            $kategori = $kategoriResponse->json()['kategori'] ?? null;
            $items = $itemsResponse->successful() ? 
                ($itemsResponse->json()['items'] ?? []) : [];
            
            // Cek apakah pengguna sudah login untuk data user
            $user = session()->has('user') ? session('user') : null;
            
            return view('user.kategori.show', [
                'kategori' => $kategori,
                'items' => $items,
                'user' => $user
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error showing category: ' . $e->getMessage());
            return redirect()->route('welcome')->with('error', 'Terjadi kesalahan saat memuat kategori');
        }
    }

    /**
     * Menampilkan semua kategori
     */
    public function showAllKategoris()
    {
        try {
            $response = Http::timeout(10)->get($this->apiBaseUrl . '/kategoris');
            
            if ($response->successful()) {
                $data = $response->json();
                $kategoris = $data['kategoris'] ?? [];
                
                // Cek apakah pengguna sudah login
                $user = session()->has('user') ? session('user') : null;
                
                return view('user.kategori.index', [
                    'kategoris' => $kategoris,
                    'user' => $user
                ]);
            }
            
            return redirect()->route('welcome')->with('error', 'Gagal memuat daftar kategori');
            
        } catch (\Exception $e) {
            Log::error('Error showing all categories: ' . $e->getMessage());
            return redirect()->route('welcome')->with('error', 'Terjadi kesalahan saat memuat kategori');
        }
    }
}