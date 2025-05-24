<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\Keranjang;
use App\Models\Item;
use App\Models\Ukuran;
use App\Models\Bahan;
use App\Models\Jenis;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Models\Custom;
use App\Models\Pembayaran;
use App\Models\BiayaDesain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class KeranjangApiController extends Controller
{
    /**
     * Mendapatkan semua item di keranjang user
     */
    public function index(Request $request)
    {
        try {
            $user = $request->authenticated_user;

            $keranjangItems = Keranjang::with([
                'item',
                'ukuran',
                'bahan',
                'jenis'
            ])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

            // Hitung total keranjang
            $totalItems = $keranjangItems->sum('quantity');
            $totalHarga = $keranjangItems->sum('total_harga');

            // Group by kategori jika ada
            $groupedItems = $keranjangItems->groupBy(function($item) {
                return $item->item->kategoris->first()->nama_kategori ?? 'Lainnya';
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'items' => $keranjangItems,
                    'grouped_items' => $groupedItems,
                    'summary' => [
                        'total_items' => $totalItems,
                        'total_harga' => $totalHarga,
                        'count_products' => $keranjangItems->count()
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching keranjang: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data keranjang'
            ], 500);
        }
    }

    /**
     * Menambah item ke keranjang
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'item_id' => 'required|exists:items,id',
                'ukuran_id' => 'required|exists:ukurans,id',
                'bahan_id' => 'required|exists:bahans,id',
                'jenis_id' => 'required|exists:jenis,id',
                'quantity' => 'required|integer|min:1|max:100',
                'tipe_desain' => 'required|in:sendiri,toko',
                'upload_desain' => 'nullable|file|mimes:jpeg,png,jpg,pdf,ai,psd|max:10240'
            ]);

            if ($validator->fails()) {
                // DEBUG: Log validation errors
                Log::error('Validation failed:', $validator->errors()->toArray());
                
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors(),
                    'received_data' => $request->all() // DEBUG: kirim data yang diterima
                ], 422);
            }

            $user = $request->authenticated_user;

            // Validasi: jika tipe_desain sendiri, harus ada file
            if ($request->tipe_desain === 'sendiri' && !$request->hasFile('upload_desain')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Untuk desain sendiri, mohon upload file desain'
                ], 422);
            }

            // Validasi: jika tipe_desain toko, tidak perlu file
            if ($request->tipe_desain === 'toko' && $request->hasFile('upload_desain')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Untuk desain toko, tidak perlu upload file'
                ], 422);
            }

            // Cek existing item dengan kombinasi lengkap termasuk tipe_desain
            $existingItem = Keranjang::where([
                'user_id' => $user->id,
                'item_id' => $request->item_id,
                'ukuran_id' => $request->ukuran_id,
                'bahan_id' => $request->bahan_id,
                'jenis_id' => $request->jenis_id,
                'tipe_desain' => $request->tipe_desain,
            ])->first();

            DB::beginTransaction();

            if ($existingItem) {
                // Update quantity jika kombinasi sudah ada
                $existingItem->quantity += $request->quantity;
                $existingItem->save();
                $keranjangItem = $existingItem;
            } else {
                // Buat item keranjang baru
                $keranjangItem = new Keranjang();
                $keranjangItem->user_id = $user->id;
                $keranjangItem->item_id = $request->item_id;
                $keranjangItem->ukuran_id = $request->ukuran_id;
                $keranjangItem->bahan_id = $request->bahan_id;
                $keranjangItem->jenis_id = $request->jenis_id;
                $keranjangItem->tipe_desain = $request->tipe_desain;
                $keranjangItem->quantity = $request->quantity;
                
                // Load relasi untuk perhitungan harga
                $keranjangItem->load(['item', 'ukuran', 'bahan', 'jenis']);
                $keranjangItem->save();
            }

            // Handle upload desain jika ada
            if ($request->hasFile('upload_desain')) {
                $uploadedFile = $this->handleFileUpload($request->file('upload_desain'), $user->id);
                $keranjangItem->upload_desain = $uploadedFile;
                $keranjangItem->save();
            }

            DB::commit();

            // Load relasi untuk response
            $keranjangItem->load(['item', 'ukuran', 'bahan', 'jenis']);

            return response()->json([
                'success' => true,
                'message' => 'Item berhasil ditambahkan ke keranjang',
                'data' => $keranjangItem
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error adding to keranjang: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menambahkan ke keranjang'
            ], 500);
        }
    }

    /**
     * Update item di keranjang (quantity atau upload desain)
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'quantity' => 'nullable|integer|min:1|max:100',
                'upload_desain' => 'nullable|file|mimes:jpeg,png,jpg,pdf,ai,psd|max:10240'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = $request->authenticated_user;

            $keranjangItem = Keranjang::where('id', $id)
                ->where('user_id', $user->id)
                ->first();

            if (!$keranjangItem) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item tidak ditemukan di keranjang'
                ], 404);
            }

            DB::beginTransaction();

            // Update quantity jika ada
            if ($request->has('quantity')) {
                $keranjangItem->quantity = $request->quantity;
            }

            // Handle upload desain baru
            if ($request->hasFile('upload_desain')) {
                // Hapus file lama jika ada
                if ($keranjangItem->upload_desain) {
                    Storage::disk('public')->delete($keranjangItem->upload_desain);
                }

                $uploadedFile = $this->handleFileUpload($request->file('upload_desain'), $user->id);
                $keranjangItem->upload_desain = $uploadedFile;
            }

            $keranjangItem->save(); // auto-recalculate harga

            DB::commit();

            $keranjangItem->load(['item', 'ukuran', 'bahan', 'jenis']);

            return response()->json([
                'success' => true,
                'message' => 'Item keranjang berhasil diperbarui',
                'data' => $keranjangItem
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating keranjang: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui keranjang'
            ], 500);
        }
    }

    /**
     * Hapus item dari keranjang
     */
    public function destroy(Request $request, $id)
    {
        try {
            $user = $request->authenticated_user;

            $keranjangItem = Keranjang::where('id', $id)
                ->where('user_id', $user->id)
                ->first();

            if (!$keranjangItem) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item tidak ditemukan di keranjang'
                ], 404);
            }

            // Hapus file desain jika ada
            if ($keranjangItem->upload_desain) {
                Storage::disk('public')->delete($keranjangItem->upload_desain);
            }

            $keranjangItem->delete();

            return response()->json([
                'success' => true,
                'message' => 'Item berhasil dihapus dari keranjang'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting from keranjang: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus dari keranjang'
            ], 500);
        }
    }

    /**
     * Hapus semua item dari keranjang
     */
    public function clear(Request $request)
    {
        try {
            $user = $request->authenticated_user;

            $keranjangItems = Keranjang::where('user_id', $user->id)->get();

            // Hapus semua file desain
            foreach ($keranjangItems as $item) {
                if ($item->upload_desain) {
                    Storage::disk('public')->delete($item->upload_desain);
                }
            }

            // Hapus semua item
            Keranjang::where('user_id', $user->id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Keranjang berhasil dikosongkan'
            ]);

        } catch (\Exception $e) {
            Log::error('Error clearing keranjang: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengosongkan keranjang'
            ], 500);
        }
    }

    /**
     * Handle file upload untuk desain
     */
    private function handleFileUpload($file, $userId)
    {
        $fileName = $userId . '_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('keranjang-designs', $fileName, 'public');
        return $path;
    }
}