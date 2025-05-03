<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Ukuran;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UkuranApiController extends Controller
{
    /**
     * Menampilkan semua ukuran
     */
    public function index()
    {
        Log::info('API: Request untuk daftar ukuran diterima');
        $ukurans = Ukuran::all();
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
            'faktor_harga' => 'required|numeric|min:0',
            'item_ids' => 'nullable|array',
            'item_ids.*' => 'exists:items,id'
        ]);
        
        $ukuran = new Ukuran();
        $ukuran->size = $request->size;
        $ukuran->faktor_harga = $request->faktor_harga;
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
            'faktor_harga' => 'required|numeric|min:0',
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
        $ukuran->faktor_harga = $request->faktor_harga;
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
        Log::info('API: Request untuk menghapus ukuran diterima', ['id' => $id]);
        
        $ukuran = Ukuran::find($id);
        
        if (!$ukuran) {
            return response()->json([
                'success' => false,
                'message' => 'Ukuran tidak ditemukan'
            ], 404);
        }
        
        // Hapus relasi dengan item sebelum menghapus ukuran
        $ukuran->items()->detach();
        $ukuran->delete();
        
        Log::info('API: Ukuran berhasil dihapus', ['id' => $id]);
        
        return response()->json([
            'success' => true,
            'message' => 'Ukuran berhasil dihapus'
        ]);
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