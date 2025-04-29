<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Item;
use App\Models\Bahan;
use App\Models\Ukuran;
use App\Models\Jenis;
use App\Models\BiayaDesain;
use App\Models\ItemBahan;
use App\Models\ItemUkuran;

class ProductApiController extends Controller
{
    /**
     * Display a listing of the items.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAllItems()
    {
        try {
            // Ambil item dengan relasi (bahans, ukurans, jenis)
            $items = Item::with(['bahans', 'ukurans', 'jenis'])->get();
            
            return response()->json([
                'success' => true,
                'data' => $items
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Store a newly created item.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeItem(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_item' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'jenis_id' => 'required|exists:jenis,id',
            'harga_dasar' => 'required|numeric|min:0',
            'bahan_ids' => 'array',
            'bahan_ids.*' => 'exists:bahans,id',
            'ukuran_ids' => 'array',
            'ukuran_ids.*' => 'exists:ukurans,id',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Create the item
        $item = Item::create([
            'nama_item' => $request->nama_item,
            'deskripsi' => $request->deskripsi,
            'jenis_id' => $request->jenis_id,
            'harga_dasar' => $request->harga_dasar
        ]);
        
        // Attach bahans if provided
        if ($request->has('bahan_ids')) {
            foreach ($request->bahan_ids as $bahanId) {
                ItemBahan::create([
                    'item_id' => $item->id,
                    'bahan_id' => $bahanId
                ]);
            }
        }
        
        // Attach ukurans if provided
        if ($request->has('ukuran_ids')) {
            foreach ($request->ukuran_ids as $ukuranId) {
                ItemUkuran::create([
                    'item_id' => $item->id,
                    'ukuran_id' => $ukuranId
                ]);
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan',
            'data' => $item->load(['bahan', 'ukuran', 'jenis'])
        ], 201);
    }
    
    /**
     * Display the specified item.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getItem($id)
    {
        $item = Item::with(['bahan', 'ukuran', 'jenis'])->find($id);
        
        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $item
        ]);
    }
    
    /**
     * Update the specified item.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateItem(Request $request, $id)
    {
        $item = Item::find($id);
        
        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'nama_item' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'jenis_id' => 'required|exists:jenis,id',
            'harga_dasar' => 'required|numeric|min:0',
            'bahan_ids' => 'array',
            'bahan_ids.*' => 'exists:bahans,id',
            'ukuran_ids' => 'array',
            'ukuran_ids.*' => 'exists:ukurans,id',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Update the item
        $item->update([
            'nama_item' => $request->nama_item,
            'deskripsi' => $request->deskripsi,
            'jenis_id' => $request->jenis_id,
            'harga_dasar' => $request->harga_dasar
        ]);
        
        // Update bahans if provided
        if ($request->has('bahan_ids')) {
            // Remove existing relations
            ItemBahan::where('item_id', $item->id)->delete();
            
            // Add new relations
            foreach ($request->bahan_ids as $bahanId) {
                ItemBahan::create([
                    'item_id' => $item->id,
                    'bahan_id' => $bahanId
                ]);
            }
        }
        
        // Update ukurans if provided
        if ($request->has('ukuran_ids')) {
            // Remove existing relations
            ItemUkuran::where('item_id', $item->id)->delete();
            
            // Add new relations
            foreach ($request->ukuran_ids as $ukuranId) {
                ItemUkuran::create([
                    'item_id' => $item->id,
                    'ukuran_id' => $ukuranId
                ]);
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil diperbarui',
            'data' => $item->load(['bahan', 'ukuran', 'jenis'])
        ]);
    }
    
    /**
     * Remove the specified item.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteItem($id)
    {
        $item = Item::find($id);
        
        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }
        
        // Remove relations
        ItemBahan::where('item_id', $id)->delete();
        ItemUkuran::where('item_id', $id)->delete();
        
        // Delete the item
        $item->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil dihapus'
        ]);
    }
    
    /**
     * Display a listing of all bahan.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAllBahans()
    {
        try {
            // Ambil bahan dengan relasi items
            $bahans = Bahan::with('items')->get();
            
            // Tambahkan informasi item terkait
            $bahansWithItems = $bahans->map(function($bahan) {
                $itemNames = $bahan->items->pluck('nama_item')->join(', ');
                return [
                    'id' => $bahan->id,
                    'nama_bahan' => $bahan->nama_bahan,
                    'biaya_tambahan' => $bahan->biaya_tambahan,
                    'items' => $bahan->items,
                    'item_names' => $itemNames
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $bahansWithItems
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Store a newly created bahan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeBahan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_bahan' => 'required|string|max:255',
            'biaya_tambahan' => 'required|numeric|min:0'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $bahan = Bahan::create([
            'nama_bahan' => $request->nama_bahan,
            'biaya_tambahan' => $request->biaya_tambahan
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Bahan berhasil ditambahkan',
            'data' => $bahan
        ], 201);
    }
    
    /**
     * Display the specified bahan.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getBahan($id)
    {
        $bahan = Bahan::find($id);
        
        if (!$bahan) {
            return response()->json([
                'success' => false,
                'message' => 'Bahan tidak ditemukan'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $bahan
        ]);
    }
    
    /**
     * Update the specified bahan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateBahan(Request $request, $id)
    {
        $bahan = Bahan::find($id);
        
        if (!$bahan) {
            return response()->json([
                'success' => false,
                'message' => 'Bahan tidak ditemukan'
            ], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'nama_bahan' => 'required|string|max:255',
            'biaya_tambahan' => 'required|numeric|min:0'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $bahan->update([
            'nama_bahan' => $request->nama_bahan,
            'biaya_tambahan' => $request->biaya_tambahan
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Bahan berhasil diperbarui',
            'data' => $bahan
        ]);
    }
    
    /**
     * Remove the specified bahan.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteBahan($id)
    {
        $bahan = Bahan::find($id);
        
        if (!$bahan) {
            return response()->json([
                'success' => false,
                'message' => 'Bahan tidak ditemukan'
            ], 404);
        }
        
        // Check if bahan is used in any products
        $bahanInUse = ItemBahan::where('bahan_id', $id)->exists();
        
        if ($bahanInUse) {
            return response()->json([
                'success' => false,
                'message' => 'Bahan tidak dapat dihapus karena masih digunakan dalam produk'
            ], 422);
        }
        
        $bahan->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Bahan berhasil dihapus'
        ]);
    }
    
    /**
     * Display a listing of all ukuran.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAllUkurans()
    {
        try {
            // Ambil ukuran dengan relasi items
            $ukurans = Ukuran::with('items')->get();
            
            // Tambahkan informasi item terkait
            $ukuransWithItems = $ukurans->map(function($ukuran) {
                $itemNames = $ukuran->items->pluck('nama_item')->join(', ');
                return [
                    'id' => $ukuran->id,
                    'size' => $ukuran->size,
                    'faktor_harga' => $ukuran->faktor_harga,
                    'items' => $ukuran->items,
                    'item_names' => $itemNames
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $ukuransWithItems
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Store a newly created ukuran.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeUkuran(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'size' => 'required|string|max:100',
            'faktor_harga' => 'required|numeric|min:0'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $ukuran = Ukuran::create([
            'size' => $request->size,
            'faktor_harga' => $request->faktor_harga
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Ukuran berhasil ditambahkan',
            'data' => $ukuran
        ], 201);
    }
    
    /**
     * Display the specified ukuran.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getUkuran($id)
    {
        $ukuran = Ukuran::find($id);
        
        if (!$ukuran) {
            return response()->json([
                'success' => false,
                'message' => 'Ukuran tidak ditemukan'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $ukuran
        ]);
    }
    
    /**
     * Update the specified ukuran.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateUkuran(Request $request, $id)
    {
        $ukuran = Ukuran::find($id);
        
        if (!$ukuran) {
            return response()->json([
                'success' => false,
                'message' => 'Ukuran tidak ditemukan'
            ], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'size' => 'required|string|max:100',
            'faktor_harga' => 'required|numeric|min:0'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $ukuran->update([
            'size' => $request->size,
            'faktor_harga' => $request->faktor_harga
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Ukuran berhasil diperbarui',
            'data' => $ukuran
        ]);
    }
    
    /**
     * Remove the specified ukuran.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteUkuran($id)
    {
        $ukuran = Ukuran::find($id);
        
        if (!$ukuran) {
            return response()->json([
                'success' => false,
                'message' => 'Ukuran tidak ditemukan'
            ], 404);
        }
        
        // Check if ukuran is used in any products
        $ukuranInUse = ItemUkuran::where('ukuran_id', $id)->exists();
        
        if ($ukuranInUse) {
            return response()->json([
                'success' => false,
                'message' => 'Ukuran tidak dapat dihapus karena masih digunakan dalam produk'
            ], 422);
        }
        
        $ukuran->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Ukuran berhasil dihapus'
        ]);
    }
    
    /**
     * Display a listing of all jenis.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAllJenis()
    {
        try {
            // Ambil jenis dengan relasi items
            $jenis = Jenis::with('items')->get();
            
            // Tambahkan informasi item terkait
            $jenisWithItems = $jenis->map(function($j) {
                $itemNames = $j->items->pluck('nama_item')->join(', ');
                return [
                    'id' => $j->id,
                    'kategori' => $j->kategori,
                    'biaya_tambahan' => $j->biaya_tambahan,
                    'items' => $j->items,
                    'item_names' => $itemNames
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $jenisWithItems
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Store a newly created jenis.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeJenis(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kategori' => 'required|string|max:255',
            'biaya_tambahan' => 'required|numeric|min:0'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $jenis = Jenis::create([
            'kategori' => $request->kategori,
            'biaya_tambahan' => $request->biaya_tambahan
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Jenis berhasil ditambahkan',
            'data' => $jenis
        ], 201);
    }
    
    /**
     * Display the specified jenis.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getJenis($id)
    {
        $jenis = Jenis::find($id);
        
        if (!$jenis) {
            return response()->json([
                'success' => false,
                'message' => 'Jenis tidak ditemukan'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $jenis
        ]);
    }
    
    /**
     * Update the specified jenis.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateJenis(Request $request, $id)
    {
        $jenis = Jenis::find($id);
        
        if (!$jenis) {
            return response()->json([
                'success' => false,
                'message' => 'Jenis tidak ditemukan'
            ], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'kategori' => 'required|string|max:255',
            'biaya_tambahan' => 'required|numeric|min:0'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $jenis->update([
            'kategori' => $request->kategori,
            'biaya_tambahan' => $request->biaya_tambahan
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Jenis berhasil diperbarui',
            'data' => $jenis
        ]);
    }
    
    /**
     * Remove the specified jenis.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteJenis($id)
    {
        $jenis = Jenis::find($id);
        
        if (!$jenis) {
            return response()->json([
                'success' => false,
                'message' => 'Jenis tidak ditemukan'
            ], 404);
        }
        
        // Check if jenis is used in any products
        $jenisInUse = Item::where('jenis_id', $id)->exists();
        
        if ($jenisInUse) {
            return response()->json([
                'success' => false,
                'message' => 'Jenis tidak dapat dihapus karena masih digunakan dalam produk'
            ], 422);
        }
        
        $jenis->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Jenis berhasil dihapus'
        ]);
    }
    
    /**
     * Display a listing of all biaya desain.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAllBiayaDesain()
    {
        $biayaDesain = BiayaDesain::all();
        
        return response()->json([
            'success' => true,
            'data' => $biayaDesain
        ]);
    }
    
    /**
     * Store a newly created biaya desain.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeBiayaDesain(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'deskripsi' => 'nullable|string',
            'biaya' => 'required|numeric|min:0'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $biayaDesain = BiayaDesain::create([
            'deskripsi' => $request->deskripsi,
            'biaya' => $request->biaya
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Biaya Desain berhasil ditambahkan',
            'data' => $biayaDesain
        ], 201);
    }

    /**
     * Display the specified biaya desain.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getBiayaDesain($id)
    {
        $biayaDesain = BiayaDesain::find($id);
        
        if (!$biayaDesain) {
            return response()->json([
                'success' => false,
                'message' => 'Biaya Desain tidak ditemukan'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $biayaDesain
        ]);
    }
    
    /**
     * Update the specified biaya desain.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateBiayaDesain(Request $request, $id)
    {
        $biayaDesain = BiayaDesain::find($id);
        
        if (!$biayaDesain) {
            return response()->json([
                'success' => false,
                'message' => 'Biaya Desain tidak ditemukan'
            ], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'deskripsi' => 'nullable|string',
            'biaya' => 'required|numeric|min:0'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $biayaDesain->update([
            'deskripsi' => $request->deskripsi,
            'biaya' => $request->biaya
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Biaya Desain berhasil diperbarui',
            'data' => $biayaDesain
        ]);
    }
    
    /**
     * Remove the specified biaya desain.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteBiayaDesain($id)
    {
        $biayaDesain = BiayaDesain::find($id);
        
        if (!$biayaDesain) {
            return response()->json([
                'success' => false,
                'message' => 'Biaya Desain tidak ditemukan'
            ], 404);
        }
        
        $biayaDesain->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Biaya Desain berhasil dihapus'
        ]);
    }
    
    /**
     * Calculate product price based on selections.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function calculatePrice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_id' => 'required|exists:items,id',
            'bahan_id' => 'nullable|exists:bahans,id',
            'ukuran_id' => 'nullable|exists:ukurans,id',
            'jenis_id' => 'nullable|exists:jenis,id',
            'biaya_desain_id' => 'nullable|exists:biaya_desains,id',
            'tipe_desain' => 'nullable|in:sendiri,dibuatkan'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Get item details
        $item = Item::find($request->item_id);
        $hargaDasar = $item->harga_dasar;
        $namaItem = $item->nama_item;
        
        // Get bahan details
        $biayaBahan = 0;
        $namaBahan = null;
        if ($request->has('bahan_id') && $request->bahan_id) {
            $bahan = Bahan::find($request->bahan_id);
            if ($bahan) {
                $biayaBahan = $bahan->biaya_tambahan;
                $namaBahan = $bahan->nama_bahan;
            }
        }
        
        // Get ukuran details
        $faktorUkuran = 1;
        $namaUkuran = null;
        if ($request->has('ukuran_id') && $request->ukuran_id) {
            $ukuran = Ukuran::find($request->ukuran_id);
            if ($ukuran) {
                $faktorUkuran = $ukuran->faktor_harga;
                $namaUkuran = $ukuran->size;
            }
        }
        
        // Get jenis details
        $biayaJenis = 0;
        $namaJenis = null;
        if ($request->has('jenis_id') && $request->jenis_id) {
            $jenis = Jenis::find($request->jenis_id);
            if ($jenis) {
                $biayaJenis = $jenis->biaya_tambahan;
                $namaJenis = $jenis->kategori;
            }
        }
        
        // Get design cost details
        $biayaDesain = 0;
        if ($request->has('tipe_desain') && $request->tipe_desain === 'dibuatkan' && $request->has('biaya_desain_id')) {
            $desain = BiayaDesain::find($request->biaya_desain_id);
            if ($desain) {
                $biayaDesain = $desain->biaya;
            }
        }
        
        // Calculate total price
        $totalHarga = ($hargaDasar + $biayaBahan + $biayaJenis) * $faktorUkuran;
        $hargaTotal = $totalHarga + $biayaDesain;
        
        return response()->json([
            'success' => true,
            'data' => [
                'item' => [
                    'id' => $item->id,
                    'nama' => $namaItem,
                    'harga_dasar' => $hargaDasar
                ],
                'bahan' => $request->bahan_id ? [
                    'id' => $request->bahan_id,
                    'nama' => $namaBahan,
                    'biaya' => $biayaBahan
                ] : null,
                'ukuran' => $request->ukuran_id ? [
                    'id' => $request->ukuran_id,
                    'nama' => $namaUkuran,
                    'faktor' => $faktorUkuran
                ] : null,
                'jenis' => $request->jenis_id ? [
                    'id' => $request->jenis_id,
                    'nama' => $namaJenis,
                    'biaya' => $biayaJenis
                ] : null,
                'desain' => ($request->tipe_desain === 'dibuatkan' && $request->biaya_desain_id) ? [
                    'id' => $request->biaya_desain_id,
                    'biaya' => $biayaDesain
                ] : null,
                'subtotal' => $totalHarga,
                'harga_total' => $hargaTotal
            ]
        ]);
    }
}