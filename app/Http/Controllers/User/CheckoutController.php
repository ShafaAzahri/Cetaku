<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class CheckoutController extends Controller
{
    protected $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = rtrim(env('API_URL', config('app.url')), '/');
    }

    /**
     * Halaman utama checkout: memanggil fungsi alamat, nama user, dan ekspedisi.
     */
    public function index(Request $request)
    {
        $addresses = $this->getAddresses($request);
        $userName = $this->getUserName($request);
        $expeditions = $this->getExpeditions($request);

        // Data lain jika perlu (weight, origin, destination)
        $weight = 1000;
        $origin = 65076;
        $destination = 64919;

        return view('user.checkout', [
            'addresses'   => $addresses,
            'user_name'   => $userName,
            'expeditions' => $expeditions,
            'weight'      => $weight,
            'origin'      => $origin,
            'destination' => $destination,
        ]);
    }

    /**
     * Mengambil daftar alamat pengguna dari API.
     */
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

    /**
     * Mengambil nama user dari API profil.
     */
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

    /**
     * Mengambil data ekspedisi dari API Raja Ongkir.
     */
    private function getExpeditions(Request $request)
    {
        $weight = 1000; // Berat barang tetap 1000 gram
        $origin = 65076; // ID Kota asal (misal: Jakarta)
        $destination = 64919; // ID Kota tujuan (misal: Semarang)
        try {
            $response = Http::withHeaders([
                'key' => env('RAJA_ONGKIR_KEY'),
            ])
            ->asForm()
            ->post('https://rajaongkir.komerce.id/api/v1/calculate/domestic-cost', [
                'origin' => $origin,
                'destination' => $destination,
                'weight' => $weight,
                'courier' => 'jne',
                'price' => 'lowest',
            ]);
            $data = $response->json();
            return $data['data'] ?? [];
        } catch (\Exception $e) {
            return [];
        }
    }

    public function checkoutTerpilih(Request $request)
    {
        $produkIds = $request->input('selected_items', []); // Mengambil produk yang dipilih

        if (empty($produkIds)) {
            return redirect()->back()->with('error', 'Silakan pilih minimal satu produk untuk checkout.');
        }

        $token = session('api_token');

        // Panggil API keranjang, lalu filter berdasarkan produk terpilih
        $response = Http::withToken($token)->get($this->apiBaseUrl . '/keranjang');
        $semuaProduk = $response->json();

        $produkTerpilih = collect($semuaProduk['data']['items'])->whereIn('id', $produkIds)->values()->all();

        // Ambil biaya desain dari API
        $biayaDesainResponse = Http::get($this->apiBaseUrl . '/biaya-desains');
        $biayaDesain = 0;
        if ($biayaDesainResponse->successful()) {
            $biayaDesains = $biayaDesainResponse->json()['biaya_desains'] ?? [];
            if (!empty($biayaDesains)) {
                $biayaDesain = $biayaDesains[0]['biaya'] ?? 0; // Ambil biaya desain pertama
            }
        }

        // Hitung total biaya desain berdasarkan tipe desain
        $totalBiayaDesain = 0;
        $uniqueDesignKeys = [];

        foreach ($produkTerpilih as $produk) {
            $tipeDesain = $produk['tipe_desain'] ?? 'sendiri';
            if ($tipeDesain === 'dibuatkan') {
                // Buat key unik berdasarkan kombinasi item, ukuran, bahan, jenis, dan tipe desain
                $key = $produk['item_id'] . '-' . 
                    $produk['ukuran_id'] . '-' . 
                    $produk['bahan_id'] . '-' . 
                    $produk['jenis_id'] . '-' . 
                    $tipeDesain;

                // Jika kombinasi unik belum dihitung, tambahkan biaya desain
                if (!in_array($key, $uniqueDesignKeys)) {
                    $totalBiayaDesain += $biayaDesain;
                    $uniqueDesignKeys[] = $key;
                }
            }
        }

        return view('user.checkout', [
            'produkTerpilih' => $produkTerpilih,
            'addresses'      => $this->getAddresses($request),
            'user_name'      => $this->getUserName($request),
            'expeditions'    => $this->getExpeditions($request),
            'weight'         => 1000,
            'origin'         => 65076,
            'destination'    => 64919,
            'biaya_desain'   => $totalBiayaDesain, // Kirim total biaya desain ke view
        ]);
    }


}
