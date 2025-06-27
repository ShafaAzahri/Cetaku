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
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return redirect()->route('user.profile')
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $response = Http::withToken($token)
                ->post($this->apiBaseUrl . '/profile/update', [
                    'nama' => $request->nama,
                    'email' => $request->email,
                ]);

            $data = $response->json();

            if ($response->successful()) {
                return redirect()->route('user.profile')
                    ->with('success', $data['message'] ?? 'Profil berhasil diperbarui');
            } else {
                return redirect()->route('user.profile')
                    ->with('error', $data['message'] ?? 'Gagal memperbarui profil');
            }

        } catch (\Exception $e) {
            return redirect()->route('user.profile')
                ->with('error', 'Terjadi kesalahan saat memperbarui profil');
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
        'nomor_hp' => 'required|string|max:15',
        'alamat_lengkap' => 'required|string',
        'kelurahan' => 'required|string',
        'kecamatan' => 'required|string',
        'kota' => 'required|string',
        'provinsi' => 'required|string',
        'kode_pos' => 'required|string|max:10',
    ]);

    if ($validator->fails()) {
        return redirect()->route('user.profile')
            ->withErrors($validator)
            ->withInput();
    }

    try {
        $response = Http::withToken($token)
            ->post($this->apiBaseUrl . '/addAlamat', [
                'nomor_hp' => $request->nomor_hp,
                'alamat_lengkap' => $request->alamat_lengkap,
                'kelurahan' => $request->kelurahan,
                'kecamatan' => $request->kecamatan,
                'kota' => $request->kota,
                'provinsi' => $request->provinsi,
                'kode_pos' => $request->kode_pos,
                'label' => $request->label,
            ]);

        $data = $response->json();

        if ($response->successful()) {
            return redirect()->route('user.profile')
                ->with('success', $data['message'] ?? 'Alamat berhasil ditambahkan');
        } else {
            return redirect()->route('user.profile')
                ->with('error', $data['message'] ?? 'Gagal menambahkan alamat');
        }

    } catch (\Exception $e) {
        return redirect()->route('user.profile')
            ->with('error', 'Terjadi kesalahan saat menambahkan alamat');
    }
}


        public function getAlamatDetail($id)
    {
        $token = session('api_token');

        try {
            $response = Http::withToken($token)
                ->get($this->apiBaseUrl . '/alamat/' . $id);

            $data = $response->json();

            // Untuk debug: cek response data
            // dd($data);

            if (!$response->successful() || !isset($data['data'])) {
                return view('user.alamat-detail')->with('error', 'Alamat tidak ditemukan');
            }

            return view('user.alamat-detail', [
                'alamat' => $data['data']
            ]);
        } catch (\Exception $e) {
            return view('user.alamat-detail')->with('error', 'Terjadi kesalahan saat mengambil data alamat');
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
        'nomor_hp' => 'required|string|max:15',
        'alamat_lengkap' => 'required|string',
        'kecamatan' => 'required|string',
        'kota' => 'required|string',
        'provinsi' => 'required|string', // typo: 'rpequired'
        'kode_pos' => 'required|string|max:10',
        'type' => 'required|string|in:Utama,Kantor',
    ]);

    if ($validator->fails()) {
        return redirect()->route('user.profile')
            ->withErrors($validator)
            ->withInput();
    }

    try {
        $response = Http::withToken($token)
            ->put($this->apiBaseUrl . '/alamat/' . $id, [
                'full_name' => $request->full_name,
                'nomor_hp' => $request->nomor_hp,
                'alamat_lengkap' => $request->alamat_lengkap,
                'kecamatan' => $request->kecamatan,
                'kota' => $request->kota,
                'provinsi' => $request->provinsi,
                'kode_pos' => $request->kode_pos,
                'type' => $request->type,
            ]);

        $data = $response->json();

        if ($response->successful()) {
            return redirect()->route('user.profile')->with('success', $data['message'] ?? 'Alamat berhasil diperbarui');
        } else {
            return redirect()->route('user.profile')->with('error', $data['message'] ?? 'Gagal memperbarui alamat');
        }

    } catch (\Exception $e) {
        return redirect()->route('user.profile')->with('error', 'Terjadi kesalahan saat memperbarui alamat');
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

        $data = $response->json();

        if ($response->successful()) {
            return redirect()->route('user.profile')
                ->with('success', $data['message'] ?? 'Alamat berhasil dihapus');
        } else {
            return redirect()->route('user.profile')
                ->with('error', $data['message'] ?? 'Gagal menghapus alamat');
        }

    } catch (\Exception $e) {
        return redirect()->route('user.profile')
            ->with('error', 'Terjadi kesalahan saat menghapus alamat');
    }
}

}
