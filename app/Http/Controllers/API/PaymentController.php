<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Models\Pembayaran;
use App\Models\Keranjang;
use App\Models\BiayaDesain;
use App\Models\User;
use App\Models\Alamat;
use App\Models\Ekspedisi;
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

    public function checkoutPayment(Request $request)
    {
        // Validasi token API
        $token = $request->bearerToken();
        if (!$token) {
            return response()->json(['success' => false, 'message' => 'Token tidak ditemukan'], 401);
        }

        $user = User::where('api_token', $token)->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Token tidak valid'], 401);
        }

        // Validasi input
        $selectedItems = $request->input('selected_items', []);
        if (empty($selectedItems) || !is_array($selectedItems)) {
            return response()->json(['success' => false, 'message' => 'Silakan pilih minimal satu produk'], 400);
        }

        $paymentMethod = $request->input('payment_method', 'cod');
        $deliveryMethod = $request->input('delivery_method', 'antar');
        $alamatId = $request->input('alamat_id');

        // Tentukan metode pengambilan berdasarkan delivery method
        $metodePengambilan = ($deliveryMethod === 'antar') ? 'antar' : 'ambil';

        // Data ekspedisi dari request
        $ekspedisiData = $request->input('ekspedisi', []);
        $namaEkspedisi = $ekspedisiData['nama'] ?? null;
        $layananEkspedisi = $ekspedisiData['layanan'] ?? null;
        $estimasiEkspedisi = $ekspedisiData['estimasi'] ?? null;
        $beratTotal = $ekspedisiData['berat'] ?? 1000;

        // Validasi alamat untuk delivery 'antar'
        $alamatPengiriman = null;
        if ($deliveryMethod === 'antar') {
            if (!$alamatId) {
                return response()->json(['success' => false, 'message' => 'Alamat harus dipilih untuk pesan antar'], 400);
            }

            $alamat = Alamat::where('id', $alamatId)->where('user_id', $user->id)->first();
            if (!$alamat) {
                return response()->json(['success' => false, 'message' => 'Alamat tidak valid'], 400);
            }

            $alamatPengiriman = "{$alamat->alamat_lengkap}, {$alamat->kelurahan}, {$alamat->kecamatan}, {$alamat->kota}, {$alamat->provinsi} {$alamat->kode_pos}";
        }

        // Ambil data keranjang
        $keranjangItems = Keranjang::with(['item', 'ukuran', 'bahan', 'jenis'])
            ->where('user_id', $user->id)
            ->whereIn('id', $selectedItems)
            ->get();

        if ($keranjangItems->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Tidak ada produk yang dipilih'], 400);
        }

        // Hitung total
        $totalHargaProduk = $keranjangItems->sum('total_harga');
        $biayaDesainRecord = BiayaDesain::first();
        $biayaDesain = (float) ($biayaDesainRecord->biaya ?? 0);
        $totalBiayaDesain = $keranjangItems->where('tipe_desain', 'dibuatkan')->count() > 0 ? $biayaDesain : 0;
        
        // Ongkir hanya untuk delivery 'antar'
        $ongkir = ($deliveryMethod === 'antar') ? (float) $request->input('ongkir', 0) : 0;
        $totalHarga = $totalHargaProduk + $totalBiayaDesain + $ongkir;

        DB::beginTransaction();
        try {
            // Buat pesanan dengan field yang benar
            $pesanan = Pesanan::create([
                'user_id' => $user->id,
                'alamat_id' => $alamatId,
                'alamat_pengiriman' => $alamatPengiriman,
                'status' => 'Pemesanan',
                'total' => $totalHarga,
                'estimasi_waktu' => 24,
                'tanggal_dipesan' => now(),
                'metode_pengiriman' => $deliveryMethod, // 'antar' atau 'ambil'
                'metode_pengambilan' => $metodePengambilan, // sama dengan metode_pengiriman untuk konsistensi
                'ongkir' => $ongkir,
            ]);

            // Simpan data ekspedisi jika delivery method adalah 'antar'
            if ($deliveryMethod === 'antar' && $namaEkspedisi) {
                Ekspedisi::create([
                    'pesanan_id' => $pesanan->id,
                    'nama_ekspedisi' => $namaEkspedisi,
                    'layanan' => $layananEkspedisi,
                    'estimasi' => $estimasiEkspedisi,
                    'ongkos_kirim' => $ongkir,
                    'berat' => $beratTotal
                ]);
            }

            // Buat detail pesanan
            foreach ($keranjangItems as $item) {
                $customId = DB::select("INSERT INTO customs (item_id, ukuran_id, bahan_id, jenis_id, harga) VALUES (?, ?, ?, ?, ?)", [
                    $item->item_id,
                    $item->ukuran_id,
                    $item->bahan_id,
                    $item->jenis_id,
                    $item->harga_satuan
                ]);
                $customId = DB::getPdo()->lastInsertId();

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

            // Proses pembayaran
            if ($paymentMethod === 'qris') {
                Config::$serverKey = config('midtrans.server_key');
                Config::$is3ds = true;
                Config::$isSanitized = true;
                Config::$isProduction = false;

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
                    'payment_type' => 'qris',
                    'qris' => ['acquirer' => 'gopay']
                ];

                $snapToken = Snap::getSnapToken($params);

                Pembayaran::create([
                    'pesanan_id' => $pesanan->id,
                    'midtrans_order_id' => $pesanan->id,
                    'snap_token' => $snapToken,
                    'metode' => 'QRIS',
                    'status' => 'Pending',
                    'midtrans_response' => json_encode($params),
                ]);

                $this->clearProcessedCartItems($keranjangItems);
                DB::commit();

                return response()->json([
                    'success' => true,
                    'payment_method' => 'qris',
                    'delivery_method' => $deliveryMethod,
                    'metode_pengambilan' => $metodePengambilan,
                    'snap_token' => $snapToken,
                    'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v2/vtweb/' . $snapToken,
                    'order_id' => $pesanan->id,
                    'message' => 'Silakan lakukan pembayaran melalui QRIS'
                ], 201);
            } else {
                // COD
                Pembayaran::create([
                    'pesanan_id' => $pesanan->id,
                    'midtrans_order_id' => null,
                    'snap_token' => null,
                    'metode' => 'COD',
                    'status' => 'Lunas',
                    'midtrans_response' => null,
                ]);

                $this->clearProcessedCartItems($keranjangItems);
                DB::commit();

                return response()->json([
                    'success' => true,
                    'payment_method' => 'cod',
                    'delivery_method' => $deliveryMethod,
                    'metode_pengambilan' => $metodePengambilan,
                    'order_id' => $pesanan->id,
                    'message' => 'Pesanan berhasil dibuat. Pembayaran saat pesanan diterima.',
                    'redirect_url' => '/pesanan'
                ], 201);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal checkout: ' . $e->getMessage()], 500);
        }
    }

    private function clearProcessedCartItems($keranjangItems)
    {
        foreach ($keranjangItems as $item) {
            if ($item->upload_desain) {
                Storage::disk('public')->delete($item->upload_desain);
            }
            $item->delete();
        }
    }

    public function checkPaymentStatus(Request $request, $orderId)
    {
        $token = $request->bearerToken();
        if (!$token) {
            return response()->json(['success' => false, 'message' => 'Token tidak ditemukan'], 401);
        }

        $user = User::where('api_token', $token)->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Token tidak valid'], 401);
        }

        $pesanan = Pesanan::with('pembayaran')->where('id', $orderId)->where('user_id', $user->id)->first();
        if (!$pesanan) {
            return response()->json(['success' => false, 'message' => 'Pesanan tidak ditemukan'], 404);
        }

        return response()->json([
            'success' => true,
            'order_id' => $pesanan->id,
            'payment_status' => $pesanan->pembayaran->status ?? 'Unknown',
            'payment_method' => $pesanan->pembayaran->metode ?? 'Unknown',
            'order_status' => $pesanan->status,
            'delivery_method' => $pesanan->metode_pengiriman,
            'metode_pengambilan' => $pesanan->metode_pengambilan,
            'total' => $pesanan->total
        ]);
    }

    public function confirmCodPayment(Request $request, $orderId)
    {
        $token = $request->bearerToken();
        if (!$token) {
            return response()->json(['success' => false, 'message' => 'Token tidak ditemukan'], 401);
        }

        $user = User::where('api_token', $token)->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Token tidak valid'], 401);
        }

        $pesanan = Pesanan::with('pembayaran')->where('id', $orderId)->first();
        if (!$pesanan) {
            return response()->json(['success' => false, 'message' => 'Pesanan tidak ditemukan'], 404);
        }

        $pembayaran = $pesanan->pembayaran;
        if (!$pembayaran || $pembayaran->metode !== 'COD') {
            return response()->json(['success' => false, 'message' => 'Bukan pesanan COD'], 400);
        }

        if ($pembayaran->status === 'Lunas') {
            return response()->json(['success' => false, 'message' => 'Sudah dikonfirmasi'], 400);
        }

        $pembayaran->status = 'Lunas';
        $pembayaran->save();

        $pesanan->status = 'Selesai';
        $pesanan->save();

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran COD dikonfirmasi',
            'order_id' => $pesanan->id,
            'payment_status' => $pembayaran->status,
            'order_status' => $pesanan->status
        ]);
    }
}
