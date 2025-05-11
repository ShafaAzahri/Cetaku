<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;


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
        try {
            Log::info('API: Request untuk menambah item baru diterima', $request->except('gambar'));
            
            // Log gambar info jika ada
            if ($request->hasFile('gambar')) {
                $file = $request->file('gambar');
                Log::info('API: Informasi file gambar yang diupload', [
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'error' => $file->getError(),
                    'is_valid' => $file->isValid()
                ]);
            } else {
                Log::info('API: Tidak ada file gambar yang diupload');
            }
            
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
            if ($request->hasFile('gambar') && $request->file('gambar')->isValid()) {
                // Generate a unique filename
                $file = $request->file('gambar');
                $fileName = Str::random(20) . '.' . $file->getClientOriginalExtension();
                $directoryPath = 'product-images';
                
                // Ensure directory exists
                $fullDirectoryPath = storage_path('app/public/' . $directoryPath);
                if (!File::isDirectory($fullDirectoryPath)) {
                    Log::info('API: Creating directory', ['path' => $fullDirectoryPath]);
                    File::makeDirectory($fullDirectoryPath, 0755, true);
                }
                
                // Move the file directly
                $uploadSuccess = $file->move($fullDirectoryPath, $fileName);
                
                if ($uploadSuccess) {
                    $item->gambar = $directoryPath . '/' . $fileName;
                    Log::info('API: Gambar berhasil diupload', [
                        'path' => $item->gambar,
                        'full_path' => $fullDirectoryPath . '/' . $fileName,
                        'exists' => File::exists($fullDirectoryPath . '/' . $fileName)
                    ]);
                } else {
                    Log::error('API: Gagal memindahkan file gambar', [
                        'target_path' => $fullDirectoryPath . '/' . $fileName
                    ]);
                }
            }
            
            $item->save();
            
            Log::info('API: Item berhasil disimpan', [
                'id' => $item->id, 
                'nama' => $item->nama_item,
                'gambar_path' => $item->gambar
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Item berhasil ditambahkan',
                'item' => $item
            ], 201);
        } catch (\Exception $e) {
            Log::error('API: Error pada store item: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan item',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan item berdasarkan id
     */
    public function show($id)
    {
        try {
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
        } catch (\Exception $e) {
            Log::error('API: Error pada show item: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data item',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Memperbarui item berdasarkan id
     */
    public function update(Request $request, $id)
    {
        try {
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
            if ($request->hasFile('gambar') && $request->file('gambar')->isValid()) {
                // Generate a unique filename
                $file = $request->file('gambar');
                $fileName = Str::random(20) . '.' . $file->getClientOriginalExtension();
                $directoryPath = 'product-images';
                
                // Ensure directory exists
                $fullDirectoryPath = storage_path('app/public/' . $directoryPath);
                if (!File::isDirectory($fullDirectoryPath)) {
                    Log::info('API: Creating directory', ['path' => $fullDirectoryPath]);
                    File::makeDirectory($fullDirectoryPath, 0755, true);
                }
                
                // Delete old image if exists
                if ($item->gambar) {
                    $oldImagePath = storage_path('app/public/' . $item->gambar);
                    if (File::exists($oldImagePath)) {
                        Log::info('API: Deleting old image', ['path' => $oldImagePath]);
                        File::delete($oldImagePath);
                    }
                }
                
                // Move the file directly
                $uploadSuccess = $file->move($fullDirectoryPath, $fileName);
                
                if ($uploadSuccess) {
                    $item->gambar = $directoryPath . '/' . $fileName;
                    Log::info('API: Gambar berhasil diupload (update)', [
                        'path' => $item->gambar,
                        'full_path' => $fullDirectoryPath . '/' . $fileName,
                        'exists' => File::exists($fullDirectoryPath . '/' . $fileName)
                    ]);
                } else {
                    Log::error('API: Gagal memindahkan file gambar (update)', [
                        'target_path' => $fullDirectoryPath . '/' . $fileName
                    ]);
                }
            }
            
            $item->save();
            
            Log::info('API: Item berhasil diperbarui', [
                'id' => $item->id,
                'gambar_path' => $item->gambar
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Item berhasil diperbarui',
                'item' => $item
            ]);
        } catch (\Exception $e) {
            Log::error('API: Error pada update item: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui item',
                'error' => $e->getMessage()
            ], 500);
        }
    }

   /**
     * Menghapus item berdasarkan id
     */
    public function destroy($id)
    {
        try {
            Log::info('API: Request untuk menghapus item diterima', ['id' => $id]);
            
            // Gunakan transaction untuk memastikan semua operasi berhasil atau tidak sama sekali
            return DB::transaction(function() use ($id) {
                $item = Item::find($id);
                
                if (!$item) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Item tidak ditemukan'
                    ], 404);
                }
                
                // Cek apakah item digunakan dalam Custom (produk yang mungkin sudah dipesan)
                $customCount = DB::table('customs')->where('item_id', $id)->count();
                if ($customCount > 0) {
                    Log::warning('API: Item tidak dapat dihapus karena digunakan dalam tabel customs', [
                        'item_id' => $id,
                        'custom_count' => $customCount
                    ]);
                    
                    return response()->json([
                        'success' => false,
                        'message' => 'Item tidak dapat dihapus karena sudah digunakan dalam pesanan'
                    ], 400);
                }
                
                // Hapus relasi dengan bahan, ukuran, dan jenis terlebih dahulu
                Log::info('API: Menghapus relasi item dengan bahan', ['item_id' => $id]);
                $item->bahans()->detach();
                
                Log::info('API: Menghapus relasi item dengan ukuran', ['item_id' => $id]);
                $item->ukurans()->detach();
                
                Log::info('API: Menghapus relasi item dengan jenis', ['item_id' => $id]);
                $item->jenis()->detach();
                
                // Hapus gambar jika ada
                if ($item->gambar) {
                    $imagePath = storage_path('app/public/' . $item->gambar);
                    if (File::exists($imagePath)) {
                        Log::info('API: Menghapus gambar item', ['gambar' => $imagePath]);
                        File::delete($imagePath);
                    } else {
                        Log::warning('API: Gambar tidak ditemukan saat menghapus', ['gambar' => $imagePath]);
                    }
                }
                
                // Hapus item
                $item->delete();
                
                Log::info('API: Item berhasil dihapus', ['id' => $id]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Item berhasil dihapus'
                ]);
            });
        } catch (\Exception $e) {
            Log::error('API: Error pada destroy item: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus item: ' . $e->getMessage()
            ], 500);
        }
    }
}