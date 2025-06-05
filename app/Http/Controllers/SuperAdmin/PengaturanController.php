<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\TokoInfo; // Assuming you have a model for TokoInfo


class PengaturanController extends Controller
{
    protected $apiBaseUrl;
    public function __construct()
    {
        $this->apiBaseUrl = rtrim(env('API_URL', config('app.url')), '/') . '/superadmin';
    }

    public function index()
    {
        // Mengambil data dari model TokoInfo
        $tokoInfo = TokoInfo::first(); // Ambil data pertama (atau sesuaikan sesuai kebutuhan)

        // Menampilkan data ke view
        return view('superadmin.pengaturan.index', compact('tokoInfo'));
    }
    public function update(Request $request)
    {
        // Validate the data
        $request->validate([
            'nama' => 'required|string|max:255',
            'alamat_lengkap' => 'required|string',
            'kecamatan' => 'required|string',
            'kota' => 'required|string',
            'provinsi' => 'required|string',
            'kode_pos' => 'required|string',
            'nomor_telepon' => 'required|string',
            'email' => 'required|email',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'is_active' => 'required|boolean',
        ]);

        // Find the first toko info entry and update it
        $tokoInfo = TokoInfo::first();

        // Handle logo upload if it's provided
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
            $tokoInfo->logo = $logoPath;
        }

        // Update the rest of the fields
        $tokoInfo->update($request->except(['logo']));

        return redirect()->route('superadmin.pengaturan.index')->with('success', 'Toko Info updated successfully');
    }
}
