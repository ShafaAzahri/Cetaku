<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jenis;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JenisApiController extends Controller
{
    /**
     * Menampilkan semua jenis
     */
    public function index()
    {
        $jenis = Jenis::all();
        return response()->json([
            'success' => true,
            'jenis' => $jenis
        ]);
    }

    /**
     * Menyimpan jenis baru
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'kategori' => 'required|string|max:255',
            'biaya_tambahan' => 'required|numeric|min:0',
            'item_ids' => 'nullable|array',
            'item_ids.*' => 'exists:items,id'
        ]);
        
        $jenis = new Jenis();
        $jenis->kategori = $request->kategori;
        $jenis->biaya_tambahan = $request->biaya_tambahan;
        $jenis->save();
        
        // Jika ada item yang dipilih, hubungkan dengan jenis ini
        if ($request->has('item_ids') && !empty($request->item_ids)) {
            $jenis->items()->attach($request->item_ids);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Jenis berhasil ditambahkan',
            'jenis' => $jenis,
            'items' => $jenis->items
        ], 201);
    }

    /**
     * Menampilkan jenis berdasarkan id
     */
    // Di JenisApiController.php
    public function show($id)
    {
        $jenis = Jenis::with('items')->find($id);
        
        if (!$jenis) {
            return response()->json([
                'success' => false,
                'message' => 'Jenis tidak ditemukan'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'jenis' => $jenis,
            'items' => $jenis->items
        ]);
    }

    /**
     * Memperbarui jenis berdasarkan id
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'kategori' => 'required|string|max:255',
            'biaya_tambahan' => 'required|numeric|min:0',
            'item_ids' => 'nullable|array',
            'item_ids.*' => 'exists:items,id'
        ]);
        
        $jenis = Jenis::find($id);
        
        if (!$jenis) {
            return response()->json([
                'success' => false,
                'message' => 'Jenis tidak ditemukan'
            ], 404);
        }
        
        $jenis->kategori = $request->kategori;
        $jenis->biaya_tambahan = $request->biaya_tambahan;
        $jenis->save();
        
        // Update relasi dengan item
        if ($request->has('item_ids')) {
            // Tambahkan log untuk debugging
            Log::info('Menyinkronkan item untuk jenis', [
                'jenis_id' => $id,
                'item_ids' => $request->item_ids
            ]);
            $jenis->items()->sync($request->item_ids);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Jenis berhasil diperbarui',
            'jenis' => $jenis,
            'items' => $jenis->items()->get()
        ]);
    }

    /**
     * Menghapus jenis berdasarkan id
     */
    public function destroy($id)
    {
        try {
            // Gunakan transaction untuk memastikan semua operasi berhasil atau tidak sama sekali
            return DB::transaction(function() use ($id) {
                $jenis = Jenis::find($id);
                
                if (!$jenis) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Jenis tidak ditemukan'
                    ], 404);
                }
                
                // Cek apakah jenis digunakan dalam Custom (produk yang mungkin sudah dipesan)
                $customCount = DB::table('customs')->where('jenis_id', $id)->count();
                if ($customCount > 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Jenis tidak dapat dihapus karena sudah digunakan dalam pesanan'
                    ], 400);
                }
                
                // Cek juga apakah jenis digunakan langsung dalam tabel items
                $itemCount = DB::table('items')->where('jenis_id', $id)->count();
                if ($itemCount > 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Jenis tidak dapat dihapus karena digunakan sebagai kategori utama dalam ' . $itemCount . ' item'
                    ], 400);
                }
                
                // Hapus relasi dengan item terlebih dahulu (dari tabel pivot)
                $jenis->items()->detach();
                
                // Hapus jenis
                $jenis->delete();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Jenis berhasil dihapus'
                ]);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus jenis: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Mendapatkan semua item berdasarkan jenis
     */
    public function getItemsByJenis($id)
    {
        $jenis = Jenis::find($id);
        
        if (!$jenis) {
            return response()->json([
                'success' => false,
                'message' => 'Jenis tidak ditemukan'
            ], 404);
        }
        
        $items = $jenis->items;
        
        return response()->json([
            'success' => true,
            'jenis' => $jenis->kategori,
            'items' => $items
        ]);
    }
}
