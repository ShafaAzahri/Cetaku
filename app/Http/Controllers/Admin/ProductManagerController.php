<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Bahan;
use App\Models\Ukuran;
use App\Models\Jenis;
use App\Models\BiayaDesain;
use App\Models\ItemBahan; // Tambahkan import ini
use App\Models\ItemUkuran; // Tambahkan import ini juga untuk nanti
use App\Models\ItemJenis; // Tambahkan import ini juga untuk nanti

class ProductManagerController extends Controller
{
    /**
     * Check if user is admin and return error if not
     */
    private function checkAdmin()
    {
        if (!Auth::check() || !Auth::user()->role || Auth::user()->role->nama_role !== 'admin') {
            return redirect()->route('welcome')->with('error', 'Unauthorized access');
        }
        
        return null;
    }
    
    /**
     * Display the product management dashboard.
     */
    public function index()
    {
        // Check admin access
        $checkResult = $this->checkAdmin();
        if ($checkResult) return $checkResult;
        
        // Get all data needed for the product management page
        $items = Item::with(['bahans', 'ukurans', 'jenis'])->get();
        $bahans = Bahan::with('items')->get();
        $ukurans = Ukuran::with('items')->get();
        $jenis = Jenis::with('items')->get();
        $biayaDesains = BiayaDesain::all();
        
        return view('admin.product-manager', compact(
            'items', 'bahans', 'ukurans', 'jenis', 'biayaDesains'
        ));
    }
    
    /**
     * Store a new product.
     */
    public function storeItem(Request $request)
    {
        // Check admin access
        $checkResult = $this->checkAdmin();
        if ($checkResult) return $checkResult;
        
        $request->validate([
            'nama_item' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga_dasar' => 'required|numeric|min:0',
        ]);
        
        $item = Item::create([
            'nama_item' => $request->nama_item,
            'deskripsi' => $request->deskripsi,
            'harga_dasar' => $request->harga_dasar
        ]);
        
        return redirect()->route('admin.product-manager')
            ->with('success', 'Produk berhasil ditambahkan');
    }
    
    /**
     * Update an existing product.
     */
    public function updateItem(Request $request, $id)
    {
        // Check admin access
        $checkResult = $this->checkAdmin();
        if ($checkResult) return $checkResult;
        
        $request->validate([
            'nama_item' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga_dasar' => 'required|numeric|min:0',
        ]);
        
        $item = Item::findOrFail($id);
        
        $item->update([
            'nama_item' => $request->nama_item,
            'deskripsi' => $request->deskripsi,
            'harga_dasar' => $request->harga_dasar
        ]);
        
        return redirect()->route('admin.product-manager')
            ->with('success', 'Produk berhasil diperbarui');
    }
    
    /**
     * Delete a product.
     */
    public function deleteItem($id)
    {
        // Check admin access
        $checkResult = $this->checkAdmin();
        if ($checkResult) return $checkResult;
        
        $item = Item::findOrFail($id);
        
        // Detach relations
        $item->bahans()->detach();
        $item->ukurans()->detach();
        $item->jenis()->detach();
        
        $item->delete();
        
        return redirect()->route('admin.product-manager')
            ->with('success', 'Produk berhasil dihapus');
    }
    
