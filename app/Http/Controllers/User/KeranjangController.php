<?php

namespace App\Http\Controllers\User;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class KeranjangController extends Controller
{
    protected $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = rtrim(env('API_URL', config('app.url')), '/');
    }

    /**
     * Menampilkan halaman keranjang
     */
    public function index()
    {
        try {
            $token = session('api_token');
            if (!$token) {
                return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
            }

            // Ambil data keranjang dari API
            $response = Http::withToken($token)->get($this->apiBaseUrl . '/keranjang');

            if (!$response->successful()) {
                return view('user.keranjang', [
                    'keranjangItems' => [],
                    'groupedItems' => [],
                    'summary' => [
                        'total_items' => 0,
                        'total_harga_produk' => 0,
                        'biaya_desain' => 0,
                        'total_harga' => 0,
                        'count_products' => 0,
                    ]
                ]);
            }

            $data = $response->json();
            $keranjangItems = $data['data']['items'] ?? [];
            $groupedItems = $data['data']['grouped_items'] ?? [];
            $summary = $data['data']['summary'] ?? [
                'total_items' => 0,
                'total_harga_produk' => 0,
                'biaya_desain' => 0,
                'total_harga' => 0,
                'count_products' => 0,
            ];

            // Log untuk debugging
            Log::info('Data keranjang dari API:', [
                'jumlah_items' => count($keranjangItems),
                'summary' => $summary
            ]);

            return view('user.keranjang', compact('keranjangItems', 'groupedItems', 'summary'));

        } catch (\Exception $e) {
            Log::error('Error loading cart: ' . $e->getMessage());
            return view('user.keranjang')->with('error', 'Terjadi kesalahan saat memuat keranjang');
        }
    }

    /**
     * Menambah produk ke keranjang via AJAX
     */
    public function addToCart(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'item_id' => 'required|integer',
                'ukuran_id' => 'required|integer',
                'bahan_id' => 'required|integer',
                'jenis_id' => 'required|integer',
                'tipe_desain' => 'required|in:sendiri,dibuatkan',
                'jumlah' => 'required|integer|min:1|max:100',
                'upload_desain' => 'nullable|file|mimes:jpeg,png,jpg,pdf,ai,psd|max:10240'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak valid',
                    'errors' => $validator->errors()
                ], 422);
            }

            $token = session('api_token');
            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Silakan login terlebih dahulu'
                ], 401);
            }

            $requestData = [
                'item_id' => $request->item_id,
                'ukuran_id' => $request->ukuran_id,
                'bahan_id' => $request->bahan_id,
                'jenis_id' => $request->jenis_id,
                'tipe_desain' => $request->tipe_desain,
                'jumlah' => $request->jumlah
            ];

            // Handle file upload jika ada
            if ($request->hasFile('upload_desain')) {
                $response = Http::withToken($token)
                    ->timeout(30)
                    ->attach(
                        'upload_desain',
                        file_get_contents($request->file('upload_desain')->getRealPath()),
                        $request->file('upload_desain')->getClientOriginalName()
                    )
                    ->post($this->apiBaseUrl . '/keranjang', $requestData);
            } else {
                $response = Http::withToken($token)
                    ->post($this->apiBaseUrl . '/keranjang', $requestData);
            }

            $responseData = $response->json();

            // Log response untuk debugging
            Log::info('Response dari API add to cart:', [
                'status' => $response->status(),
                'response' => $responseData
            ]);

            if ($response->successful() && ($responseData['success'] ?? false)) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Produk berhasil ditambahkan ke keranjang',
                        'data' => $responseData['data'] ?? null
                    ]);
                } else {
                    return redirect()->back()->with('success', 'Produk berhasil ditambahkan ke keranjang');
                }
            }

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $responseData['message'] ?? 'Gagal menambahkan ke keranjang',
                    'errors' => $responseData['errors'] ?? null
                ], $response->status());
            } else {
                return redirect()->back()->with('error', $responseData['message'] ?? 'Gagal menambahkan ke keranjang');
            }
        } catch (\Exception $e) {
            Log::error('Error adding to cart: ' . $e->getMessage());
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem']);
            } else {
                return redirect()->back()->with('error', 'Terjadi kesalahan sistem');
            }
        }
    }

    /**
     * Update jumlah item di keranjang
     */
    public function updateQuantity(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'jumlah' => 'required|integer|min:1|max:100'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jumlah tidak valid'
                ], 422);
            }

            $token = session('api_token');
            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Silakan login terlebih dahulu'
                ], 401);
            }

            $response = Http::withToken($token)
                ->put($this->apiBaseUrl . '/keranjang/' . $id, [
                    'jumlah' => $request->jumlah
                ]);

            $responseData = $response->json();

            if ($response->successful() && ($responseData['success'] ?? false)) {
                return response()->json([
                    'success' => true,
                    'message' => 'Jumlah berhasil diperbarui',
                    'data' => $responseData['data'] ?? null
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $responseData['message'] ?? 'Gagal memperbarui jumlah'
            ], $response->status());
        } catch (\Exception $e) {
            Log::error('Error updating quantity: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem'
            ], 500);
        }
    }

    /**
     * Upload/ganti desain untuk item di keranjang
     */
    public function uploadDesign(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'upload_desain' => 'required|file|mimes:jpeg,png,jpg,pdf,ai,psd|max:10240'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'File tidak valid',
                    'errors' => $validator->errors()
                ], 422);
            }

            $token = session('api_token');
            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Silakan login terlebih dahulu'
                ], 401);
            }

            $response = Http::withToken($token)
                ->timeout(30)
                ->attach(
                    'upload_desain',
                    file_get_contents($request->file('upload_desain')->getRealPath()),
                    $request->file('upload_desain')->getClientOriginalName()
                )
                ->put($this->apiBaseUrl . '/keranjang/' . $id);

            $responseData = $response->json();

            if ($response->successful() && ($responseData['success'] ?? false)) {
                return response()->json([
                    'success' => true,
                    'message' => 'Desain berhasil diupload',
                    'data' => $responseData['data'] ?? null
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $responseData['message'] ?? 'Gagal mengupload desain'
            ], $response->status());
        } catch (\Exception $e) {
            Log::error('Error uploading design: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem'
            ], 500);
        }
    }

    /**
     * Hapus item dari keranjang
     */
    public function removeItem(Request $request, $id)
    {
        try {
            $token = session('api_token');
            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Silakan login terlebih dahulu'
                ], 401);
            }

            $response = Http::withToken($token)
                ->delete($this->apiBaseUrl . '/keranjang/' . $id);

            $responseData = $response->json();

            if ($response->successful() && ($responseData['success'] ?? false)) {
                return response()->json([
                    'success' => true,
                    'message' => 'Item berhasil dihapus dari keranjang'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $responseData['message'] ?? 'Gagal menghapus item'
            ], $response->status());
        } catch (\Exception $e) {
            Log::error('Error removing item: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem'
            ], 500);
        }
    }

    /**
     * Kosongkan seluruh keranjang
     */
    public function clearCart(Request $request)
    {
        try {
            $token = session('api_token');
            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Silakan login terlebih dahulu'
                ], 401);
            }

            $response = Http::withToken($token)
                ->delete($this->apiBaseUrl . '/keranjang/clear');

            $responseData = $response->json();

            if ($response->successful() && ($responseData['success'] ?? false)) {
                return response()->json([
                    'success' => true,
                    'message' => 'Keranjang berhasil dikosongkan'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $responseData['message'] ?? 'Gagal mengosongkan keranjang'
            ], $response->status());
        } catch (\Exception $e) {
            Log::error('Error clearing cart: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem'
            ], 500);
        }
    }

    /**
     * Get cart count untuk navbar
     */
    public function getCartCount()
    {
        try {
            $token = session('api_token');
            if (!$token) {
                return response()->json(['count' => 0]);
            }

            $response = Http::withToken($token)
                ->get($this->apiBaseUrl . '/keranjang');

            if ($response->successful()) {
                $data = $response->json();
                $count = $data['data']['summary']['total_items'] ?? 0;
                return response()->json(['count' => $count]);
            }

            return response()->json(['count' => 0]);
        } catch (\Exception $e) {
            Log::error('Error getting cart count: ' . $e->getMessage());
            return response()->json(['count' => 0]);
        }
    }
}