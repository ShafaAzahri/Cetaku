<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class PaymentWeb extends Controller
{
    protected $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = rtrim(env('API_URL', config('app.url')), '/');
    }

    public function checkoutPayment(Request $request)
    {
        // Validasi token session
        $apiToken = session('api_token');
        if (!$apiToken) {
            return response()->json([
                'success' => false, 
                'message' => 'Anda harus login terlebih dahulu'
            ], 401);
        }

        try {
            // Ambil data ekspedisi dari request
            $ekspedisiInput = $request->input('ekspedisi');
            
            // Pastikan data ekspedisi dalam format array yang benar
            $ekspedisiData = [];
            if ($ekspedisiInput) {
                if (is_string($ekspedisiInput)) {
                    // Jika data ekspedisi berupa string JSON, decode dulu
                    $ekspedisiData = json_decode($ekspedisiInput, true) ?? [];
                } elseif (is_array($ekspedisiInput)) {
                    // Jika sudah array, gunakan langsung
                    $ekspedisiData = $ekspedisiInput;
                }
            }

            // Prepare data untuk API
            $checkoutData = [
                'alamat_id' => $request->input('alamat_id'),
                'selected_items' => $request->input('selected_items', []),
                'ongkir' => (float) $request->input('ongkir', 0),
                'payment_method' => $request->input('payment_method', 'cod'),
                'delivery_method' => $request->input('delivery_method', 'antar'),
                'ekspedisi' => $ekspedisiData,
                'nomor_hp' => $request->input('nomor_hp', '081234567890')
            ];

            // Validasi input
            if (empty($checkoutData['selected_items']) || !is_array($checkoutData['selected_items'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Silakan pilih minimal satu produk'
                ], 400);
            }

            if ($checkoutData['delivery_method'] === 'antar' && !$checkoutData['alamat_id']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Alamat harus dipilih untuk pesan antar'
                ], 400);
            }

            // PERBAIKAN: Validasi data ekspedisi untuk delivery method 'antar'
            if ($checkoutData['delivery_method'] === 'antar') {
                // Cek apakah ekspedisi data ada dan memiliki field yang diperlukan
                if (empty($ekspedisiData) || 
                    empty($ekspedisiData['nama']) || 
                    empty($ekspedisiData['code']) || 
                    !isset($ekspedisiData['cost'])) {
                    
                    return response()->json([
                        'success' => false,
                        'message' => 'Data ekspedisi tidak lengkap. Silakan pilih ekspedisi terlebih dahulu.'
                    ], 400);
                }

                // Validasi tambahan untuk memastikan cost adalah numeric
                if (!is_numeric($ekspedisiData['cost']) || $ekspedisiData['cost'] < 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Biaya ekspedisi tidak valid'
                    ], 400);
                }
            }

            // Log data yang akan dikirim untuk debugging
            Log::info('Data checkout yang akan dikirim ke API', [
                'checkout_data' => $checkoutData,
                'ekspedisi_detail' => $ekspedisiData,
                'user_session' => session()->getId()
            ]);

            // Call API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiToken,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ])->timeout(30)->post($this->apiBaseUrl . '/checkout/payment', $checkoutData);

            // Handle response
            if ($response->successful()) {
                $result = $response->json();
                
                if ($result['success']) {
                    // Log successful checkout
                    Log::info('Checkout berhasil', [
                        'order_id' => $result['order_id'] ?? null,
                        'payment_method' => $result['payment_method'] ?? null,
                        'delivery_method' => $result['delivery_method'] ?? null,
                        'ekspedisi_used' => $ekspedisiData,
                        'user_session' => session()->getId()
                    ]);

                    return response()->json($result, 201);
                } else {
                    // Log API error
                    Log::warning('API checkout failed', [
                        'api_message' => $result['message'] ?? 'Unknown error',
                        'api_response' => $result,
                        'request_data' => $checkoutData
                    ]);

                    return response()->json($result, 400);
                }
            } else {
                // Handle API error response
                $errorData = $response->json();
                $errorMessage = $errorData['message'] ?? 'Terjadi kesalahan pada server';
                
                Log::error('API checkout error', [
                    'status' => $response->status(),
                    'response' => $errorData,
                    'request_data' => $checkoutData
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], $response->status());
            }

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Connection error during checkout', [
                'error' => $e->getMessage(),
                'api_url' => $this->apiBaseUrl
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat terhubung ke server. Silakan coba lagi.'
            ], 503);

        } catch (\Illuminate\Http\Client\RequestException $e) {
            Log::error('Request error during checkout', [
                'error' => $e->getMessage(),
                'response' => $e->response ? $e->response->body() : null
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses permintaan.'
            ], 500);

        } catch (\Exception $e) {
            Log::error('Unexpected error during checkout', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan yang tidak terduga. Silakan coba lagi.'
            ], 500);
        }
    }
}