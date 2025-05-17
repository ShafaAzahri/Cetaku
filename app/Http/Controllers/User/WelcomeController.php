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
            return view('welcome', ['user' => $user, 'terlaris' => $terlaris]);
        }
        
        // Pengguna belum login
        return view('welcome', ['terlaris' => $terlaris]);
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
}