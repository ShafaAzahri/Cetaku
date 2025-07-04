<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\TokoInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class CheckoutController extends Controller
{
    protected $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = rtrim(env('API_URL', config('app.url')), '/');
    }

    public function index(Request $request)
    {
        $addresses = $this->getAddresses($request);
        $userName = $this->getUserName($request);
        $expeditions = $this->getExpeditions($request, $addresses);
        $tokoInfo = TokoInfo::first(); // ⬅ Tambahkan ini

        return view('user.checkout', [
            'addresses' => $addresses,
            'user_name' => $userName,
            'expeditions' => $expeditions,
            'tokoInfo' => $tokoInfo, // ⬅ Kirim ke view
        ]);
    }


    private function getAddresses(Request $request)
    {
        $token = session('api_token');
        try {
            $response = Http::withToken($token)
                ->get($this->apiBaseUrl . '/alamat');
            $addresses = $response->json();
            if (is_array($addresses) && isset($addresses['data'])) {
                return $addresses['data'];
            } elseif (is_array($addresses) && isset($addresses[0])) {
                return $addresses;
            } else {
                return [];
            }
        } catch (\Exception $e) {
            return [];
        }
    }

    private function getUserName(Request $request)
    {
        $token = session('api_token');
        try {
            $response = Http::withToken($token)
                ->get($this->apiBaseUrl . '/profile');
            $profile = $response->json();
            if (is_array($profile) && isset($profile['data']['nama'])) {
                return $profile['data']['nama'];
            } elseif (is_array($profile) && isset($profile['user']['nama'])) {
                return $profile['user']['nama'];
            } else {
                return '';
            }
        } catch (\Exception $e) {
            return '';
        }
    }

    private function getLocationId($searchTerm)
    {
        try {
            $response = Http::withHeaders([
                'key' => env('RAJA_ONGKIR_KEY'),
            ])->get('https://rajaongkir.komerce.id/api/v1/destination/domestic-destination', [
                        'search' => $searchTerm
                    ]);

            $data = $response->json();

            Log::info("Search '{$searchTerm}' result:", $data['data'] ?? []);

            // Gunakan 'id' dari data pertama
            return $data['data'][0]['id'] ?? null;
        } catch (\Exception $e) {
            Log::error('getLocationId error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get origin location ID from toko_info table
     */
    private function getOriginLocationId()
    {
        try {
            $tokoInfo = TokoInfo::getActiveToko();

            if (!$tokoInfo || !$tokoInfo->kelurahan) {
                Log::warning('Toko info tidak ditemukan atau kelurahan kosong, menggunakan default Genuk');
                return $this->getLocationId('Genuk');
            }

            $originLocationId = $this->getLocationId($tokoInfo->kelurahan);

            if (!$originLocationId) {
                Log::warning("Tidak dapat menemukan ID lokasi untuk kelurahan: {$tokoInfo->kelurahan}, menggunakan default Genuk");
                return $this->getLocationId('Genuk');
            }

            Log::info("Origin dari toko: {$tokoInfo->kelurahan} (ID: {$originLocationId})");
            return $originLocationId;

        } catch (\Exception $e) {
            Log::error('Error getting origin location: ' . $e->getMessage());
            // Fallback ke default jika terjadi error
            return $this->getLocationId('Genuk');
        }
    }

    private function getExpeditions(Request $request, $addresses)
    {
        $weight = 1000;

        // Ambil origin dari toko_info
        $origin = $this->getOriginLocationId();

        if (empty($addresses)) {
            return [];
        }

        $alamatUtama = collect($addresses)->first();

        // Dapatkan destination dari kelurahan alamat user
        $destinationCity = trim($alamatUtama['kelurahan'] ?? '');
        $destination = $this->getLocationId($destinationCity);

        Log::info('Origin dari toko (ID: ' . $origin . ')');
        Log::info('Destination: ' . $destinationCity . ' (ID: ' . $destination . ')');

        if (!$origin || !$destination) {
            return [];
        }

        try {
            $response = Http::withHeaders([
                'key' => env('RAJA_ONGKIR_KEY'),
            ])
                ->asForm()
                ->post('https://rajaongkir.komerce.id/api/v1/calculate/domestic-cost', [
                    'origin' => $origin,
                    'destination' => $destination,
                    'weight' => $weight,
                    'courier' => 'jnt',
                    'price' => 'lowest',
                ]);

            $data = $response->json();
            return $data['data'] ?? [];
        } catch (\Exception $e) {
            Log::error('Error getExpeditions: ' . $e->getMessage());
            return [];
        }
    }

    public function checkoutTerpilih(Request $request)
    {

        $produkIds = $request->input('selected_items', []);

        if (empty($produkIds)) {
            return redirect()->back()->with('error', 'Silakan pilih minimal satu produk untuk checkout.');
        }

        $token = session('api_token');

        $response = Http::withToken($token)->get($this->apiBaseUrl . '/keranjang');
        $semuaProduk = $response->json();

        $produkTerpilih = collect($semuaProduk['data']['items'])->whereIn('id', $produkIds)->values()->all();

        $biayaDesainResponse = Http::get($this->apiBaseUrl . '/biaya-desains');
        $biayaDesain = 0;
        if ($biayaDesainResponse->successful()) {
            $biayaDesains = $biayaDesainResponse->json()['biaya_desains'] ?? [];
            if (!empty($biayaDesains)) {
                $biayaDesain = $biayaDesains[0]['biaya'] ?? 0;
            }
        }

        $totalBiayaDesain = 0;
        $uniqueDesignKeys = [];

        foreach ($produkTerpilih as $produk) {
            $tipeDesain = $produk['tipe_desain'] ?? 'sendiri';
            if ($tipeDesain === 'dibuatkan') {
                $key = $produk['item_id'] . '-' .
                    $produk['ukuran_id'] . '-' .
                    $produk['bahan_id'] . '-' .
                    $produk['jenis_id'] . '-' .
                    $tipeDesain;

                if (!in_array($key, $uniqueDesignKeys)) {
                    $totalBiayaDesain += $biayaDesain;
                    $uniqueDesignKeys[] = $key;
                }
            }
        }
        $tokoInfo = TokoInfo::first();
        $addresses = $this->getAddresses($request);
        $expeditions = $this->getExpeditions($request, $addresses);

        return view('user.checkout', [
            'produkTerpilih' => $produkTerpilih,
            'addresses' => $addresses,
            'user_name' => $this->getUserName($request),
            'expeditions' => $expeditions,
            'biaya_desain' => $totalBiayaDesain,
            'tokoInfo' => $tokoInfo,
        ]);
    }
}