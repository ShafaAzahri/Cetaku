<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Models\Pembayaran;
use App\Models\Keranjang;
use App\Models\Custom;
use App\Models\BiayaDesain;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Midtrans\Config;
use Midtrans\Snap;

class PaymentController extends Controller
{
    protected $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = rtrim(env('API_URL', config('app.url')), '/');
    }

    /**
     * Proses checkout dan pembayaran via Midtrans (QRIS otomatis tersedia jika aktif di Midtrans)
     */
    public function checkoutPayment(Request $request)
    {
        // Validasi token API secara manual
        $token = $request->bearerToken();
        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak ditemukan'
            ], 401);
        }

        // Cari user berdasarkan token
        $user = User::where('api_token', $token)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak valid'
            ], 401);
        }

        // Ambil produk yang dipilih dari request (array ID item keranjang)
        $selectedItems = $request->input('selected_items', []);
        if (empty($selectedItems) || !is_array($selectedItems)) {
            return response()->json([
                'success' => false,
                'message' => 'Silakan pilih minimal satu produk untuk checkout'
            ], 400);
        }

        // Ambil data keranjang dari database
        $keranjangItems = Keranjang::with(['item', 'ukuran', 'bahan', 'jenis'])
            ->where('user_id', $user->id)
            ->whereIn('id', $selectedItems)
            ->get();

        if ($keranjangItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada produk yang dipilih di keranjang'
            ], 400);
        }

        // Hitung total harga produk
        $totalHargaProduk = $keranjangItems->sum('total_harga');

        // Ambil biaya desain dari database (jika ada tipe desain 'dibuatkan')
        $biayaDesainRecord = BiayaDesain::first();
        $biayaDesain = (float) ($biayaDesainRecord->biaya ?? 0);

        // Hitung total biaya desain berdasarkan tipe desain 'dibuatkan'
        $totalBiayaDesain = $keranjangItems->where('tipe_desain', 'dibuatkan')->count() > 0
            ? $biayaDesain
            : 0;

        // Ambil ongkir dari request
        $ongkir = (float) $request->input('ongkir', 0);

        // Hitung total harga keseluruhan
        $totalHarga = $totalHargaProduk + $totalBiayaDesain + $ongkir;

        // Mulai transaksi database
        DB::beginTransaction();
        try {
            // Buat pesanan baru
            $pesanan = Pesanan::create([
                'user_id' => $user->id,
                'status' => 'Pemesanan',
                'total' => $totalHarga,
                'estimasi_waktu' => 24, // Contoh: 24 jam
                'tanggal_dipesan' => now(),
            ]);

            // Buat detail pesanan dari item keranjang
            foreach ($keranjangItems as $item) {
                // PAKSA INSERT BARU - Gunakan raw SQL langsung
                // Melewati semua logic Eloquent yang mungkin ada
                $customId = DB::select("
                    INSERT INTO customs (item_id, ukuran_id, bahan_id, jenis_id, harga) 
                    VALUES (?, ?, ?, ?, ?)
                ", [
                    $item->item_id,
                    $item->ukuran_id,
                    $item->bahan_id,
                    $item->jenis_id,
                    $item->harga_satuan
                ]);

                // Ambil ID yang baru saja diinsert
                $customId = DB::getPdo()->lastInsertId();

                // Debug: Log untuk memastikan custom ID baru dibuat
                Log::info("Custom ID baru dibuat: " . $customId . " untuk item: " . $item->item_id);

                DetailPesanan::create([
                    'pesanan_id' => $pesanan->id,
                    'custom_id' => $customId,
                    'jumlah' => $item->jumlah,
                    'upload_desain' => $item->upload_desain,
                    'total_harga' => $item->total_harga,
                    'tipe_desain' => $item->tipe_desain,
                    'biaya_jasa' => ($item->tipe_desain === 'dibuatkan') ? $biayaDesain : 0,
                ]);
            }

            // Konfigurasi Midtrans
            Config::$serverKey = config('midtrans.server_key');
            Config::$is3ds = true;
            Config::$isSanitized = true;
            Config::$isProduction = false;

            // Parameter untuk Midtrans
            $params = [
                'transaction_details' => [
                    'order_id' => $pesanan->id,
                    'gross_amount' => $totalHarga,
                ],
                'customer_details' => [
                    'first_name' => $user->nama,
                    'email' => $user->email,
                    'phone' => $request->input('nomor_hp', '081234567890'),
                ],
                // Jika ingin spesifik QRIS, bisa tambahkan payment_type (hanya jika plan dan fitur Midtrans mendukung)
                // 'payment_type' => 'qris',
            ];
            
            // Generate Snap Token
            $snapToken = Snap::getSnapToken($params);

            // Simpan data pembayaran ke database
            Pembayaran::create([
                'pesanan_id' => $pesanan->id,
                'midtrans_order_id' => $pesanan->id,
                'snap_token' => $snapToken,
                'metode' => 'QRIS', // Simpan metode sebagai QRIS, meskipun di Midtrans bisa memilih metode lain
                'status' => 'Pending',
                'midtrans_response' => json_encode($params),
            ]);

            // Hapus item keranjang yang sudah diproses
            foreach ($keranjangItems as $item) {
                if ($item->upload_desain) {
                    Storage::disk('public')->delete($item->upload_desain);
                }
                $item->delete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
                'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v2/vtweb/' . $snapToken,
                'order_id' => $pesanan->id,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan checkout: ' . $e->getMessage()
            ], 500);
        }
    }
}