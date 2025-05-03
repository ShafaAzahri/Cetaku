<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ItemApiController extends Controller
{
    /**
     * Menampilkan semua items
     */
    public function index()
    {
        Log::info('API: Request untuk daftar item diterima');
        $items = Item::all();
        return response()->json([
            'success' => true,
            'items' => $items
        ]);
    }

    /**
     * Menyimpan item baru
     */
    public function store(Request $request)
    {
        Log::info('API: Request untuk menambah item baru diterima', $request->except('gambar'));
        
        $validatedData = $request->validate([
            'nama_item' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga_dasar' => 'required|numeric|min:0',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        $item = new Item();
        $item->nama_item = $request->nama_item;
        $item->deskripsi = $request->deskripsi;
        $item->harga_dasar = $request->harga_dasar;
        
        // Upload gambar jika ada
        if ($request->hasFile('gambar')) {
            Log::debug('API: Gambar ditemukan, memproses upload');
            $gambar = $request->file('gambar');
            $gambarName = 'product-images/' . Str::slug($request->nama_item) . '_' . time() . '.' . $gambar->getClientOriginalExtension();
            $gambar->storeAs('public', $gambarName);
            $item->gambar = $gambarName;
        }
        
        $item->save();
        
        Log::info('API: Item berhasil disimpan', ['id' => $item->id, 'nama' => $item->nama_item]);
        
        return response()->json([
            'success' => true,
            'message' => 'Item berhasil ditambahkan',
            'item' => $item
        ], 201);
    }

    /**
     * Menampilkan item berdasarkan id
     */
    public function show($id)
    {
        Log::info('API: Request untuk menampilkan item diterima', ['id' => $id]);
        
        $item = Item::find($id);
        
        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Item tidak ditemukan'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'item' => $item
        ]);
    }

    /**
     * Memperbarui item berdasarkan id
     */
    public function update(Request $request, $id)
    {
        Log::info('API: Request untuk memperbarui item diterima', ['id' => $id, 'data' => $request->except('gambar')]);
        
        $validatedData = $request->validate([
            'nama_item' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga_dasar' => 'required|numeric|min:0',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        $item = Item::find($id);
        
        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Item tidak ditemukan'
            ], 404);
        }
        
        $item->nama_item = $request->nama_item;
        $item->deskripsi = $request->deskripsi;
        $item->harga_dasar = $request->harga_dasar;
        
        // Upload gambar baru jika ada
        if ($request->hasFile('gambar')) {
            Log::debug('API: Gambar baru ditemukan, memproses upload');
            
            // Hapus gambar lama jika ada
            if ($item->gambar) {
                Log::debug('API: Menghapus gambar lama', ['gambar' => $item->gambar]);
                Storage::delete('public/' . $item->gambar);
            }
            
            $gambar = $request->file('gambar');
            $gambarName = 'product-images/' . Str::slug($request->nama_item) . '_' . time() . '.' . $gambar->getClientOriginalExtension();
            $gambar->storeAs('public', $gambarName);
            $item->gambar = $gambarName;
        }
        
        $item->save();
        
        Log::info('API: Item berhasil diperbarui', ['id' => $item->id]);
        
        return response()->json([
            'success' => true,
            'message' => 'Item berhasil diperbarui',
            'item' => $item
        ]);
    }

    /**
     * Menghapus item berdasarkan id
     */
    public function destroy($id)
    {
        Log::info('API: Request untuk menghapus item diterima', ['id' => $id]);
        
        $item = Item::find($id);
        
        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Item tidak ditemukan'
            ], 404);
        }
        
        // Hapus gambar jika ada
        if ($item->gambar) {
            Log::debug('API: Menghapus gambar item', ['gambar' => $item->gambar]);
            Storage::delete('public/' . $item->gambar);
        }
        
        $item->delete();
        
        Log::info('API: Item berhasil dihapus', ['id' => $id]);
        
        return response()->json([
            'success' => true,
            'message' => 'Item berhasil dihapus'
        ]);
    }
}