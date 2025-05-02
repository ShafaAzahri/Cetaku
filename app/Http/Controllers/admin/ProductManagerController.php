<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Bahan;
use App\Models\Ukuran;
use App\Models\Jenis;
use App\Models\BiayaDesain;
use App\Models\ItemBahan;
use App\Models\ItemUkuran;
use App\Models\ItemJenis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductManagerController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'items');
        $page = $request->get('page', 1);
        
        $data = [];
        
        switch ($tab) {
            case 'items':
                $data['items'] = Item::paginate(10);
                break;
                
            case 'bahans':
                $data['bahans'] = Bahan::with('items')->paginate(10);
                $data['itemsDropdown'] = Item::orderBy('nama_item')->get();
                break;
                
            case 'ukurans':
                $data['ukurans'] = Ukuran::with('items')->paginate(10);
                $data['itemsDropdown'] = Item::orderBy('nama_item')->get();
                break;
                
            case 'jenis':
                $data['jenis'] = Jenis::with('items')->paginate(10);
                $data['itemsDropdown'] = Item::orderBy('nama_item')->get();
                break;
                
            case 'biaya-desain':
                $data['biayaDesain'] = BiayaDesain::paginate(10);
                break;
        }
        
        $data['activeTab'] = $tab;
        
        return view('admin.product-manager', $data);
    }
    
    // Items Methods
    public function storeItem(Request $request)
    {
        $validated = $request->validate([
            'nama_item' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga_dasar' => 'required|numeric|min:0',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        if ($request->hasFile('gambar')) {
            $validated['gambar'] = $request->file('gambar')->store('product-images', 'public');
        }
        
        Item::create($validated);
        
        return redirect()->route('admin.product-manager', ['tab' => 'items'])
            ->with('success', 'Produk berhasil ditambahkan');
    }
    
    public function updateItem(Request $request, $id)
    {
        $item = Item::findOrFail($id);
        
        $validated = $request->validate([
            'nama_item' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga_dasar' => 'required|numeric|min:0',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        if ($request->hasFile('gambar')) {
            // Delete old image
            if ($item->gambar) {
                Storage::disk('public')->delete($item->gambar);
            }
            $validated['gambar'] = $request->file('gambar')->store('product-images', 'public');
        }
        
        $item->update($validated);
        
        return redirect()->route('admin.product-manager', ['tab' => 'items'])
            ->with('success', 'Produk berhasil diperbarui');
    }
    
    public function destroyItem($id)
    {
        $item = Item::findOrFail($id);
        
        // Delete related records
        ItemBahan::where('item_id', $id)->delete();
        ItemUkuran::where('item_id', $id)->delete();
        ItemJenis::where('item_id', $id)->delete();
        
        // Delete image if exists
        if ($item->gambar) {
            Storage::disk('public')->delete($item->gambar);
        }
        
        $item->delete();
        
        return redirect()->route('admin.product-manager', ['tab' => 'items'])
            ->with('success', 'Produk berhasil dihapus');
    }
    
    // Bahan Methods
    public function storeBahan(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'nama_bahan' => 'required|string|max:255',
            'biaya_tambahan' => 'required|numeric|min:0',
            'is_available' => 'boolean',
        ]);
        
        $bahan = Bahan::create([
            'nama_bahan' => $validated['nama_bahan'],
            'biaya_tambahan' => $validated['biaya_tambahan'],
            'is_available' => $request->has('is_available') ? 1 : 0,
        ]);
        
        ItemBahan::create([
            'item_id' => $validated['item_id'],
            'bahan_id' => $bahan->id
        ]);
        
        return redirect()->route('admin.product-manager', ['tab' => 'bahans'])
            ->with('success', 'Bahan berhasil ditambahkan');
    }
    
    public function updateBahan(Request $request, $id)
    {
        $bahan = Bahan::findOrFail($id);
        
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'nama_bahan' => 'required|string|max:255',
            'biaya_tambahan' => 'required|numeric|min:0',
            'is_available' => 'boolean',
        ]);
        
        $bahan->update([
            'nama_bahan' => $validated['nama_bahan'],
            'biaya_tambahan' => $validated['biaya_tambahan'],
            'is_available' => $request->has('is_available') ? 1 : 0,
        ]);
        
        // Update item association
        ItemBahan::where('bahan_id', $bahan->id)->delete();
        ItemBahan::create([
            'item_id' => $validated['item_id'],
            'bahan_id' => $bahan->id
        ]);
        
        return redirect()->route('admin.product-manager', ['tab' => 'bahans'])
            ->with('success', 'Bahan berhasil diperbarui');
    }
    
    public function destroyBahan($id)
    {
        $bahan = Bahan::findOrFail($id);
        
        // Delete associations
        ItemBahan::where('bahan_id', $id)->delete();
        
        $bahan->delete();
        
        return redirect()->route('admin.product-manager', ['tab' => 'bahans'])
            ->with('success', 'Bahan berhasil dihapus');
    }
    
    // Ukuran Methods
    public function storeUkuran(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'size' => 'required|string|max:100',
            'faktor_harga' => 'required|numeric|min:0.1',
        ]);
        
        $ukuran = Ukuran::create([
            'size' => $validated['size'],
            'faktor_harga' => $validated['faktor_harga'],
        ]);
        
        ItemUkuran::create([
            'item_id' => $validated['item_id'],
            'ukuran_id' => $ukuran->id
        ]);
        
        return redirect()->route('admin.product-manager', ['tab' => 'ukurans'])
            ->with('success', 'Ukuran berhasil ditambahkan');
    }
    
    public function updateUkuran(Request $request, $id)
    {
        $ukuran = Ukuran::findOrFail($id);
        
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'size' => 'required|string|max:100',
            'faktor_harga' => 'required|numeric|min:0.1',
        ]);
        
        $ukuran->update([
            'size' => $validated['size'],
            'faktor_harga' => $validated['faktor_harga'],
        ]);
        
        // Update item association
        ItemUkuran::where('ukuran_id', $ukuran->id)->delete();
        ItemUkuran::create([
            'item_id' => $validated['item_id'],
            'ukuran_id' => $ukuran->id
        ]);
        
        return redirect()->route('admin.product-manager', ['tab' => 'ukurans'])
            ->with('success', 'Ukuran berhasil diperbarui');
    }
    
    public function destroyUkuran($id)
    {
        $ukuran = Ukuran::findOrFail($id);
        
        // Delete associations
        ItemUkuran::where('ukuran_id', $id)->delete();
        
        $ukuran->delete();
        
        return redirect()->route('admin.product-manager', ['tab' => 'ukurans'])
            ->with('success', 'Ukuran berhasil dihapus');
    }
    
    // Jenis Methods
    public function storeJenis(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'kategori' => 'required|string|max:255',
            'biaya_tambahan' => 'required|numeric|min:0',
        ]);
        
        $jenis = Jenis::create([
            'kategori' => $validated['kategori'],
            'biaya_tambahan' => $validated['biaya_tambahan'],
        ]);
        
        ItemJenis::create([
            'item_id' => $validated['item_id'],
            'jenis_id' => $jenis->id
        ]);
        
        return redirect()->route('admin.product-manager', ['tab' => 'jenis'])
            ->with('success', 'Jenis berhasil ditambahkan');
    }
    
    public function updateJenis(Request $request, $id)
    {
        $jenis = Jenis::findOrFail($id);
        
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'kategori' => 'required|string|max:255',
            'biaya_tambahan' => 'required|numeric|min:0',
        ]);
        
        $jenis->update([
            'kategori' => $validated['kategori'],
            'biaya_tambahan' => $validated['biaya_tambahan'],
        ]);
        
        // Update item association
        ItemJenis::where('jenis_id', $jenis->id)->delete();
        ItemJenis::create([
            'item_id' => $validated['item_id'],
            'jenis_id' => $jenis->id
        ]);
        
        return redirect()->route('admin.product-manager', ['tab' => 'jenis'])
            ->with('success', 'Jenis berhasil diperbarui');
    }
    
    public function destroyJenis($id)
    {
        $jenis = Jenis::findOrFail($id);
        
        // Delete associations
        ItemJenis::where('jenis_id', $id)->delete();
        
        $jenis->delete();
        
        return redirect()->route('admin.product-manager', ['tab' => 'jenis'])
            ->with('success', 'Jenis berhasil dihapus');
    }
    
    // Biaya Desain Methods
    public function storeBiayaDesain(Request $request)
    {
        $validated = $request->validate([
            'biaya' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
        ]);
        
        BiayaDesain::create($validated);
        
        return redirect()->route('admin.product-manager', ['tab' => 'biaya-desain'])
            ->with('success', 'Biaya desain berhasil ditambahkan');
    }
    
    public function updateBiayaDesain(Request $request, $id)
    {
        $biayaDesain = BiayaDesain::findOrFail($id);
        
        $validated = $request->validate([
            'biaya' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
        ]);
        
        $biayaDesain->update($validated);
        
        return redirect()->route('admin.product-manager', ['tab' => 'biaya-desain'])
            ->with('success', 'Biaya desain berhasil diperbarui');
    }
    
    public function destroyBiayaDesain($id)
    {
        $biayaDesain = BiayaDesain::findOrFail($id);
        $biayaDesain->delete();
        
        return redirect()->route('admin.product-manager', ['tab' => 'biaya-desain'])
            ->with('success', 'Biaya desain berhasil dihapus');
    }
}