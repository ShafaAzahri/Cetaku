<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Jenis;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class JenisApiController extends Controller
{
    /**
     * Menampilkan semua jenis
     */
    public function index()
    {
        Log::info('API: Request untuk daftar jenis diterima');
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
        Log::info('API: Request untuk menambah jenis baru diterima', $request->all());
        
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
        
        Log::info('API: Jenis berhasil disimpan', ['id' => $jenis->id, 'kategori' => $jenis->kategori]);
        
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
    public function show($id)
    {
        Log::info('API: Request untuk menampilkan jenis diterima', ['id' => $id]);
        
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
        Log::info('API: Request untuk memperbarui jenis diterima', ['id' => $id, 'data' => $request->all()]);
        
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
            $jenis->items()->sync($request->item_ids);
        }
        
        Log::info('API: Jenis berhasil diperbarui', ['id' => $jenis->id]);
        
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
        Log::info('API: Request untuk menghapus jenis diterima', ['id' => $id]);
        
        $jenis = Jenis::find($id);
        
        if (!$jenis) {
            return response()->json([
                'success' => false,
                'message' => 'Jenis tidak ditemukan'
            ], 404);
        }
        
        // Hapus relasi dengan item sebelum menghapus jenis
        $jenis->items()->detach();
        $jenis->delete();
        
        Log::info('API: Jenis berhasil dihapus', ['id' => $id]);
        
        return response()->json([
            'success' => true,
            'message' => 'Jenis berhasil dihapus'
        ]);
    }
    
    /**
     * Mendapatkan semua item berdasarkan jenis
     */
    public function getItemsByJenis($id)
    {
        Log::info('API: Request untuk mendapatkan item berdasarkan jenis', ['jenis_id' => $id]);
        
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