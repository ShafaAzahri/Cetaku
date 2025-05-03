<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\API\BahanApiController;
use App\Http\Controllers\API\ItemApiController;

class BahanController extends Controller
{
    protected $apiController;

    public function __construct()
    {
        $this->apiController = new BahanApiController();
    }

    /**
     * Menampilkan daftar bahan (untuk tab bahan)
     */
    public function index()
    {
        try {
            Log::info('BahanController: Memulai pengambilan data bahan');
            
            // Panggil API controller untuk mendapatkan data bahan
            $response = $this->apiController->index();
            $data = $response->getData(true);
            
            // Pastikan kita menerima data yang diharapkan
            if (!isset($data['success']) || $data['success'] !== true) {
                throw new \Exception('API Error: ' . ($data['message'] ?? 'Terjadi kesalahan saat mengambil data bahan'));
            }
            
            $bahans = $data['bahans'] ?? [];
            
            // Ambil data item untuk formulir tambah/edit
            $apiItemController = new ItemApiController();
            $itemResponse = $apiItemController->index();
            $itemData = $itemResponse->getData(true);
            $items = $itemData['items'] ?? [];
            
            Log::info('BahanController: Berhasil mengambil data', [
                'bahan_count' => count($bahans),
                'item_count' => count($items)
            ]);
            
            // Jika request adalah ajax, kembalikan hanya data
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'bahans' => $bahans,
                    'items' => $items
                ]);
            }
            
            // Render view product-manager dengan tab bahan aktif
            return view('admin.product-manager', [
                'activeTab' => 'bahan',
                'bahans' => $bahans,
                'items' => $items
            ]);
        } catch (\Exception $e) {
            Log::error('BahanController Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('admin.product-manager')
                ->with('error', 'Terjadi kesalahan saat mengambil data bahan: ' . $e->getMessage());
        }
    }

    /**
     * Menyimpan bahan baru
     */
    public function store(Request $request)
    {
        try {
            Log::info('BahanController: Memulai penyimpanan bahan baru', $request->except('_token'));
            
            // Validasi request
            $validated = $request->validate([
                'nama_bahan' => 'required|string|max:255',
                'biaya_tambahan' => 'required|numeric|min:0',
                'item_ids' => 'nullable|array',
                'item_ids.*' => 'exists:items,id'
            ]);
            
            // Panggil API controller untuk menyimpan bahan
            $response = $this->apiController->store($request);
            $data = $response->getData(true);
            
            // Periksa keberhasilan
            if (!isset($data['success']) || $data['success'] !== true) {
                throw new \Exception('API Error: ' . ($data['message'] ?? 'Terjadi kesalahan saat menyimpan bahan'));
            }
            
            Log::info('BahanController: Bahan berhasil disimpan', [
                'bahan_id' => $data['bahan']['id'] ?? null,
                'nama_bahan' => $data['bahan']['nama_bahan'] ?? null
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Bahan berhasil ditambahkan',
                    'bahan' => $data['bahan'] ?? null
                ]);
            }
            
            return redirect()->route('admin.product-manager', ['tab' => 'bahan'])
                ->with('success', 'Bahan berhasil ditambahkan');
        } catch (\Exception $e) {
            Log::error('BahanController Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan bahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Menampilkan detail bahan
     */
    public function show($id)
    {
        try {
            Log::info('BahanController: Memulai pengambilan detail bahan', ['id' => $id]);
            
            // Panggil API controller untuk mendapatkan detail bahan
            $response = $this->apiController->show($id);
            $data = $response->getData(true);
            
            // Pastikan kita menerima data yang diharapkan
            if (!isset($data['success']) || $data['success'] !== true) {
                throw new \Exception('API Error: ' . ($data['message'] ?? 'Bahan tidak ditemukan'));
            }
            
            $bahan = $data['bahan'] ?? null;
            $items = $data['items'] ?? [];
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'bahan' => $bahan,
                    'items' => $items
                ]);
            }
            
            // Tampilkan detail bahan
            return view('admin.bahan.show', compact('bahan', 'items'));
        } catch (\Exception $e) {
            Log::error('BahanController Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('admin.product-manager', ['tab' => 'bahan'])
                ->with('error', 'Terjadi kesalahan saat mengambil detail bahan: ' . $e->getMessage());
        }
    }

    /**
     * Update bahan yang sudah ada
     */
    public function update(Request $request, $id)
    {
        try {
            Log::info('BahanController: Memulai update bahan', [
                'id' => $id,
                'data' => $request->except('_token', '_method')
            ]);
            
            // Validasi request
            $validated = $request->validate([
                'nama_bahan' => 'required|string|max:255',
                'biaya_tambahan' => 'required|numeric|min:0',
                'item_ids' => 'nullable|array',
                'item_ids.*' => 'exists:items,id'
            ]);
            
            // Panggil API controller untuk update bahan
            $response = $this->apiController->update($request, $id);
            $data = $response->getData(true);
            
            // Periksa keberhasilan
            if (!isset($data['success']) || $data['success'] !== true) {
                throw new \Exception('API Error: ' . ($data['message'] ?? 'Terjadi kesalahan saat memperbarui bahan'));
            }
            
            Log::info('BahanController: Bahan berhasil diperbarui', [
                'bahan_id' => $id
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Bahan berhasil diperbarui',
                    'bahan' => $data['bahan'] ?? null
                ]);
            }
            
            return redirect()->route('admin.product-manager', ['tab' => 'bahan'])
                ->with('success', 'Bahan berhasil diperbarui');
        } catch (\Exception $e) {
            Log::error('BahanController Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memperbarui bahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Hapus bahan
     */
    public function destroy($id)
    {
        try {
            Log::info('BahanController: Memulai penghapusan bahan', ['id' => $id]);
            
            // Panggil API controller untuk hapus bahan
            $response = $this->apiController->destroy($id);
            $data = $response->getData(true);
            
            // Periksa keberhasilan
            if (!isset($data['success']) || $data['success'] !== true) {
                throw new \Exception('API Error: ' . ($data['message'] ?? 'Terjadi kesalahan saat menghapus bahan'));
            }
            
            Log::info('BahanController: Bahan berhasil dihapus', [
                'bahan_id' => $id
            ]);
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Bahan berhasil dihapus'
                ]);
            }
            
            return redirect()->route('admin.product-manager', ['tab' => 'bahan'])
                ->with('success', 'Bahan berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('BahanController Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('admin.product-manager', ['tab' => 'bahan'])
                ->with('error', 'Terjadi kesalahan saat menghapus bahan: ' . $e->getMessage());
        }
    }
}