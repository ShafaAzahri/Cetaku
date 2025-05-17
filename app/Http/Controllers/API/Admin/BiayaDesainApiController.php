<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\BiayaDesain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BiayaDesainApiController extends Controller
{
    /**
     * Menampilkan semua biaya desain
     */
    public function index()
    {
        $biayaDesains = BiayaDesain::all();
        return response()->json([
            'success' => true,
            'biaya_desains' => $biayaDesains
        ]);
    }

    /**
     * Menyimpan biaya desain baru
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'biaya' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string'
        ]);
        
        $biayaDesain = new BiayaDesain();
        $biayaDesain->biaya = $request->biaya;
        $biayaDesain->deskripsi = $request->deskripsi;
        $biayaDesain->save();
        
        Log::info('API: Biaya desain berhasil disimpan', ['id' => $biayaDesain->id, 'biaya' => $biayaDesain->biaya]);
        
        return response()->json([
            'success' => true,
            'message' => 'Biaya desain berhasil ditambahkan',
            'biaya_desain' => $biayaDesain
        ], 201);
    }

    /**
     * Menampilkan biaya desain berdasarkan id
     */
    public function show($id)
    {
        $biayaDesain = BiayaDesain::find($id);
        
        if (!$biayaDesain) {
            return response()->json([
                'success' => false,
                'message' => 'Biaya desain tidak ditemukan'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'biaya_desain' => $biayaDesain
        ]);
    }

    /**
     * Memperbarui biaya desain berdasarkan id
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'biaya' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string'
        ]);
        
        $biayaDesain = BiayaDesain::find($id);
        
        if (!$biayaDesain) {
            return response()->json([
                'success' => false,
                'message' => 'Biaya desain tidak ditemukan'
            ], 404);
        }
        
        $biayaDesain->biaya = $request->biaya;
        $biayaDesain->deskripsi = $request->deskripsi;
        $biayaDesain->save();

        return response()->json([
            'success' => true,
            'message' => 'Biaya desain berhasil diperbarui',
            'biaya_desain' => $biayaDesain
        ]);
    }

    /**
     * Menghapus biaya desain berdasarkan id
     */
    public function destroy($id)
    {
        $biayaDesain = BiayaDesain::find($id);
        
        if (!$biayaDesain) {
            return response()->json([
                'success' => false,
                'message' => 'Biaya desain tidak ditemukan'
            ], 404);
        }
        
        $biayaDesain->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Biaya desain berhasil dihapus'
        ]);
    }
}