    /**
     * Store a new bahan.
     */
    public function storeBahan(Request $request)
    {
        // Check admin access
        $checkResult = $this->checkAdmin();
        if ($checkResult) return $checkResult;
        
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'nama_bahan' => 'required|string|max:255',
            'biaya_tambahan' => 'required|numeric|min:0'
        ]);
        
        $bahan = Bahan::create([
            'nama_bahan' => $request->nama_bahan,
            'biaya_tambahan' => $request->biaya_tambahan
        ]);
        
        // Attach bahan to the selected item
        $item = Item::find($request->item_id);
        $item->bahans()->attach($bahan->id);
        
        return redirect()->route('admin.product-manager')
            ->with('success', 'Bahan berhasil ditambahkan');
    }
    
    /**
     * Update an existing bahan.
     */
    public function updateBahan(Request $request, $id)
    {
        // Check admin access
        $checkResult = $this->checkAdmin();
        if ($checkResult) return $checkResult;
        
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'nama_bahan' => 'required|string|max:255',
            'biaya_tambahan' => 'required|numeric|min:0'
        ]);
        
        $bahan = Bahan::findOrFail($id);
        
        $bahan->update([
            'nama_bahan' => $request->nama_bahan,
            'biaya_tambahan' => $request->biaya_tambahan
        ]);
        
        // Update the item relation - first detach from all items
        ItemBahan::where('bahan_id', $id)->delete();
        
        // Then attach to the selected item
        $item = Item::find($request->item_id);
        $item->bahans()->attach($bahan->id);
        
        return redirect()->route('admin.product-manager')
            ->with('success', 'Bahan berhasil diperbarui');
    }
    
    /**
     * Delete a bahan.
     */
    public function deleteBahan($id)
    {
        // Check admin access
        $checkResult = $this->checkAdmin();
        if ($checkResult) return $checkResult;
        
        $bahan = Bahan::findOrFail($id);
        
        // Remove all association with items
        ItemBahan::where('bahan_id', $id)->delete();
        
        $bahan->delete();
        
        return redirect()->route('admin.product-manager')
            ->with('success', 'Bahan berhasil dihapus');
    }
    
    /**
     * Store a new ukuran.
     */
    public function storeUkuran(Request $request)
    {
        // Check admin access
        $checkResult = $this->checkAdmin();
        if ($checkResult) return $checkResult;
        
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'size' => 'required|string|max:100',
            'faktor_harga' => 'required|numeric|min:0'
        ]);
        
        $ukuran = Ukuran::create([
            'size' => $request->size,
            'faktor_harga' => $request->faktor_harga
        ]);
        
        // Attach ukuran to the selected item
        $item = Item::find($request->item_id);
        $item->ukurans()->attach($ukuran->id);
        
        return redirect()->route('admin.product-manager')
            ->with('success', 'Ukuran berhasil ditambahkan');
    }
    
    /**
     * Update an existing ukuran.
     */
    public function updateUkuran(Request $request, $id)
    {
        // Check admin access
        $checkResult = $this->checkAdmin();
        if ($checkResult) return $checkResult;
        
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'size' => 'required|string|max:100',
            'faktor_harga' => 'required|numeric|min:0'
        ]);
        
        $ukuran = Ukuran::findOrFail($id);
        
        $ukuran->update([
            'size' => $request->size,
            'faktor_harga' => $request->faktor_harga
        ]);
        
        // Update the item relation - first detach from all items
        ItemUkuran::where('ukuran_id', $id)->delete();
        
        // Then attach to the selected item
        $item = Item::find($request->item_id);
        $item->ukurans()->attach($ukuran->id);
        
        return redirect()->route('admin.product-manager')
            ->with('success', 'Ukuran berhasil diperbarui');
    }
    
    /**
     * Delete an ukuran.
     */
    public function deleteUkuran($id)
    {
        // Check admin access
        $checkResult = $this->checkAdmin();
        if ($checkResult) return $checkResult;
        
        $ukuran = Ukuran::findOrFail($id);
        
        // Remove all association with items
        ItemUkuran::where('ukuran_id', $id)->delete();
        
        $ukuran->delete();
        
        return redirect()->route('admin.product-manager')
            ->with('success', 'Ukuran berhasil dihapus');
    }
    
    /**
     * Store a new jenis.
     */
    public function storeJenis(Request $request)
    {
        // Check admin access
        $checkResult = $this->checkAdmin();
        if ($checkResult) return $checkResult;
        
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'kategori' => 'required|string|max:255',
            'biaya_tambahan' => 'required|numeric|min:0'
        ]);
        
        $jenis = Jenis::create([
            'kategori' => $request->kategori,
            'biaya_tambahan' => $request->biaya_tambahan
        ]);
        
        // Attach jenis to the selected item
        $item = Item::find($request->item_id);
        $item->jenis()->attach($jenis->id);
        
        return redirect()->route('admin.product-manager')
            ->with('success', 'Jenis berhasil ditambahkan');
    }
    
    /**
     * Update an existing jenis.
     */
    public function updateJenis(Request $request, $id)
    {
        // Check admin access
        $checkResult = $this->checkAdmin();
        if ($checkResult) return $checkResult;
        
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'kategori' => 'required|string|max:255',
            'biaya_tambahan' => 'required|numeric|min:0'
        ]);
        
        $jenis = Jenis::findOrFail($id);
        
        $jenis->update([
            'kategori' => $request->kategori,
            'biaya_tambahan' => $request->biaya_tambahan
        ]);
        
        // Update the item relation - first detach from all items
        ItemJenis::where('jenis_id', $id)->delete();
        
        // Then attach to the selected item
        $item = Item::find($request->item_id);
        $item->jenis()->attach($jenis->id);
        
        return redirect()->route('admin.product-manager')
            ->with('success', 'Jenis berhasil diperbarui');
    }
    
    /**
     * Delete a jenis.
     */
    public function deleteJenis($id)
    {
        // Check admin access
        $checkResult = $this->checkAdmin();
        if ($checkResult) return $checkResult;
        
        $jenis = Jenis::findOrFail($id);
        
        // Remove all association with items
        ItemJenis::where('jenis_id', $id)->delete();
        
        $jenis->delete();
        
        return redirect()->route('admin.product-manager')
            ->with('success', 'Jenis berhasil dihapus');
    }
    
    /**
     * Store a new biaya desain.
     */
    /**
     * Store a new biaya desain.
     */
    public function storeBiayaDesain(Request $request)
    {
        // Check admin access
        $checkResult = $this->checkAdmin();
        if ($checkResult) return $checkResult;
        
        $request->validate([
            'deskripsi' => 'nullable|string',
            'biaya' => 'required|numeric|min:0'
        ]);
        
        BiayaDesain::create([
            'deskripsi' => $request->deskripsi,
            'biaya' => $request->biaya
        ]);
        
        return redirect()->route('admin.product-manager')
            ->with('success', 'Biaya Desain berhasil ditambahkan');
    }

    /**
     * Update an existing biaya desain.
     */
    public function updateBiayaDesain(Request $request, $id)
    {
        // Check admin access
        $checkResult = $this->checkAdmin();
        if ($checkResult) return $checkResult;
        
        $request->validate([
            'deskripsi' => 'nullable|string',
            'biaya' => 'required|numeric|min:0'
        ]);
        
        $biayaDesain = BiayaDesain::findOrFail($id);
        
        $biayaDesain->update([
            'deskripsi' => $request->deskripsi,
            'biaya' => $request->biaya
        ]);
        
        return redirect()->route('admin.product-manager')
            ->with('success', 'Biaya Desain berhasil diperbarui');
    }
    
    /**
     * Delete a biaya desain.
     */
    public function deleteBiayaDesain($id)
    {
        // Check admin access
        $checkResult = $this->checkAdmin();
        if ($checkResult) return $checkResult;
        
        $biayaDesain = BiayaDesain::findOrFail($id);
        $biayaDesain->delete();
        
        return redirect()->route('admin.product-manager')
            ->with('success', 'Biaya Desain berhasil dihapus');
    }
}