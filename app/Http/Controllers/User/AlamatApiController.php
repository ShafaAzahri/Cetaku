<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Alamat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class AlamatApiController extends Controller
{
    /**
     * Mengambil semua alamat
     * - Untuk user biasa: hanya alamat miliknya
     * - Untuk super admin: semua alamat atau alamat user tertentu jika ada user_id
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            // User sudah diautentikasi oleh middleware
            $user = $request->authenticated_user;
            $query = Alamat::query();
            
            // Jika super admin dan ada parameter user_id
            if ($user->isSuperAdmin() && $request->has('user_id')) {
                $query->where('user_id', $request->user_id);
            }
            // Jika bukan super admin, hanya tampilkan alamat user tersebut
            elseif (!$user->isSuperAdmin()) {
                $query->where('user_id', $user->id);
            }
            
            $alamats = $query->get();
            
            return response()->json([
                'success' => true,
                'alamats' => $alamats
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching addresses: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data alamat: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menyimpan alamat baru
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            // Validasi input
            $validator = Validator::make($request->all(), [
                'user_id' => 'nullable|exists:users,id', // Opsional, untuk super admin
                'label' => 'nullable|string|max:255',
                'alamat_lengkap' => 'required|string',
                'kelurahan' => 'nullable|string|max:255',
                'kecamatan' => 'nullable|string|max:255',
                'kota' => 'required|string|max:255',
                'provinsi' => 'required|string|max:255',
                'kode_pos' => 'required|string|max:10',
                'nomor_hp' => 'required|string|max:20'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            // User sudah diautentikasi oleh middleware
            $user = $request->authenticated_user;
            
            $alamat = new Alamat();
            
            // Jika super admin dan ada parameter user_id, gunakan user_id tersebut
            if ($user->isSuperAdmin() && $request->has('user_id')) {
                $alamat->user_id = $request->user_id;
            } else {
                $alamat->user_id = $user->id;
            }
            
            $alamat->label = $request->label;
            $alamat->alamat_lengkap = $request->alamat_lengkap;
            $alamat->kelurahan = $request->kelurahan;
            $alamat->kecamatan = $request->kecamatan;
            $alamat->kota = $request->kota;
            $alamat->provinsi = $request->provinsi;
            $alamat->kode_pos = $request->kode_pos;
            $alamat->nomor_hp = $request->nomor_hp;
            $alamat->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Alamat berhasil disimpan',
                'alamat' => $alamat
            ], 201);
            
        } catch (\Exception $e) {
            Log::error('Error creating address: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan alamat: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan alamat berdasarkan ID
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        try {
            // User sudah diautentikasi oleh middleware
            $user = $request->authenticated_user;
            
            $alamat = Alamat::find($id);
            
            if (!$alamat) {
                return response()->json([
                    'success' => false,
                    'message' => 'Alamat tidak ditemukan'
                ], 404);
            }
            
            // Cek apakah user berhak mengakses alamat ini
            if (!$user->isSuperAdmin() && $alamat->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk alamat ini'
                ], 403);
            }
            
            return response()->json([
                'success' => true,
                'alamat' => $alamat
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching address: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data alamat: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Memperbarui alamat berdasarkan ID
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            // Validasi input
            $validator = Validator::make($request->all(), [
                'label' => 'nullable|string|max:255',
                'alamat_lengkap' => 'required|string',
                'kelurahan' => 'nullable|string|max:255',
                'kecamatan' => 'nullable|string|max:255',
                'kota' => 'required|string|max:255',
                'provinsi' => 'required|string|max:255',
                'kode_pos' => 'required|string|max:10',
                'nomor_hp' => 'required|string|max:20'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            // User sudah diautentikasi oleh middleware
            $user = $request->authenticated_user;
            
            $alamat = Alamat::find($id);
            
            if (!$alamat) {
                return response()->json([
                    'success' => false,
                    'message' => 'Alamat tidak ditemukan'
                ], 404);
            }
            
            // Cek apakah user berhak mengakses alamat ini
            if (!$user->isSuperAdmin() && $alamat->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk alamat ini'
                ], 403);
            }
            
            $alamat->label = $request->label;
            $alamat->alamat_lengkap = $request->alamat_lengkap;
            $alamat->kelurahan = $request->kelurahan;
            $alamat->kecamatan = $request->kecamatan;
            $alamat->kota = $request->kota;
            $alamat->provinsi = $request->provinsi;
            $alamat->kode_pos = $request->kode_pos;
            $alamat->nomor_hp = $request->nomor_hp;
            $alamat->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Alamat berhasil diperbarui',
                'alamat' => $alamat
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error updating address: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui alamat: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menghapus alamat berdasarkan ID
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        try {
            // User sudah diautentikasi oleh middleware
            $user = $request->authenticated_user;
            
            $alamat = Alamat::find($id);
            
            if (!$alamat) {
                return response()->json([
                    'success' => false,
                    'message' => 'Alamat tidak ditemukan'
                ], 404);
            }
            
            // Cek apakah user berhak mengakses alamat ini
            if (!$user->isSuperAdmin() && $alamat->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk alamat ini'
                ], 403);
            }
            
            $alamat->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Alamat berhasil dihapus'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error deleting address: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus alamat: ' . $e->getMessage()
            ], 500);
        }
    }
}