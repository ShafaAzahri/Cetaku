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
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Check admin access
        $checkResult = $this->checkAdmin();
        if ($checkResult) return $checkResult;
        
        // Get all data needed for the product management page
        $items = Item::with(['jenis', 'bahans', 'ukurans'])->get();
        $bahans = Bahan::all();
        $ukurans = Ukuran::all();
        $jenis = Jenis::all();
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
            'jenis_id' => 'required|exists:jenis,id',
            'harga_dasar' => 'required|numeric|min:0',
            'bahan_ids' => 'array',
            'bahan_ids.*' => 'exists:bahans,id',
            'ukuran_ids' => 'array',
            'ukuran_ids.*' => 'exists:ukurans,id',
        ]);
        
        $item = Item::create([
            'nama_item' => $request->nama_item,
            'deskripsi' => $request->deskripsi,
            'jenis_id' => $request->jenis_id,
            'harga_dasar' => $request->harga_dasar
        ]);
        
        // Attach bahans if provided
        if ($request->has('bahan_ids')) {
            $item->bahans()->attach($request->bahan_ids);
        }
        
        // Attach ukurans if provided
        if ($request->has('ukuran_ids')) {
            $item->ukurans()->attach($request->ukuran_ids);
        }
        
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
            'jenis_id' => 'required|exists:jenis,id',
            'harga_dasar' => 'required|numeric|min:0',
            'bahan_ids' => 'array',
            'bahan_ids.*' => 'exists:bahans,id',
            'ukuran_ids' => 'array',
            'ukuran_ids.*' => 'exists:ukurans,id',
        ]);
        
        $item = Item::findOrFail($id);
        
        $item->update([
            'nama_item' => $request->nama_item,
            'deskripsi' => $request->deskripsi,
            'jenis_id' => $request->jenis_id,
            'harga_dasar' => $request->harga_dasar
        ]);
        
        // Sync bahans
        if ($request->has('bahan_ids')) {
            $item->bahans()->sync($request->bahan_ids);
        } else {
            $item->bahans()->detach();
        }
        
        // Sync ukurans
        if ($request->has('ukuran_ids')) {
            $item->ukurans()->sync($request->ukuran_ids);
        } else {
            $item->ukurans()->detach();
        }
        
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
            'nama_bahan' => 'required|string|max:255',
            'biaya_tambahan' => 'required|numeric|min:0'
        ]);
        
        Bahan::create([
            'nama_bahan' => $request->nama_bahan,
            'biaya_tambahan' => $request->biaya_tambahan
        ]);
        
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
            'nama_bahan' => 'required|string|max:255',
            'biaya_tambahan' => 'required|numeric|min:0'
        ]);
        
        $bahan = Bahan::findOrFail($id);
        
        $bahan->update([
            'nama_bahan' => $request->nama_bahan,
            'biaya_tambahan' => $request->biaya_tambahan
        ]);
        
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
        
        // Check if the bahan is used in any products
        if ($bahan->items()->count() > 0) {
            return redirect()->route('admin.product-manager')
                ->with('error', 'Bahan tidak dapat dihapus karena masih digunakan dalam produk');
        }
        
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
            'size' => 'required|string|max:100',
            'faktor_harga' => 'required|numeric|min:0'
        ]);
        
        Ukuran::create([
            'size' => $request->size,
            'faktor_harga' => $request->faktor_harga
        ]);
        
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
            'size' => 'required|string|max:100',
            'faktor_harga' => 'required|numeric|min:0'
        ]);
        
        $ukuran = Ukuran::findOrFail($id);
        
        $ukuran->update([
            'size' => $request->size,
            'faktor_harga' => $request->faktor_harga
        ]);
        
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
        
        // Check if the ukuran is used in any products
        if ($ukuran->items()->count() > 0) {
            return redirect()->route('admin.product-manager')
                ->with('error', 'Ukuran tidak dapat dihapus karena masih digunakan dalam produk');
        }
        
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
            'kategori' => 'required|string|max:255',
            'biaya_tambahan' => 'required|numeric|min:0'
        ]);
        
        Jenis::create([
            'kategori' => $request->kategori,
            'biaya_tambahan' => $request->biaya_tambahan
        ]);
        
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
            'kategori' => 'required|string|max:255',
            'biaya_tambahan' => 'required|numeric|min:0'
        ]);
        
        $jenis = Jenis::findOrFail($id);
        
        $jenis->update([
            'kategori' => $request->kategori,
            'biaya_tambahan' => $request->biaya_tambahan
        ]);
        
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
        
        // Check if the jenis is used in any products
        if ($jenis->items()->count() > 0) {
            return redirect()->route('admin.product-manager')
                ->with('error', 'Jenis tidak dapat dihapus karena masih digunakan dalam produk');
        }
        
        $jenis->delete();
        
        return redirect()->route('admin.product-manager')
            ->with('success', 'Jenis berhasil dihapus');
    }
    
    /**
     * Store a new biaya desain.
     */
    public function storeBiayaDesain(Request $request)
    {
        // Check admin access
        $checkResult = $this->checkAdmin();
        if ($checkResult) return $checkResult;
        
        $request->validate([
            'nama_tingkat' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'biaya' => 'required|numeric|min:0'
        ]);
        
        BiayaDesain::create([
            'nama_tingkat' => $request->nama_tingkat,
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
            'nama_tingkat' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'biaya' => 'required|numeric|min:0'
        ]);
        
        $biayaDesain = BiayaDesain::findOrFail($id);
        
        $biayaDesain->update([
            'nama_tingkat' => $request->nama_tingkat,
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