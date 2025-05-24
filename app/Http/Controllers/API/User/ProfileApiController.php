<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Alamat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ProfileApiController extends Controller
{
    /**
     * Mendapatkan data profil pengguna
     */
    public function getProfile(Request $request)
    {
        try {
            $user = $request->authenticated_user;
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'nama' => $user->nama,
                    'email' => $user->email,
                    'created_at' => $user->created_at
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error mengambil profil: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data profil'
            ], 500);
        }
    }

    /**
     * Memperbarui data profil pengguna
     */
    public function updateProfile(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nama' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $request->authenticated_user->id
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = $request->authenticated_user;
            $user->nama = $request->nama;
            $user->email = $request->email;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Profil berhasil diperbarui',
                'data' => [
                    'id' => $user->id,
                    'nama' => $user->nama,
                    'email' => $user->email
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error memperbarui profil: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui profil'
            ], 500);
        }
    }

    /**
     * Memperbarui password pengguna
     */
    public function updatePassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'old_password' => 'required|string',
                'new_password' => 'required|string|min:8',
                'new_password_confirmation' => 'required|same:new_password'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = $request->authenticated_user;

            // Verifikasi password lama
            if (!Hash::check($request->old_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password lama tidak sesuai'
                ], 400);
            }

            // Update password
            $user->password = Hash::make($request->new_password);
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Password berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            Log::error('Error memperbarui password: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui password'
            ], 500);
        }
    }

    /**
     * Mendapatkan daftar alamat pengguna
     */
    public function getAlamat(Request $request)
    {
        try {
            $alamats = Alamat::where('user_id', $request->authenticated_user->id)->get();
            
            return response()->json([
                'success' => true,
                'data' => $alamats
            ]);
        } catch (\Exception $e) {
            Log::error('Error mengambil alamat: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data alamat'
            ], 500);
        }
    }

    /**
     * Mendapatkan detail alamat
     */
    public function getAlamatDetail(Request $request, $id)
    {
        try {
            $alamat = Alamat::where('id', $id)
                ->where('user_id', $request->authenticated_user->id)
                ->first();
            
            if (!$alamat) {
                return response()->json([
                    'success' => false,
                    'message' => 'Alamat tidak ditemukan'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'data' => $alamat
            ]);
        } catch (\Exception $e) {
            Log::error('Error mengambil detail alamat: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil detail alamat'
            ], 500);
        }
    }

    /**
     * Menambahkan alamat baru
     */
    public function createAlamat(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'label' => 'nullable|string|max:255',
                'alamat_lengkap' => 'required|string',
                'kelurahan' => 'nullable|string|max:255',
                'kecamatan' => 'required|string|max:255',
                'kota' => 'required|string|max:255',
                'provinsi' => 'required|string|max:255',
                'kode_pos' => 'required|string|max:10',
                'nomor_hp' => 'required|string|max:20'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $alamat = new Alamat();
            $alamat->user_id = $request->authenticated_user->id;
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
                'message' => 'Alamat berhasil ditambahkan',
                'data' => $alamat
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error menambahkan alamat: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menambahkan alamat'
            ], 500);
        }
    }

    /**
     * Memperbarui alamat
     */
    public function updateAlamat(Request $request, $id)
    {
        try {
            $alamat = Alamat::where('id', $id)
                ->where('user_id', $request->authenticated_user->id)
                ->first();
            
            if (!$alamat) {
                return response()->json([
                    'success' => false,
                    'message' => 'Alamat tidak ditemukan'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'label' => 'nullable|string|max:255',
                'alamat_lengkap' => 'required|string',
                'kelurahan' => 'nullable|string|max:255',
                'kecamatan' => 'required|string|max:255',
                'kota' => 'required|string|max:255',
                'provinsi' => 'required|string|max:255',
                'kode_pos' => 'required|string|max:10',
                'nomor_hp' => 'required|string|max:20'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
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
                'data' => $alamat
            ]);
        } catch (\Exception $e) {
            Log::error('Error memperbarui alamat: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui alamat'
            ], 500);
        }
    }

    /**
     * Menghapus alamat
     */
    public function deleteAlamat(Request $request, $id)
    {
        try {
            $alamat = Alamat::where('id', $id)
                ->where('user_id', $request->authenticated_user->id)
                ->first();
            
            if (!$alamat) {
                return response()->json([
                    'success' => false,
                    'message' => 'Alamat tidak ditemukan'
                ], 404);
            }

            $alamat->delete();

            return response()->json([
                'success' => true,
                'message' => 'Alamat berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            Log::error('Error menghapus alamat: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus alamat'
            ], 500);
        }
    }
}