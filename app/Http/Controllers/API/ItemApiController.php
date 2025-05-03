<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ItemApiController extends Controller
{
    // ...kode lainnya tetap sama...
    
    /**
     * Menyimpan item baru
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_item' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga_dasar' => 'required|numeric|min:0',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $item = new Item();
            $item->nama_item = $request->nama_item;
            $item->deskripsi = $request->deskripsi;
            $item->harga_dasar = $request->harga_dasar;
            
            // Upload gambar jika ada
            if ($request->hasFile('gambar')) {
                $gambar = $request->file('gambar');
                $gambarName = 'product-images/' . time() . '_' . $gambar->getClientOriginalName();
                $gambar->storeAs('public', $gambarName);
                $item->gambar = $gambarName;
            }
            
            $item->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Item berhasil disimpan',
                'item' => $item
            ], 201);
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan item: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan item'
            ], 500);
        }
    }
    
    /**
     * Update item
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama_item' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga_dasar' => 'required|numeric|min:0',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
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
                // Hapus gambar lama jika ada
                if ($item->gambar) {
                    Storage::delete('public/' . $item->gambar);
                }
                
                $gambar = $request->file('gambar');
                $gambarName = 'product-images/' . time() . '_' . $gambar->getClientOriginalName();
                $gambar->storeAs('public', $gambarName);
                $item->gambar = $gambarName;
            }
            
            $item->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Item berhasil diperbarui',
                'item' => $item
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal memperbarui item: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui item'
            ], 500);
        }
    }
    
    /**
     * Hapus item
     */
    public function destroy($id)
    {
        try {
            $item = Item::find($id);
            
            if (!$item) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item tidak ditemukan'
                ], 404);
            }
            
            // Hapus gambar jika ada
            if ($item->gambar) {
                Storage::delete('public/' . $item->gambar);
            }
            
            $item->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Item berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal menghapus item: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus item'
            ], 500);
        }
    }
}