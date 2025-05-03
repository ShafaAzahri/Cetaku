<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\API\ItemApiController;
use App\Http\Controllers\API\BahanApiController;
use App\Http\Controllers\API\JenisApiController;
use App\Http\Controllers\API\UkuranApiController;
use App\Http\Controllers\API\BiayaDesainApiController;
use Illuminate\Support\Facades\DB;

class ProductManagerController extends Controller
{
    protected $apiBaseUrl;
    protected $itemApiController;
    protected $bahanApiController;
    protected $jenisApiController;
    protected $ukuranApiController;
    protected $biayaDesainApiController;

    public function __construct()
    {
        // Gunakan API_URL dari .env, bukan app.url
        $this->apiBaseUrl = rtrim(env('API_URL', 'http://127.0.0.1:8001'), '/');
        
        // Inisialisasi API controllers
        $this->itemApiController = new ItemApiController();
        $this->bahanApiController = new BahanApiController();
        $this->jenisApiController = new JenisApiController();
        $this->ukuranApiController = new UkuranApiController();
        $this->biayaDesainApiController = new BiayaDesainApiController();
        
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
        $data = [];
        
        try {
            // Selalu ambil data items untuk dropdown di modal
            $responseItems = $this->itemApiController->index();
            $dataItems = $responseItems->getData(true);
            $data['items'] = $dataItems['items'] ?? [];
            
            // Ambil data sesuai tab yang aktif
            switch ($activeTab) {
                case 'items':
                    // Data items sudah diambil di atas
                    break;
                    
                case 'bahan':
                    $responseBahan = $this->bahanApiController->index();
                    $dataBahan = $responseBahan->getData(true);
                    $data['bahans'] = $dataBahan['bahans'] ?? [];
                    
                    Log::debug('Data bahan diambil', [
                        'count' => count($data['bahans'] ?? [])
                    ]);
                    break;
                    
                case 'jenis':
                    $responseJenis = $this->jenisApiController->index();
                    $dataJenis = $responseJenis->getData(true);
                    $data['jenis_list'] = $dataJenis['jenis'] ?? [];
                    
                    Log::debug('Data jenis diambil', [
                        'count' => count($data['jenis_list'] ?? [])
                    ]);
                    break;
                    
                case 'ukuran':
                    $responseUkuran = $this->ukuranApiController->index();
                    $dataUkuran = $responseUkuran->getData(true);
                    $data['ukurans'] = $dataUkuran['ukurans'] ?? [];
                    
                    Log::debug('Data ukuran diambil', [
                        'count' => count($data['ukurans'] ?? [])
                    ]);
                    break;
                    
                case 'biaya-desain':
                    $responseBiayaDesain = $this->biayaDesainApiController->index();
                    $dataBiayaDesain = $responseBiayaDesain->getData(true);
                    $data['biaya_desains'] = $dataBiayaDesain['biaya_desains'] ?? [];
                    
                    Log::debug('Data biaya desain diambil', [
                        'count' => count($data['biaya_desains'] ?? [])
                    ]);
                    break;
            }
            
            Log::info('Berhasil mengambil data untuk tab ' . $activeTab);
        } catch (\Exception $e) {
            Log::error('Error mengambil data: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
        }
        
        // Tambahkan activeTab ke data
        $data['activeTab'] = $activeTab;
        
        return view('admin.product-manager', $data);
    }
    
    /**
     * Item Methods
     */
    
    /**
     * Store a newly created item
     */
    public function storeItem(Request $request)
    {
        try {
            Log::debug('Attempting to store item via API controller', [
                'data' => $request->except('gambar'),
                'has_file' => $request->hasFile('gambar')
            ]);
            
            // Panggil method store dari API controller
            $response = $this->itemApiController->store($request);
            
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
        try {
            Log::debug('Attempting to update item via API controller', [
                'item_id' => $id,
                'data' => $request->except('gambar'),
                'has_file' => $request->hasFile('gambar')
            ]);
            
            // Panggil method update dari API controller
            $response = $this->itemApiController->update($request, $id);
            
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
            
            // Panggil method destroy dari API controller
            $response = $this->itemApiController->destroy($id);
            
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
    
    /**
     * Bahan Methods
     */
    
    /**
     * Store a newly created bahan
     */
    public function storeBahan(Request $request)
    {
        try {
            Log::debug('Attempting to store bahan via API controller', [
                'data' => $request->all()
            ]);
            
            // Panggil method store dari API controller
            $response = $this->bahanApiController->store($request);
            
            // Ambil data dari response JSON
            $data = $response->getData(true);
            
            if ($data['success'] ?? false) {
                Log::info('Bahan berhasil disimpan via API controller', ['bahan' => $data['bahan'] ?? []]);
                return redirect()->route('admin.product-manager', ['tab' => 'bahan'])
                    ->with('success', 'Bahan berhasil ditambahkan');
            } else {
                throw new \Exception('API error: ' . ($data['message'] ?? 'Unknown error'));
            }
        } catch (\Exception $e) {
            Log::error('Error menyimpan bahan: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Update the specified bahan
     */
    public function updateBahan(Request $request, $id)
    {
        try {
            Log::debug('Attempting to update bahan via API controller', [
                'bahan_id' => $id,
                'data' => $request->all()
            ]);
            
            // Panggil method update dari API controller
            $response = $this->bahanApiController->update($request, $id);
            
            // Ambil data dari response JSON
            $data = $response->getData(true);
            
            if ($data['success'] ?? false) {
                Log::info('Bahan berhasil diperbarui via API controller', ['bahan_id' => $id]);
                return redirect()->route('admin.product-manager', ['tab' => 'bahan'])
                    ->with('success', 'Bahan berhasil diperbarui');
            } else {
                throw new \Exception('API error: ' . ($data['message'] ?? 'Unknown error'));
            }
        } catch (\Exception $e) {
            Log::error('Error memperbarui bahan: ' . $e->getMessage(), [
                'bahan_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified bahan
     */
    public function destroyBahan($id)
    {
        try {
            Log::debug('Attempting to delete bahan via API controller', [
                'bahan_id' => $id
            ]);
            
            // Panggil method destroy dari API controller
            $response = $this->bahanApiController->destroy($id);
            
            // Ambil data dari response JSON
            $data = $response->getData(true);
            
            if ($data['success'] ?? false) {
                Log::info('Bahan berhasil dihapus via API controller', ['bahan_id' => $id]);
                return redirect()->route('admin.product-manager', ['tab' => 'bahan'])
                    ->with('success', 'Bahan berhasil dihapus');
            } else {
                throw new \Exception('API error: ' . ($data['message'] ?? 'Unknown error'));
            }
        } catch (\Exception $e) {
            Log::error('Error menghapus bahan: ' . $e->getMessage(), [
                'bahan_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    /**
     * Jenis Methods
     */
    
    /**
     * Store a newly created jenis
     */
    public function storeJenis(Request $request)
    {
        try {
            Log::debug('Attempting to store jenis via API controller', [
                'data' => $request->all()
            ]);
            
            // Panggil method store dari API controller
            $response = $this->jenisApiController->store($request);
            
            // Ambil data dari response JSON
            $data = $response->getData(true);
            
            if ($data['success'] ?? false) {
                Log::info('Jenis berhasil disimpan via API controller', ['jenis' => $data['jenis'] ?? []]);
                return redirect()->route('admin.product-manager', ['tab' => 'jenis'])
                    ->with('success', 'Jenis berhasil ditambahkan');
            } else {
                throw new \Exception('API error: ' . ($data['message'] ?? 'Unknown error'));
            }
        } catch (\Exception $e) {
            Log::error('Error menyimpan jenis: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Update the specified jenis
     */
    public function updateJenis(Request $request, $id)
    {
        try {
            Log::debug('Attempting to update jenis via API controller', [
                'jenis_id' => $id,
                'data' => $request->all()
            ]);
            
            // Panggil method update dari API controller
            $response = $this->jenisApiController->update($request, $id);
            
            // Ambil data dari response JSON
            $data = $response->getData(true);
            
            if ($data['success'] ?? false) {
                Log::info('Jenis berhasil diperbarui via API controller', ['jenis_id' => $id]);
                return redirect()->route('admin.product-manager', ['tab' => 'jenis'])
                    ->with('success', 'Jenis berhasil diperbarui');
            } else {
                throw new \Exception('API error: ' . ($data['message'] ?? 'Unknown error'));
            }
        } catch (\Exception $e) {
            Log::error('Error memperbarui jenis: ' . $e->getMessage(), [
                'jenis_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified jenis
     */
    public function destroyJenis($id)
    {
        try {
            Log::debug('Attempting to delete jenis via API controller', [
                'jenis_id' => $id
            ]);
            
            // Panggil method destroy dari API controller
            $response = $this->jenisApiController->destroy($id);
            
            // Ambil data dari response JSON
            $data = $response->getData(true);
            
            if ($data['success'] ?? false) {
                Log::info('Jenis berhasil dihapus via API controller', ['jenis_id' => $id]);
                return redirect()->route('admin.product-manager', ['tab' => 'jenis'])
                    ->with('success', 'Jenis berhasil dihapus');
            } else {
                throw new \Exception('API error: ' . ($data['message'] ?? 'Unknown error'));
            }
        } catch (\Exception $e) {
            Log::error('Error menghapus jenis: ' . $e->getMessage(), [
                'jenis_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    /**
     * Ukuran Methods
     */
    
    /**
     * Store a newly created ukuran
     */
    public function storeUkuran(Request $request)
    {
        try {
            Log::debug('Attempting to store ukuran via API controller', [
                'data' => $request->all()
            ]);
            
            // Panggil method store dari API controller
            $response = $this->ukuranApiController->store($request);
            
            // Ambil data dari response JSON
            $data = $response->getData(true);
            
            if ($data['success'] ?? false) {
                Log::info('Ukuran berhasil disimpan via API controller', ['ukuran' => $data['ukuran'] ?? []]);
                return redirect()->route('admin.product-manager', ['tab' => 'ukuran'])
                    ->with('success', 'Ukuran berhasil ditambahkan');
            } else {
                throw new \Exception('API error: ' . ($data['message'] ?? 'Unknown error'));
            }
        } catch (\Exception $e) {
            Log::error('Error menyimpan ukuran: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Update the specified ukuran
     */
    public function updateUkuran(Request $request, $id)
    {
        try {
            Log::debug('Attempting to update ukuran via API controller', [
                'ukuran_id' => $id,
                'data' => $request->all()
            ]);
            
            // Panggil method update dari API controller
            $response = $this->ukuranApiController->update($request, $id);
            
            // Ambil data dari response JSON
            $data = $response->getData(true);
            
            if ($data['success'] ?? false) {
                Log::info('Ukuran berhasil diperbarui via API controller', ['ukuran_id' => $id]);
                return redirect()->route('admin.product-manager', ['tab' => 'ukuran'])
                    ->with('success', 'Ukuran berhasil diperbarui');
            } else {
                throw new \Exception('API error: ' . ($data['message'] ?? 'Unknown error'));
            }
        } catch (\Exception $e) {
            Log::error('Error memperbarui ukuran: ' . $e->getMessage(), [
                'ukuran_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified ukuran
     */
    public function destroyUkuran($id)
    {
        try {
            Log::debug('Attempting to delete ukuran via API controller', [
                'ukuran_id' => $id
            ]);
            
            // Panggil method destroy dari API controller
            $response = $this->ukuranApiController->destroy($id);
            
            // Ambil data dari response JSON
            $data = $response->getData(true);
            
            if ($data['success'] ?? false) {
                Log::info('Ukuran berhasil dihapus via API controller', ['ukuran_id' => $id]);
                return redirect()->route('admin.product-manager', ['tab' => 'ukuran'])
                    ->with('success', 'Ukuran berhasil dihapus');
            } else {
                throw new \Exception('API error: ' . ($data['message'] ?? 'Unknown error'));
            }
        } catch (\Exception $e) {
            Log::error('Error menghapus ukuran: ' . $e->getMessage(), [
                'ukuran_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    /**
     * Biaya Desain Methods
     */
    
    /**
     * Store a newly created biaya desain
     */
    public function storeBiayaDesain(Request $request)
    {
        try {
            Log::debug('Attempting to store biaya desain via API controller', [
                'data' => $request->all()
            ]);
            
            // Panggil method store dari API controller
            $response = $this->biayaDesainApiController->store($request);
            
            // Ambil data dari response JSON
            $data = $response->getData(true);
            
            if ($data['success'] ?? false) {
                Log::info('Biaya desain berhasil disimpan via API controller', ['biaya_desain' => $data['biaya_desain'] ?? []]);
                return redirect()->route('admin.product-manager', ['tab' => 'biaya-desain'])
                    ->with('success', 'Biaya desain berhasil ditambahkan');
            } else {
                throw new \Exception('API error: ' . ($data['message'] ?? 'Unknown error'));
            }
        } catch (\Exception $e) {
            Log::error('Error menyimpan biaya desain: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Update the specified biaya desain
     */
    public function updateBiayaDesain(Request $request, $id)
    {
        try {
            Log::debug('Attempting to update biaya desain via API controller', [
                'biaya_desain_id' => $id,
                'data' => $request->all()
            ]);
            
            // Panggil method update dari API controller
            $response = $this->biayaDesainApiController->update($request, $id);
            
            // Ambil data dari response JSON
            $data = $response->getData(true);
            
            if ($data['success'] ?? false) {
                Log::info('Biaya desain berhasil diperbarui via API controller', ['biaya_desain_id' => $id]);
                return redirect()->route('admin.product-manager', ['tab' => 'biaya-desain'])
                    ->with('success', 'Biaya desain berhasil diperbarui');
            } else {
                throw new \Exception('API error: ' . ($data['message'] ?? 'Unknown error'));
            }
        } catch (\Exception $e) {
            Log::error('Error memperbarui biaya desain: ' . $e->getMessage(), [
                'biaya_desain_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified biaya desain
     */
    public function destroyBiayaDesain($id)
    {
        try {
            Log::debug('Attempting to delete biaya desain via API controller', [
                'biaya_desain_id' => $id
            ]);
            
            // Panggil method destroy dari API controller
            $response = $this->biayaDesainApiController->destroy($id);
            
            // Ambil data dari response JSON
            $data = $response->getData(true);
            
            if ($data['success'] ?? false) {
                Log::info('Biaya desain berhasil dihapus via API controller', ['biaya_desain_id' => $id]);
                return redirect()->route('admin.product-manager', ['tab' => 'biaya-desain'])
                    ->with('success', 'Biaya desain berhasil dihapus');
            } else {
                throw new \Exception('API error: ' . ($data['message'] ?? 'Unknown error'));
            }
        } catch (\Exception $e) {
            Log::error('Error menghapus biaya desain: ' . $e->getMessage(), [
                'biaya_desain_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}