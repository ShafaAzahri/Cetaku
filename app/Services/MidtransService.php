<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MidtransService
{
    protected string $serverKey;
    protected string $apiUrl;

    public function __construct()
    {
        $this->serverKey = config('midtrans.server_key');
        $this->apiUrl = config('midtrans.is_sandbox', true)
            ? 'https://api.sandbox.midtrans.com/v2/charge'
            : 'https://api.midtrans.com/v2/charge';
    }

    /**
     * Membuat charge pembayaran QRIS ke Midtrans
     *
     * @param string $orderId - ID unik order dari merchant
     * @param int $grossAmount - total pembayaran dalam IDR
     * @param array $itemDetails - list produk [{id, price, quantity, name}]
     * @param array $customerDetails - informasi pelanggan [first_name, last_name, email, phone]
     * @return array - hasil response Midtrans dalam bentuk array
     */
    public function createQrisPayment(string $orderId, int $grossAmount, array $itemDetails, array $customerDetails = []): array
    {
        $payload = [
            "payment_type" => "qris",
            "transaction_details" => [
                "order_id" => $orderId,
                "gross_amount" => $grossAmount,
            ],
            "item_details" => $itemDetails,
        ];

        if (!empty($customerDetails)) {
            $payload['customer_details'] = $customerDetails;
        }

        $response = Http::withBasicAuth($this->serverKey, '')
            ->timeout(10)
            ->post($this->apiUrl, $payload);

        if ($response->successful()) {
            return $response->json();
        }

        return [
            'error' => true,
            'message' => 'Gagal membuat pembayaran QRIS',
            'status' => $response->status(),
            'body' => $response->body(),
        ];
    }
}
