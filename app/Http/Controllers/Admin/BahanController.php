<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Bahan;
use App\Models\ItemBahan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;

class BahanController extends Controller
{
    /**
     * Check if user is admin and return error if not
     *
     * @return RedirectResponse|null
     */
    private function checkAdmin()
    {
        if (!Auth::check() || !Auth::user()->role || Auth::user()->role->nama_role !== 'admin') {
            return redirect()->route('welcome')->with('error', 'Unauthorized access');
        }
        
        return null;
    }
    
    /**
     * Display a listing of materials.
     *
     * @return View|RedirectResponse
     */
    public function index()
    {
        // Check admin access
        $checkResult = $this->checkAdmin();
        if ($checkResult) return $checkResult;
        
        $bahans = Bahan::all();
        
        return view('admin.bahans.index', compact('bahans'));
    }
    
    /**
     * Show the form for creating a new material.
     *
     * @return View|RedirectResponse
     */
    public function create()
    {
        // Check admin access
        $checkResult = $this->checkAdmin();
        if ($checkResult) return $checkResult;
        
        return view('admin.bahans.create');
    }
    
    /**
     * Store a newly created material in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return RedirectResponse
     */
    public function store(Request $request)
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
        
        return redirect()->route('admin.bahans.index')
            ->with('success', 'Bahan berhasil ditambahkan');
    }
    
    /**
     * Show the form for editing the specified material.
     *
     * @param  int  $id
     * @return View|RedirectResponse
     */
    public function edit($id)
    {
        // Check admin access
        $checkResult = $this->checkAdmin();
        if ($checkResult) return $checkResult;
        
        $bahan = Bahan::findOrFail($id);
        
        return view('admin.bahans.edit', compact('bahan'));
    }
    
    /**
     * Update the specified material in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id)
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
        
        return redirect()->route('admin.bahans.index')
            ->with('success', 'Bahan berhasil diperbarui');
    }
    
    /**
     * Remove the specified material from storage.
     *
     * @param  int  $id
     * @return RedirectResponse
     */
    public function destroy($id)
    {
        // Check admin access
        $checkResult = $this->checkAdmin();
        if ($checkResult) return $checkResult;
        
        $bahan = Bahan::findOrFail($id);
        
        // Check if the material is used in any products
        $bahanInUse = ItemBahan::where('bahan_id', $id)->exists();
        
        if ($bahanInUse) {
            return redirect()->route('admin.bahans.index')
                ->with('error', 'Bahan tidak dapat dihapus karena masih digunakan dalam produk');
        }
        
        $bahan->delete();
        
        return redirect()->route('admin.bahans.index')
            ->with('success', 'Bahan berhasil dihapus');
    }
}