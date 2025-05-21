<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    protected $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = rtrim(env('API_URL', config('app.url')), '/');
    }

    /**
     * Tampilkan halaman profil dengan data dari API
     */
    public function showProfile(Request $request)
    {
        // Ambil data profil
        $profile = $this->getProfile($request);
        $profileData = [];
        if (is_array($profile) && isset($profile['data'])) {
            $profileData = $profile['data'];
        } elseif (is_array($profile) && isset($profile['user'])) {
            // fallback jika API mengembalikan key 'user'
            $profileData = $profile['user'];
        }

        // Ambil data alamat
        $addresses = $this->getAddresses($request);
        $addressesData = [];
        if (is_array($addresses) && isset($addresses['data'])) {
            $addressesData = $addresses['data'];
        } elseif (is_array($addresses) && isset($addresses[0])) {
            // fallback jika API mengembalikan array langsung
            $addressesData = $addresses;
        }

        return view('user.profile', [
            'profile' => $profileData,
            'addresses' => $addressesData,
        ]);
    }

    /**
     * Mendapatkan profil pengguna dari API
     */
    public function getProfile(Request $request)
    {
        $token = session('api_token');
        try {
            $response = Http::withToken($token)
                ->get($this->apiBaseUrl . '/profile');
            // Pastikan response selalu array
            return $response->json();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Mendapatkan daftar alamat pengguna dari API
     */
    public function getAddresses(Request $request)
    {
        $token = session('api_token');
        try {
            $response = Http::withToken($token)
                ->get($this->apiBaseUrl . '/alamat');
            // Pastikan response selalu array
            return $response->json();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Memperbarui profil pengguna
     */
    public function updateProfile(Request $request)
    {
        $token = session('api_token');
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $request->user()->id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $response = Http::withToken($token)
                ->post($this->apiBaseUrl . '/profile/update', [
                    'nama' => $request->nama,
                    'email' => $request->email,
                ]);
            return $response->json();
        } catch (\Exception $e) {
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
        $validator = Validator::make($request->all(), [
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->route('user.profile')
                ->withErrors($validator)
                ->withInput();
        }

        $token = session('api_token');
        
        try {
            $response = Http::withToken($token)
                ->post($this->apiBaseUrl . '/profile/update-password', [
                    'old_password' => $request->old_password,
                    'new_password' => $request->new_password,
                    'new_password_confirmation' => $request->new_password_confirmation
                ]);

            $responseData = $response->json();
            
            if (!$response->successful()) {
                // Jika API mengembalikan error
                if (isset($responseData['errors']) && is_array($responseData['errors'])) {
                    $errorMessages = [];
                    foreach ($responseData['errors'] as $field => $errors) {
                        foreach ($errors as $error) {
                            $errorMessages[] = $error;
                        }
                    }
                    return redirect()->route('user.profile')->withErrors($errorMessages);
                }
                
                return redirect()->route('user.profile')
                    ->with('error', $responseData['message'] ?? 'Terjadi kesalahan saat mengubah password');
            }

            return redirect()->route('user.profile')
                ->with('success', 'Password berhasil diperbarui');

        } catch (\Exception $e) {
            return redirect()->route('user.profile')
                ->with('error', 'Terjadi kesalahan koneksi ke server');
        }
    }

    /**
     * Menambah alamat baru
     */
    public function addAddress(Request $request)
    {
        $token = session('api_token');
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'address' => 'required|string',
            'kecamatan' => 'required|string',
            'kota' => 'required|string',
            'provinsi' => 'required|string',
            'kode_pos' => 'required|string|max:10',
            'type' => 'required|string|in:Utama,Kantor',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $response = Http::withToken($token)
                ->post($this->apiBaseUrl . '/alamat', [
                    'full_name' => $request->full_name,
                    'phone' => $request->phone,
                    'address' => $request->address,
                    'kecamatan' => $request->kecamatan,
                    'kota' => $request->kota,
                    'provinsi' => $request->provinsi,
                    'kode_pos' => $request->kode_pos,
                    'type' => $request->type,
                ]);
            return $response->json();
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menambahkan alamat'
            ], 500);
        }
    }

    /**
     * Memperbarui alamat
     */
    public function updateAddress(Request $request, $id)
    {
        $token = session('api_token');
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'address' => 'required|string',
            'kecamatan' => 'required|string',
            'kota' => 'required|string',
            'provinsi' => 'required|string',
            'kode_pos' => 'required|string|max:10',
            'type' => 'required|string|in:Utama,Kantor',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $response = Http::withToken($token)
                ->put($this->apiBaseUrl . '/alamat/' . $id, [
                    'full_name' => $request->full_name,
                    'phone' => $request->phone,
                    'address' => $request->address,
                    'kecamatan' => $request->kecamatan,
                    'kota' => $request->kota,
                    'provinsi' => $request->provinsi,
                    'kode_pos' => $request->kode_pos,
                    'type' => $request->type,
                ]);
            return $response->json();
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui alamat'
            ], 500);
        }
    }

    /**
     * Menghapus alamat
     */
    public function deleteAddress(Request $request, $id)
    {
        $token = session('api_token');
        try {
            $response = Http::withToken($token)
                ->delete($this->apiBaseUrl . '/alamat/' . $id);
            return $response->json();
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus alamat'
            ], 500);
        }
    }
}
