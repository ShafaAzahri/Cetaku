<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProductManagerController extends Controller
{
    protected $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = rtrim(env('API_URL', config('app.url')), '/');
    }
    
    /**
     * Tampilkan halaman product manager
     */
    public function index()
    {
        $activeTab = request('tab', 'items');
        $data = ['activeTab' => $activeTab];
        
        // Selalu ambil data items untuk dropdown di modal
        $data['items'] = $this->fetchData('/items');
        
        // Ambil data sesuai tab yang aktif
        switch ($activeTab) {
            case 'bahan':
                $data['bahans'] = $this->fetchData('/bahans');
                break;
                
            case 'jenis':
                $data['jenis_list'] = $this->fetchData('/jenis');
                break;
                
            case 'ukuran':
                $data['ukurans'] = $this->fetchData('/ukurans');
                break;
                
            case 'biaya-desain':
                $data['biaya_desains'] = $this->fetchData('/biaya-desains');
                break;
        }
        
        return view('admin.product-manager', $data);
    }
    
    /**
     * Fetch data from API
     */
    private function fetchData($endpoint)
    {
        try {
            $token = session('api_token');
            $response = Http::withToken($token)->get($this->apiBaseUrl . $endpoint);
            
            if ($response->successful()) {
                $data = $response->json();
                $key = basename($endpoint);
                return $data[$key] ?? [];
            }
            
            return [];
        } catch (\Exception $e) {
            return [];
        }
    }
    
    /**
     * Send API request with authentication
     */
    private function sendApiRequest($method, $endpoint, Request $request = null, $hasFile = false)
    {
        $token = session('api_token');
        $httpRequest = Http::withToken($token);
        
        if ($hasFile && $request && $request->hasFile('gambar')) {
            $response = $httpRequest->attach(
                'gambar', 
                $request->file('gambar')->getContent(),
                $request->file('gambar')->getClientOriginalName()
            )->$method($this->apiBaseUrl . $endpoint, $request->except('gambar'));
        } else {
            $data = $request ? $request->all() : [];
            $response = $httpRequest->$method($this->apiBaseUrl . $endpoint, $data);
        }
        
        return $response->json();
    }
    
    /**
     * Item Methods
     */
    
    /**
     * Store a newly created item
     */
    public function storeItem(Request $request)
    {
        $response = $this->sendApiRequest('post', '/items', $request, true);
        
        if ($response['success'] ?? false) {
            return redirect()->route('admin.product-manager', ['tab' => 'items'])
                ->with('success', 'Item berhasil ditambahkan');
        }
        
        return redirect()->back()
            ->with('error', $response['message'] ?? 'Terjadi kesalahan')
            ->withInput();
    }

    /**
     * Update the specified item
     */
    public function updateItem(Request $request, $id)
    {
        $response = $this->sendApiRequest('put', "/items/{$id}", $request, true);
        
        if ($response['success'] ?? false) {
            return redirect()->route('admin.product-manager', ['tab' => 'items'])
                ->with('success', 'Item berhasil diperbarui');
        }
        
        return redirect()->back()
            ->with('error', $response['message'] ?? 'Terjadi kesalahan')
            ->withInput();
    }

    /**
     * Remove the specified item
     */
    public function destroyItem($id)
    {
        $response = $this->sendApiRequest('delete', "/items/{$id}");
        
        if ($response['success'] ?? false) {
            return redirect()->route('admin.product-manager', ['tab' => 'items'])
                ->with('success', 'Item berhasil dihapus');
        }
        
        return redirect()->back()
            ->with('error', $response['message'] ?? 'Terjadi kesalahan');
    }
    
    /**
     * Bahan Methods
     */
    
    /**
     * Store a newly created bahan
     */
    public function storeBahan(Request $request)
    {
        $response = $this->sendApiRequest('post', '/bahans', $request);
        
        if ($response['success'] ?? false) {
            return redirect()->route('admin.product-manager', ['tab' => 'bahan'])
                ->with('success', 'Bahan berhasil ditambahkan');
        }
        
        return redirect()->back()
            ->with('error', $response['message'] ?? 'Terjadi kesalahan')
            ->withInput();
    }

    /**
     * Update the specified bahan
     */
    public function updateBahan(Request $request, $id)
    {
        $response = $this->sendApiRequest('put', "/bahans/{$id}", $request);
        
        if ($response['success'] ?? false) {
            return redirect()->route('admin.product-manager', ['tab' => 'bahan'])
                ->with('success', 'Bahan berhasil diperbarui');
        }
        
        return redirect()->back()
            ->with('error', $response['message'] ?? 'Terjadi kesalahan')
            ->withInput();
    }

    /**
     * Remove the specified bahan
     */
    public function destroyBahan($id)
    {
        $response = $this->sendApiRequest('delete', "/bahans/{$id}");
        
        if ($response['success'] ?? false) {
            return redirect()->route('admin.product-manager', ['tab' => 'bahan'])
                ->with('success', 'Bahan berhasil dihapus');
        }
        
        return redirect()->back()
            ->with('error', $response['message'] ?? 'Terjadi kesalahan');
    }
    
    /**
     * Jenis Methods
     */
    
    /**
     * Store a newly created jenis
     */
    public function storeJenis(Request $request)
    {
        $response = $this->sendApiRequest('post', '/jenis', $request);
        
        if ($response['success'] ?? false) {
            return redirect()->route('admin.product-manager', ['tab' => 'jenis'])
                ->with('success', 'Jenis berhasil ditambahkan');
        }
        
        return redirect()->back()
            ->with('error', $response['message'] ?? 'Terjadi kesalahan')
            ->withInput();
    }

    /**
     * Update the specified jenis
     */
    public function updateJenis(Request $request, $id)
    {
        $response = $this->sendApiRequest('put', "/jenis/{$id}", $request);
        
        if ($response['success'] ?? false) {
            return redirect()->route('admin.product-manager', ['tab' => 'jenis'])
                ->with('success', 'Jenis berhasil diperbarui');
        }
        
        return redirect()->back()
            ->with('error', $response['message'] ?? 'Terjadi kesalahan')
            ->withInput();
    }

    /**
     * Remove the specified jenis
     */
    public function destroyJenis($id)
    {
        $response = $this->sendApiRequest('delete', "/jenis/{$id}");
        
        if ($response['success'] ?? false) {
            return redirect()->route('admin.product-manager', ['tab' => 'jenis'])
                ->with('success', 'Jenis berhasil dihapus');
        }
        
        return redirect()->back()
            ->with('error', $response['message'] ?? 'Terjadi kesalahan');
    }
    
    /**
     * Ukuran Methods
     */
    
    /**
     * Store a newly created ukuran
     */
    public function storeUkuran(Request $request)
    {
        $response = $this->sendApiRequest('post', '/ukurans', $request);
        
        if ($response['success'] ?? false) {
            return redirect()->route('admin.product-manager', ['tab' => 'ukuran'])
                ->with('success', 'Ukuran berhasil ditambahkan');
        }
        
        return redirect()->back()
            ->with('error', $response['message'] ?? 'Terjadi kesalahan')
            ->withInput();
    }

    /**
     * Update the specified ukuran
     */
    public function updateUkuran(Request $request, $id)
    {
        $response = $this->sendApiRequest('put', "/ukurans/{$id}", $request);
        
        if ($response['success'] ?? false) {
            return redirect()->route('admin.product-manager', ['tab' => 'ukuran'])
                ->with('success', 'Ukuran berhasil diperbarui');
        }
        
        return redirect()->back()
            ->with('error', $response['message'] ?? 'Terjadi kesalahan')
            ->withInput();
    }

    /**
     * Remove the specified ukuran
     */
    public function destroyUkuran($id)
    {
        $response = $this->sendApiRequest('delete', "/ukurans/{$id}");
        
        if ($response['success'] ?? false) {
            return redirect()->route('admin.product-manager', ['tab' => 'ukuran'])
                ->with('success', 'Ukuran berhasil dihapus');
        }
        
        return redirect()->back()
            ->with('error', $response['message'] ?? 'Terjadi kesalahan');
    }
    
    /**
     * Biaya Desain Methods
     */
    
    /**
     * Store a newly created biaya desain
     */
    public function storeBiayaDesain(Request $request)
    {
        $response = $this->sendApiRequest('post', '/biaya-desains', $request);
        
        if ($response['success'] ?? false) {
            return redirect()->route('admin.product-manager', ['tab' => 'biaya-desain'])
                ->with('success', 'Biaya desain berhasil ditambahkan');
        }
        
        return redirect()->back()
            ->with('error', $response['message'] ?? 'Terjadi kesalahan')
            ->withInput();
    }

    /**
     * Update the specified biaya desain
     */
    public function updateBiayaDesain(Request $request, $id)
    {
        $response = $this->sendApiRequest('put', "/biaya-desains/{$id}", $request);
        
        if ($response['success'] ?? false) {
            return redirect()->route('admin.product-manager', ['tab' => 'biaya-desain'])
                ->with('success', 'Biaya desain berhasil diperbarui');
        }
        
        return redirect()->back()
            ->with('error', $response['message'] ?? 'Terjadi kesalahan')
            ->withInput();
    }

    /**
     * Remove the specified biaya desain
     */
    public function destroyBiayaDesain($id)
    {
        $response = $this->sendApiRequest('delete', "/biaya-desains/{$id}");
        
        if ($response['success'] ?? false) {
            return redirect()->route('admin.product-manager', ['tab' => 'biaya-desain'])
                ->with('success', 'Biaya desain berhasil dihapus');
        }
        
        return redirect()->back()
            ->with('error', $response['message'] ?? 'Terjadi kesalahan');
    }
}