<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class KategoriApiController extends Controller
{
    /**
     * Menampilkan semua kategori
     */
    public function index()
    {
        try {
            $kategoris = Kategori::with('items')->get();
            
            return response()->json([
                'success' => true,
                'kategoris' => $kategoris
            ]);
        } catch (\Exception $e) {
            Log::error('API Error: Gagal mendapatkan daftar kategori - ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil daftar kategori',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menyimpan kategori baru
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nama_kategori' => 'required|string|max:255',
                'deskripsi' => 'nullable|string',
                'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'item_ids' => 'nullable|array',
                'item_ids.*' => 'exists:items,id'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            DB::beginTransaction();
            
            $kategori = new Kategori();
            $kategori->nama_kategori = $request->nama_kategori;
            $kategori->deskripsi = $request->deskripsi;
            
            // Upload gambar jika ada
            if ($request->hasFile('gambar') && $request->file('gambar')->isValid()) {
                $file = $request->file('gambar');
                $fileName = Str::random(20) . '.' . $file->getClientOriginalExtension();
                $directoryPath = 'kategori-images';
                
                $fullDirectoryPath = storage_path('app/public/' . $directoryPath);
                if (!File::isDirectory($fullDirectoryPath)) {
                    File::makeDirectory($fullDirectoryPath, 0755, true);
                }
                
                $uploadSuccess = $file->move($fullDirectoryPath, $fileName);
                if ($uploadSuccess) {
                    $kategori->gambar = $directoryPath . '/' . $fileName;
                }
            }
            
            $kategori->save();
            
            // Jika ada item yang dipilih, hubungkan dengan kategori ini
            if ($request->has('item_ids') && !empty($request->item_ids)) {
                $kategori->items()->attach($request->item_ids);
            }
            
            DB::commit();
            
            // Refresh model untuk mendapatkan relasi
            $kategori->load('items');
            
            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil ditambahkan',
                'kategori' => $kategori
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('API Error: Gagal menyimpan kategori - ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan kategori',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan kategori berdasarkan id
     */
    public function show($id)
    {
        try {
            $kategori = Kategori::with('items')->find($id);
            
            if (!$kategori) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kategori tidak ditemukan'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'kategori' => $kategori
            ]);
        } catch (\Exception $e) {
            Log::error('API Error: Gagal mendapatkan detail kategori - ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil detail kategori',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Memperbarui kategori berdasarkan id
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nama_kategori' => 'required|string|max:255',
                'deskripsi' => 'nullable|string',
                'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'item_ids' => 'nullable|array',
                'item_ids.*' => 'exists:items,id'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $kategori = Kategori::find($id);
            
            if (!$kategori) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kategori tidak ditemukan'
                ], 404);
            }
            
            DB::beginTransaction();
            
            $kategori->nama_kategori = $request->nama_kategori;
            $kategori->deskripsi = $request->deskripsi;
            
            // Upload gambar baru jika ada
            if ($request->hasFile('gambar') && $request->file('gambar')->isValid()) {
                $file = $request->file('gambar');
                $fileName = Str::random(20) . '.' . $file->getClientOriginalExtension();
                $directoryPath = 'kategori-images';
                
                $fullDirectoryPath = storage_path('app/public/' . $directoryPath);
                if (!File::isDirectory($fullDirectoryPath)) {
                    File::makeDirectory($fullDirectoryPath, 0755, true);
                }
                
                // Hapus gambar lama jika ada
                if ($kategori->gambar) {
                    $oldImagePath = storage_path('app/public/' . $kategori->gambar);
                    if (File::exists($oldImagePath)) {
                        File::delete($oldImagePath);
                    }
                }
                
                $uploadSuccess = $file->move($fullDirectoryPath, $fileName);
                if ($uploadSuccess) {
                    $kategori->gambar = $directoryPath . '/' . $fileName;
                }
            }
            
            $kategori->save();
            
            // Update relasi dengan item
            if ($request->has('item_ids')) {
                $kategori->items()->sync($request->item_ids);
            }
            
            DB::commit();
            
            // Refresh model untuk mendapatkan relasi
            $kategori->load('items');
            
            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil diperbarui',
                'kategori' => $kategori
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('API Error: Gagal memperbarui kategori - ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui kategori',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menghapus kategori berdasarkan id
     */
    public function destroy($id)
    {
        try {
            // Gunakan transaction untuk memastikan semua operasi berhasil atau tidak sama sekali
            return DB::transaction(function() use ($id) {
                $kategori = Kategori::find($id);
                
                if (!$kategori) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Kategori tidak ditemukan'
                    ], 404);
                }
                
                // Simpan informasi untuk log
                $kategoriInfo = [
                    'id' => $kategori->id,
                    'nama_kategori' => $kategori->nama_kategori
                ];
                
                // Hapus relasi dengan item terlebih dahulu (pivot table)
                $kategori->items()->detach();
                
                // Hapus gambar jika ada
                if ($kategori->gambar) {
                    $imagePath = storage_path('app/public/' . $kategori->gambar);
                    if (File::exists($imagePath)) {
                        File::delete($imagePath);
                    }
                }
                
                // Hapus kategori
                $kategori->delete();
                
                // Log aktivitas
                Log::info('Kategori berhasil dihapus', [
                    'kategori_info' => $kategoriInfo,
                    'deleted_by' => auth()->user()->id ?? 'Unknown'
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Kategori berhasil dihapus'
                ]);
            });
        } catch (\Exception $e) {
            Log::error('API Error: Gagal menghapus kategori - ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus kategori: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan semua item berdasarkan kategori
     */
    public function getItemsByKategori($id)
    {
        try {
            $kategori = Kategori::find($id);
            
            if (!$kategori) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kategori tidak ditemukan'
                ], 404);
            }
            
            $items = $kategori->items;
            
            return response()->json([
                'success' => true,
                'kategori' => $kategori->nama_kategori,
                'items' => $items
            ]);
        } catch (\Exception $e) {
            Log::error('API Error: Gagal mendapatkan item berdasarkan kategori - ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data item berdasarkan kategori',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}