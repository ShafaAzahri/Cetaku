<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
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
            // Log gambar info jika ada
            if ($request->hasFile('gambar')) {
                $file = $request->file('gambar');
                // Tidak ada log
            } else {
                // Tidak ada log
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
                $file = $request->file('gambar');
                $fileName = Str::random(20) . '.' . $file->getClientOriginalExtension();
                $directoryPath = 'product-images';

                $fullDirectoryPath = storage_path('app/public/' . $directoryPath);
                if (!File::isDirectory($fullDirectoryPath)) {
                    File::makeDirectory($fullDirectoryPath, 0755, true);
                }

                $uploadSuccess = $file->move($fullDirectoryPath, $fileName);
                if ($uploadSuccess) {
                    $item->gambar = $directoryPath . '/' . $fileName;
                } else {
                    // Tidak ada log
                }
            }

            $item->save();

            return response()->json([
                'success' => true,
                'message' => 'Item berhasil ditambahkan',
                'item' => $item
            ], 201);
        } catch (\Exception $e) {
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
                $file = $request->file('gambar');
                $fileName = Str::random(20) . '.' . $file->getClientOriginalExtension();
                $directoryPath = 'product-images';

                $fullDirectoryPath = storage_path('app/public/' . $directoryPath);
                if (!File::isDirectory($fullDirectoryPath)) {
                    File::makeDirectory($fullDirectoryPath, 0755, true);
                }

                // Delete old image if exists
                if ($item->gambar) {
                    $oldImagePath = storage_path('app/public/' . $item->gambar);
                    if (File::exists($oldImagePath)) {
                        File::delete($oldImagePath);
                    }
                }

                $uploadSuccess = $file->move($fullDirectoryPath, $fileName);
                if ($uploadSuccess) {
                    $item->gambar = $directoryPath . '/' . $fileName;
                } else {
                    // Tidak ada log
                }
            }

            $item->save();

            return response()->json([
                'success' => true,
                'message' => 'Item berhasil diperbarui',
                'item' => $item
            ]);
        } catch (\Exception $e) {
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
                    return response()->json([
                        'success' => false,
                        'message' => 'Item tidak dapat dihapus karena sudah digunakan dalam pesanan'
                    ], 400);
                }

                // Hapus relasi dengan bahan, ukuran, dan jenis terlebih dahulu
                $item->bahans()->detach();
                $item->ukurans()->detach();
                $item->jenis()->detach();

                // Hapus gambar jika ada
                if ($item->gambar) {
                    $imagePath = storage_path('app/public/' . $item->gambar);
                    if (File::exists($imagePath)) {
                        File::delete($imagePath);
                    }
                }

                // Hapus item
                $item->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Item berhasil dihapus'
                ]);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus item: ' . $e->getMessage()
            ], 500);
        }
    }
}
