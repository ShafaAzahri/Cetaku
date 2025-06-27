<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // Ambil data notifikasi dari Midtrans
        $notif = $request->all();

        // Log notifikasi untuk debugging
        Log::info('Midtrans Webhook:', $notif);

        $orderId = $notif['order_id'] ?? null;
        $transactionStatus = $notif['transaction_status'] ?? null;
        $fraudStatus = $notif['fraud_status'] ?? null;

        // Update status pembayaran dan pesanan di database
        if ($orderId) {
            $pembayaran = Pembayaran::where('midtrans_order_id', $orderId)->first();
            $pesanan = Pesanan::where('id', $orderId)->first();

            if ($pembayaran && $pesanan) {
                if ($transactionStatus == 'settlement') {
                    // Pembayaran sukses
                    $pembayaran->status = 'Lunas';
                    $pesanan->status = 'Dikonfirmasi'; // Atau status sesuai workflow kamu
                } elseif ($transactionStatus == 'pending') {
                    $pembayaran->status = 'Pending';
                    $pesanan->status = 'Pemesanan';
                } elseif ($transactionStatus == 'expire' || $transactionStatus == 'cancel') {
                    $pembayaran->status = 'Dibatalkan';
                    $pesanan->status = 'Dibatalkan';
                }

                $pembayaran->midtrans_response = json_encode($notif);
                $pembayaran->save();
                $pesanan->save();
            }
        }

        return response()->json(['message' => 'Notification received']);
    }

//     public function handle(Request $request)
// {
//     $notif = $request->all();
//     // (Opsional) Validasi signature key di sini

//     $orderId = $notif['order_id'] ?? null;
//     $transactionStatus = $notif['transaction_status'] ?? null;

//     if ($orderId) {
//         $pembayaran = Pembayaran::where('midtrans_order_id', $orderId)->first();
//         $pesanan = Pesanan::where('id', $orderId)->first();

//         if ($pembayaran && $pesanan) {
//             if ($transactionStatus == 'settlement' || $transactionStatus == 'capture') {
//                 $pembayaran->status = 'Lunas';
//                 $pesanan->status = 'Dikonfirmasi';
//             } elseif ($transactionStatus == 'pending') {
//                 $pembayaran->status = 'Pending';
//                 $pesanan->status = 'Pemesanan';
//             } elseif ($transactionStatus == 'expire' || $transactionStatus == 'cancel') {
//                 $pembayaran->status = 'Dibatalkan';
//                 $pesanan->status = 'Dibatalkan';
//             }
//             $pembayaran->midtrans_response = json_encode($notif);
//             $pembayaran->save();
//             $pesanan->save();
//         }
//     }
//     return response()->json(['message' => 'Notification received']);
// }

}
