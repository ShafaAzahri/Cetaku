<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ukuran;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


class UkuranApiController extends Controller
{
    /**
     * Menampilkan semua ukuran
     */
    public function index()
    {
        Log::info('API: Request untuk daftar ukuran diterima');
        // Eager load relations untuk mengurangi N+1 query problem
        $ukurans = Ukuran::with('items')->get();
        
        return response()->json([
            'success' => true,
            'ukurans' => $ukurans
        ]);
    }

    /**
     * Menyimpan ukuran baru
     */
    public function store(Request $request)
    {
        Log::info('API: Request untuk menambah ukuran baru diterima', $request->all());
        
        $validatedData = $request->validate([
            'size' => 'required|string|max:100',
            'biaya_tambahan' => 'required|numeric|min:0',  // ganti dari 'faktor_harga'
            'item_ids' => 'nullable|array',
            'item_ids.*' => 'exists:items,id'
        ]);
        
        $ukuran = new Ukuran();
        $ukuran->size = $request->size;
        $ukuran->biaya_tambahan = $request->biaya_tambahan;  // ganti dari faktor_harga
        $ukuran->save();
        
        // Jika ada item yang dipilih, hubungkan dengan ukuran ini
        if ($request->has('item_ids') && !empty($request->item_ids)) {
            $ukuran->items()->attach($request->item_ids);
        }
        
        Log::info('API: Ukuran berhasil disimpan', ['id' => $ukuran->id, 'size' => $ukuran->size]);
        
        return response()->json([
            'success' => true,
            'message' => 'Ukuran berhasil ditambahkan',
            'ukuran' => $ukuran,
            'items' => $ukuran->items
        ], 201);
    }

    /**
     * Menampilkan ukuran berdasarkan id
     */
    public function show($id)
    {
        Log::info('API: Request untuk menampilkan ukuran diterima', ['id' => $id]);
        
        $ukuran = Ukuran::with('items')->find($id);
        
        if (!$ukuran) {
            return response()->json([
                'success' => false,
                'message' => 'Ukuran tidak ditemukan'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'ukuran' => $ukuran,
            'items' => $ukuran->items
        ]);
    }

    /**
     * Memperbarui ukuran berdasarkan id
     */
    public function update(Request $request, $id)
    {
        Log::info('API: Request untuk memperbarui ukuran diterima', ['id' => $id, 'data' => $request->all()]);
        
        $validatedData = $request->validate([
            'size' => 'required|string|max:100',
            'biaya_tambahan' => 'required|numeric|min:0',  // ganti dari 'faktor_harga'
            'item_ids' => 'nullable|array',
            'item_ids.*' => 'exists:items,id'
        ]);
        
        $ukuran = Ukuran::find($id);
        
        if (!$ukuran) {
            return response()->json([
                'success' => false,
                'message' => 'Ukuran tidak ditemukan'
            ], 404);
        }
        
        $ukuran->size = $request->size;
        $ukuran->biaya_tambahan = $request->biaya_tambahan;  // ganti dari faktor_harga
        $ukuran->save();
        
        // Update relasi dengan item
        if ($request->has('item_ids')) {
            $ukuran->items()->sync($request->item_ids);
        }
        
        Log::info('API: Ukuran berhasil diperbarui', ['id' => $ukuran->id]);
        
        return response()->json([
            'success' => true,
            'message' => 'Ukuran berhasil diperbarui',
            'ukuran' => $ukuran,
            'items' => $ukuran->items()->get()
        ]);
    }

    /**
     * Menghapus ukuran berdasarkan id
     */
    public function destroy($id)
    {
        try {
            Log::info('API: Request untuk menghapus ukuran diterima', ['id' => $id]);
            
            // Gunakan transaction untuk memastikan semua operasi berhasil atau tidak sama sekali
            return DB::transaction(function() use ($id) {
                $ukuran = Ukuran::find($id);
                
                if (!$ukuran) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Ukuran tidak ditemukan'
                    ], 404);
                }
                
                // Cek apakah ukuran digunakan dalam Custom (produk yang mungkin sudah dipesan)
                $customCount = DB::table('customs')->where('ukuran_id', $id)->count();
                if ($customCount > 0) {
                    Log::warning('API: Ukuran tidak dapat dihapus karena digunakan dalam tabel customs', [
                        'ukuran_id' => $id,
                        'custom_count' => $customCount
                    ]);
                    
                    return response()->json([
                        'success' => false,
                        'message' => 'Ukuran tidak dapat dihapus karena sudah digunakan dalam pesanan'
                    ], 400);
                }
                
                // Hapus relasi dengan item terlebih dahulu
                Log::info('API: Menghapus relasi ukuran dengan item', ['ukuran_id' => $id]);
                $ukuran->items()->detach();
                
                // Hapus ukuran
                $ukuran->delete();
                
                Log::info('API: Ukuran berhasil dihapus', ['id' => $id]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Ukuran berhasil dihapus'
                ]);
            });
        } catch (\Exception $e) {
            Log::error('API: Error pada destroy ukuran: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus ukuran: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Mendapatkan semua item berdasarkan ukuran
     */
    public function getItemsByUkuran($id)
    {
        Log::info('API: Request untuk mendapatkan item berdasarkan ukuran', ['ukuran_id' => $id]);
        
        $ukuran = Ukuran::find($id);
        
        if (!$ukuran) {
            return response()->json([
                'success' => false,
                'message' => 'Ukuran tidak ditemukan'
            ], 404);
        }
        
        $items = $ukuran->items;
        
        return response()->json([
            'success' => true,
            'ukuran' => $ukuran->size,
            'items' => $items
        ]);
    }
}