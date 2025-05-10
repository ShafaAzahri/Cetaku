<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PesananController extends Controller
{
    /**
     * Menampilkan daftar pesanan
     */
    public function index()
    {
        // Data dummy untuk tampilan awal
        $pesanan = [
            [
                'id' => '0001',
                'tanggal' => '2025-04-05',
                'pelanggan' => 'Ahmad Fauzi',
                'status' => 'Pemesanan',
                'metode' => 'Ambil di Tempat',
                'produk' => 'Kaos Lengan Panjang',
                'produk_id' => 1,
                'total' => 250000,
            ],
            [
                'id' => '0002',
                'tanggal' => '2025-04-05',
                'pelanggan' => 'Budi Santoso',
                'status' => 'Sedang Diproses',
                'metode' => 'Dikirim',
                'produk' => 'Hoodie Premium',
                'produk_id' => 2,
                'total' => 450000,
            ],
            [
                'id' => '0003',
                'tanggal' => '2025-04-05',
                'pelanggan' => 'Citra Dewi',
                'status' => 'Menunggu Pengambilan',
                'metode' => 'Ambil di Tempat',
                'produk' => 'Jersey Custom',
                'produk_id' => 3,
                'total' => 175000,
            ],
            [
                'id' => '0004',
                'tanggal' => '2025-04-05',
                'pelanggan' => 'Deni Purnama',
                'status' => 'Sedang Dikirim',
                'metode' => 'Dikirim',
                'produk' => 'Topi Sablon',
                'produk_id' => 4,
                'total' => 320000,
            ],
            [
                'id' => '0005',
                'tanggal' => '2025-04-05',
                'pelanggan' => 'Eko Sulistyo',
                'status' => 'Selesai',
                'metode' => 'Ambil di Tempat',
                'produk' => 'Kaos Polo',
                'produk_id' => 5,
                'total' => 180000,
            ],
        ];
        
        // Data produk untuk modal
        $produk = [
            [
                'id' => 1,
                'nama' => 'Kaos Lengan Panjang',
                'jenis' => 'Lengan Panjang',
                'bahan' => 'Katun',
                'ukuran' => 'XL',
                'harga' => 50000,
                'gambar' => 'kaos_lengan_panjang.jpg',
                'catatan' => 'Tidak ada catatan khusus.'
            ],
            [
                'id' => 2,
                'nama' => 'Hoodie Premium',
                'jenis' => 'Hoodie',
                'bahan' => 'Cotton Fleece',
                'ukuran' => 'L',
                'harga' => 150000,
                'gambar' => 'hoodie_premium.jpg',
                'catatan' => 'Bahan premium, double layer'
            ],
            [
                'id' => 3,
                'nama' => 'Jersey Custom',
                'jenis' => 'Jersey',
                'bahan' => 'Dry Fit',
                'ukuran' => 'M',
                'harga' => 25000,
                'gambar' => 'jersey_custom.jpg',
                'catatan' => 'Jersey tim olahraga'
            ],
            [
                'id' => 4,
                'nama' => 'Topi Sablon',
                'jenis' => 'Topi',
                'bahan' => 'Canvas',
                'ukuran' => 'All Size',
                'harga' => 32000,
                'gambar' => 'topi_sablon.jpg',
                'catatan' => 'Topi snapback dengan sablon custom'
            ],
            [
                'id' => 5,
                'nama' => 'Kaos Polo',
                'jenis' => 'Polo',
                'bahan' => 'Lacoste',
                'ukuran' => 'L',
                'harga' => 45000,
                'gambar' => 'kaos_polo.jpg',
                'catatan' => 'Kaos polo untuk formal/semi-formal'
            ],
        ];
        
        return view('admin.pesanan.index', compact('pesanan', 'produk'));
    }
    
    /**
     * Menampilkan detail pesanan
     */
    public function show($id)
    {
        // Data pesanan spesifik (dummy)
        $pesananDetails = [
            '0001' => [
                'id' => '0001',
                'tanggal' => '2025-04-05',
                'pelanggan' => 'Ahmad Fauzi',
                'pelanggan_id' => 'A1',
                'status' => 'Pemesanan',
                'produk' => 'Kaos Lengan Panjang',
                'produk_id' => 1,
                'alamat' => 'Bandungan',
                'metode' => 'Ambil di Tempat',
                'jumlah' => 5,
                'total' => 250000,
                'estimasi_selesai' => '2025-04-10',
                'dengan_jasa_edit' => true,
            ],
            '0002' => [
                'id' => '0002',
                'tanggal' => '2025-04-05',
                'pelanggan' => 'Budi Santoso',
                'pelanggan_id' => 'B2',
                'status' => 'Sedang Diproses',
                'produk' => 'Hoodie Premium',
                'produk_id' => 2,
                'alamat' => 'Semarang',
                'metode' => 'Dikirim',
                'jumlah' => 3,
                'total' => 450000,
                'estimasi_selesai' => '2025-04-12',
                'dengan_jasa_edit' => true,
            ],
            '0003' => [
                'id' => '0003',
                'tanggal' => '2025-04-05',
                'pelanggan' => 'Citra Dewi',
                'pelanggan_id' => 'C3',
                'status' => 'Menunggu Pengambilan',
                'produk' => 'Jersey Custom',
                'produk_id' => 3,
                'alamat' => 'Yogyakarta',
                'metode' => 'Ambil di Tempat',
                'jumlah' => 7,
                'total' => 175000,
                'estimasi_selesai' => '2025-04-08',
                'dengan_jasa_edit' => false,
            ],
            '0004' => [
                'id' => '0004',
                'tanggal' => '2025-04-05',
                'pelanggan' => 'Deni Purnama',
                'pelanggan_id' => 'D4',
                'status' => 'Sedang Dikirim',
                'produk' => 'Topi Sablon',
                'produk_id' => 4,
                'alamat' => 'Jakarta',
                'metode' => 'Dikirim',
                'jumlah' => 10,
                'total' => 320000,
                'estimasi_selesai' => '2025-04-15',
                'dengan_jasa_edit' => true,
            ],
            '0005' => [
                'id' => '0005',
                'tanggal' => '2025-04-05',
                'pelanggan' => 'Eko Sulistyo',
                'pelanggan_id' => 'E5',
                'status' => 'Selesai',
                'produk' => 'Kaos Polo',
                'produk_id' => 5,
                'alamat' => 'Surabaya',
                'metode' => 'Ambil di Tempat',
                'jumlah' => 4,
                'total' => 180000,
                'estimasi_selesai' => '2025-04-07',
                'dengan_jasa_edit' => false,
            ],
        ];

        // Jika tidak ada pesanan dengan ID tersebut
        if (!isset($pesananDetails[$id])) {
            return redirect()->route('admin.pesanan.index')
                ->with('error', "Pesanan #$id tidak ditemukan");
        }
        
        $pesanan = $pesananDetails[$id];
        
        // Data produk (dummy)
        $produkId = $pesanan['produk_id'];
        $produk = $this->getProdukById($produkId);
        
        return view('admin.pesanan.show', compact('pesanan', 'produk'));
    }
    
    /**
     * Helper method untuk mendapatkan produk berdasarkan ID
     */
    private function getProdukById($id)
    {
        $produkData = [
            1 => [
                'id' => 1,
                'nama' => 'Kaos Lengan Panjang',
                'jenis' => 'Lengan Panjang',
                'bahan' => 'Katun',
                'ukuran' => 'XL',
                'harga' => 50000,
                'gambar' => 'kaos_lengan_panjang.jpg',
                'catatan' => 'Tidak ada catatan khusus.'
            ],
            2 => [
                'id' => 2,
                'nama' => 'Hoodie Premium',
                'jenis' => 'Hoodie',
                'bahan' => 'Cotton Fleece',
                'ukuran' => 'L',
                'harga' => 150000,
                'gambar' => 'hoodie_premium.jpg',
                'catatan' => 'Bahan premium, double layer'
            ],
            3 => [
                'id' => 3,
                'nama' => 'Jersey Custom',
                'jenis' => 'Jersey',
                'bahan' => 'Dry Fit',
                'ukuran' => 'M',
                'harga' => 25000,
                'gambar' => 'jersey_custom.jpg',
                'catatan' => 'Jersey tim olahraga'
            ],
            4 => [
                'id' => 4,
                'nama' => 'Topi Sablon',
                'jenis' => 'Topi',
                'bahan' => 'Canvas',
                'ukuran' => 'All Size',
                'harga' => 32000,
                'gambar' => 'topi_sablon.jpg',
                'catatan' => 'Topi snapback dengan sablon custom'
            ],
            5 => [
                'id' => 5,
                'nama' => 'Kaos Polo',
                'jenis' => 'Polo',
                'bahan' => 'Lacoste',
                'ukuran' => 'L',
                'harga' => 45000,
                'gambar' => 'kaos_polo.jpg',
                'catatan' => 'Kaos polo untuk formal/semi-formal'
            ],
        ];
        
        return $produkData[$id] ?? null;
    }
    
    /**
     * Mendapatkan detail produk (untuk AJAX)
     */
    public function getDetailProduk($id)
    {
        $produk = $this->getProdukById($id);
        
        if (!$produk) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'produk' => $produk
        ]);
    }
    
    /**
     * Mengubah status pesanan
     */
    public function updateStatus(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'status' => 'required|string|in:Pemesanan,Sedang Diproses,Menunggu Pengambilan,Sedang Dikirim,Selesai,Dibatalkan',
        ]);
        
        $status = $request->status;
        
        // Log untuk debugging
        Log::info('Mengubah status pesanan', [
            'id' => $id,
            'status' => $status
        ]);
        
        // Dalam implementasi nyata, akan melakukan update ke database
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Status pesanan #$id berhasil diubah menjadi $status"
            ]);
        }
        
        return redirect()->route('admin.pesanan.index')
            ->with('success', "Status pesanan #$id berhasil diubah menjadi $status");
    }
    
    /**
     * Mencetak invoice pesanan
     */
    public function printInvoice($id)
    {
        // Cek data pesanan (dummy)
        $pesananDetails = [
            '0001' => [
                'id' => '0001',
                'tanggal' => '2025-04-05',
                'pelanggan' => 'Ahmad Fauzi',
                'pelanggan_id' => 'A1',
                'status' => 'Pemesanan',
                'produk' => 'Kaos Lengan Panjang',
                'produk_id' => 1,
                'alamat' => 'Bandungan',
                'metode' => 'Ambil di Tempat',
                'jumlah' => 5,
                'total' => 250000,
                'estimasi_selesai' => '2025-04-10',
                'dengan_jasa_edit' => true,
            ],
            // Data dummy pesanan lain...
        ];
        
        // Jika tidak ada pesanan dengan ID tersebut
        if (!isset($pesananDetails[$id])) {
            return redirect()->route('admin.pesanan.index')
                ->with('error', "Pesanan #$id tidak ditemukan");
        }
        
        $pesanan = $pesananDetails[$id];
        
        // Data produk
        $produkId = $pesanan['produk_id'];
        $produk = $this->getProdukById($produkId);
        
        // Menampilkan halaman invoice dengan data pesanan
        return view('admin.pesanan.invoice', compact('pesanan', 'produk'));
    }
    
    /**
     * Upload desain pesanan
     */
    public function uploadDesain(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'desain' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'catatan' => 'nullable|string|max:255',
            'tipe' => 'required|in:customer,final', // customer=desain dari pelanggan, final=hasil edit
        ]);
        
        // Log untuk debugging
        Log::info('Upload desain untuk pesanan', [
            'id' => $id,
            'tipe' => $request->tipe
        ]);
        
        // Upload file
        if ($request->hasFile('desain')) {
            $file = $request->file('desain');
            $fileName = 'desain_' . $request->tipe . '_' . $id . '_' . time() . '.' . $file->getClientOriginalExtension();
            
            // Store file - dalam implementasi nyata, simpan ke storage yang benar
            $path = $file->storeAs('public/desain', $fileName);
            
            // Update database (implementasi nyata)
            // $pesanan = Pesanan::find($id);
            // if ($request->tipe == 'customer') {
            //     $pesanan->desain_pelanggan = $fileName;
            // } else {
            //     $pesanan->desain_final = $fileName;
            // }
            // $pesanan->save();
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Desain berhasil diupload',
                    'file_path' => Storage::url($path)
                ]);
            }
            
            return redirect()->route('admin.pesanan.show', $id)
                ->with('success', 'Desain berhasil diupload');
        }
        
        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada file yang diupload'
            ], 400);
        }
        
        return redirect()->route('admin.pesanan.show', $id)
            ->with('error', 'Tidak ada file yang diupload');
    }
    
    /**
     * Batalkan pesanan
     */
    public function cancel($id)
    {
        // Log untuk debugging
        Log::info('Membatalkan pesanan', [
            'id' => $id
        ]);
        
        // Dalam implementasi nyata, akan melakukan update status ke "Dibatalkan"
        // $pesanan = Pesanan::find($id);
        // $pesanan->status = 'Dibatalkan';
        // $pesanan->save();
        
        // Tambahkan ke riwayat status
        // PesananStatus::create([
        //     'pesanan_id' => $id,
        //     'status' => 'Dibatalkan',
        //     'keterangan' => 'Pesanan dibatalkan oleh admin',
        //     'user_id' => auth()->id()
        // ]);
        
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Pesanan #$id telah dibatalkan"
            ]);
        }
        
        return redirect()->route('admin.pesanan.index')
            ->with('success', "Pesanan #$id telah dibatalkan");
    }
    
    /**
     * Konfirmasi pengambilan pesanan
     */
    public function confirmPickup($id)
    {
        // Log untuk debugging
        Log::info('Konfirmasi pengambilan pesanan', [
            'id' => $id
        ]);
        
        // Dalam implementasi nyata, akan melakukan update status ke "Diambil"
        // $pesanan = Pesanan::find($id);
        // $pesanan->status = 'Diambil';
        // $pesanan->save();
        
        // Tambahkan ke riwayat status
        // PesananStatus::create([
        //     'pesanan_id' => $id,
        //     'status' => 'Diambil',
        //     'keterangan' => 'Pesanan telah diambil oleh pelanggan',
        //     'user_id' => auth()->id()
        // ]);
        
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Pengambilan pesanan #$id telah dikonfirmasi"
            ]);
        }
        
        return redirect()->route('admin.pesanan.index')
            ->with('success', "Pengambilan pesanan #$id telah dikonfirmasi");
    }
    
    /**
     * Update tracking pesanan
     */
    public function updateTracking(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'nomor_resi' => 'required|string|max:100',
            'kurir' => 'required|string|max:50',
            'estimasi_tiba' => 'nullable|date',
        ]);
        
        // Log untuk debugging
        Log::info('Update tracking pesanan', [
            'id' => $id,
            'resi' => $request->nomor_resi,
            'kurir' => $request->kurir
        ]);
        
        // Dalam implementasi nyata, akan menyimpan informasi tracking
        // $pengiriman = Pengiriman::where('pesanan_id', $id)->first();
        // if (!$pengiriman) {
        //     $pengiriman = new Pengiriman();
        //     $pengiriman->pesanan_id = $id;
        // }
        // 
        // $pengiriman->nomor_resi = $request->nomor_resi;
        // $pengiriman->kurir = $request->kurir;
        // $pengiriman->estimasi_tiba = $request->estimasi_tiba;
        // $pengiriman->save();
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Informasi tracking untuk pesanan #$id berhasil diupdate"
            ]);
        }
        
        return redirect()->route('admin.pesanan.show', $id)
            ->with('success', "Informasi tracking untuk pesanan #$id berhasil diupdate");
    }
    
    /**
     * Konfirmasi pengiriman pesanan
     */
    public function confirmShipment($id)
    {
        // Log untuk debugging
        Log::info('Konfirmasi pengiriman pesanan', [
            'id' => $id
        ]);
        
        // Dalam implementasi nyata, akan melakukan update status ke "Dikirim"
        // $pesanan = Pesanan::find($id);
        // $pesanan->status = 'Dikirim';
        // $pesanan->save();
        
        // Tambahkan ke riwayat status
        // PesananStatus::create([
        //     'pesanan_id' => $id,
        //     'status' => 'Dikirim',
        //     'keterangan' => 'Pesanan telah dikirim',
        //     'user_id' => auth()->id()
        // ]);
        
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Pengiriman pesanan #$id telah dikonfirmasi"
            ]);
        }
        
        return redirect()->route('admin.pesanan.index')
            ->with('success', "Pengiriman pesanan #$id telah dikonfirmasi");
    }
    
    /**
     * Menyelesaikan pesanan
     */
    public function complete($id)
    {
        // Log untuk debugging
        Log::info('Menyelesaikan pesanan', [
            'id' => $id
        ]);
        
        // Dalam implementasi nyata, akan melakukan update status ke "Selesai"
        // $pesanan = Pesanan::find($id);
        // $pesanan->status = 'Selesai';
        // $pesanan->save();
        
        // Tambahkan ke riwayat status
        // PesananStatus::create([
        //     'pesanan_id' => $id,
        //     'status' => 'Selesai',
        //     'keterangan' => 'Pesanan telah selesai',
        //     'user_id' => auth()->id()
        // ]);
        
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Pesanan #$id telah diselesaikan"
            ]);
        }
        
        return redirect()->route('admin.pesanan.index')
            ->with('success', "Pesanan #$id telah diselesaikan");
    }
    
    /**
     * Kirim notifikasi ke pelanggan
     */
    public function sendNotification(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'pesan' => 'required|string',
        ]);
        
        // Log untuk debugging
        Log::info('Kirim notifikasi ke pelanggan', [
            'id' => $id,
            'pesan' => $request->pesan
        ]);
        
        // Dalam implementasi nyata, akan mengirim notifikasi ke pelanggan
        // $pesanan = Pesanan::with('user')->find($id);
        
        // Kirim notifikasi via email
        // Mail::to($pesanan->user->email)->send(new PesananNotification($pesanan, $request->pesan));
        
        // Atau simpan notifikasi internal
        // Notification::create([
        //     'user_id' => $pesanan->user_id,
        //     'pesanan_id' => $id,
        //     'pesan' => $request->pesan,
        //     'dibaca' => false,
        // ]);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Notifikasi untuk pesanan #$id berhasil dikirim"
            ]);
        }
        
        return redirect()->route('admin.pesanan.show', $id)
            ->with('success', "Notifikasi untuk pesanan #$id berhasil dikirim");
    }
    
    /**
     * Halaman riwayat pesanan
     */
    public function history($id)
    {
        // Data pesanan
        $pesanan = [
            'id' => $id,
            'pelanggan' => 'Ahmad Fauzi',
            'status' => 'Diproses',
        ];
        
        // Data dummy untuk riwayat
        $history = [
            [
                'tanggal' => '2025-04-05 09:30:00',
                'status' => 'Pemesanan',
                'keterangan' => 'Pesanan dibuat oleh pelanggan',
                'user' => 'Ahmad Fauzi',
            ],
            [
                'tanggal' => '2025-04-05 10:15:00',
                'status' => 'Dikonfirmasi',
                'keterangan' => 'Pesanan dikonfirmasi oleh admin',
                'user' => 'Admin',
            ],
            [
                'tanggal' => '2025-04-06 14:20:00',
                'status' => 'Diproses',
                'keterangan' => 'Pesanan sedang diproses',
                'user' => 'Operator',
            ],
        ];
        
        return view('admin.pesanan.history', compact('pesanan', 'history'));
    }
    
    /**
     * Dashboard pesanan (ringkasan)
     */
    public function dashboard()
    {
        // Data statistik dummy
        $stats = [
            'total_pesanan' => 125,
            'pesanan_baru' => 15,
            'sedang_proses' => 35,
            'selesai' => 70,
            'dibatalkan' => 5,
            'pendapatan_bulan_ini' => 12500000,
            'pesanan_bulan_ini' => 45,
        ];
        
        // Data pesanan terbaru
        $pesananTerbaru = [
            [
                'id' => '0001',
                'tanggal' => '2025-04-05',
                'pelanggan' => 'Ahmad Fauzi',
                'status' => 'Pemesanan',
                'total' => 250000,
            ],
            [
                'id' => '0002',
                'tanggal' => '2025-04-05',
                'pelanggan' => 'Budi Santoso',
                'status' => 'Sedang Diproses',
                'total' => 450000,
            ],
            [
                'id' => '0003',
                'tanggal' => '2025-04-05',
                'pelanggan' => 'Citra Dewi',
                'status' => 'Menunggu Pengambilan',
                'total' => 175000,
            ],
        ];
        
        return view('admin.pesanan.dashboard', compact('stats', 'pesananTerbaru'));
    }
    
    /**
     * API Endpoint untuk mendapatkan data pesanan (untuk AJAX)
     */
    public function getDataForAjax(Request $request)
    {
        // Parameter filter
        $status = $request->input('status');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $search = $request->input('search');
        
        // Log untuk debugging
        Log::info('Request data pesanan via AJAX', [
            'status' => $status,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'search' => $search
        ]);
        
        // Dalam implementasi nyata, akan melakukan query ke database dengan filter
        // $query = Pesanan::with(['user', 'detailPesanan'])
        //     ->orderBy('created_at', 'desc');
        //     
        // if ($status) {
        //     $query->where('status', $status);
        // }
        // 
        // if ($startDate && $endDate) {
        //     $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        // }
        // 
        // if ($search) {
        //     $query->where(function($q) use ($search) {
        //         $q->where('id', 'like', "%$search%")
        //           ->orWhereHas('user', function($q) use ($search) {
        //               $q->where('nama', 'like', "%$search%");
        //           });
        //     });
        // }
        // 
        // $pesanan = $query->paginate(10);
        
        // Data dummy untuk tabel
        $pesanan = [
            [
                'id' => '0001',
                'tanggal' => '2025-04-05',
                'pelanggan' => 'Ahmad Fauzi',
                'status' => 'Pemesanan',
                'metode' => 'Ambil di Tempat',
                'produk' => 'Kaos Lengan Panjang',
                'produk_id' => 1,
                'total' => 250000,
            ],
            [
                'id' => '0002',
                'tanggal' => '2025-04-05',
                'pelanggan' => 'Budi Santoso',
                'status' => 'Sedang Diproses',
                'metode' => 'Dikirim',
                'produk' => 'Hoodie Premium',
                'produk_id' => 2,
                'total' => 450000,
            ],
            [
                'id' => '0003',
                'tanggal' => '2025-04-05',
                'pelanggan' => 'Citra Dewi',
                'status' => 'Menunggu Pengambilan',
                'metode' => 'Ambil di Tempat',
                'produk' => 'Jersey Custom',
                'produk_id' => 3,
                'total' => 175000,
            ],
            [
                'id' => '0004',
                'tanggal' => '2025-04-05',
                'pelanggan' => 'Deni Purnama',
                'status' => 'Sedang Dikirim',
                'metode' => 'Dikirim',
                'produk' => 'Topi Sablon',
                'produk_id' => 4,
                'total' => 320000,
            ],
            [
                'id' => '0005',
                'tanggal' => '2025-04-05',
                'pelanggan' => 'Eko Sulistyo',
                'status' => 'Selesai',
                'metode' => 'Ambil di Tempat',
                'produk' => 'Kaos Polo',
                'produk_id' => 5,
                'total' => 180000,
            ],
        ];
        
        // Filter data dummy berdasarkan parameter
        if ($status && $status !== 'all') {
            $pesanan = array_filter($pesanan, function($p) use ($status) {
                return stripos($p['status'], $status) !== false;
            });
        }
        
        if ($search) {
            $pesanan = array_filter($pesanan, function($p) use ($search) {
                return stripos($p['id'], $search) !== false || 
                       stripos($p['pelanggan'], $search) !== false ||
                       stripos($p['produk'], $search) !== false;
            });
        }
        
        // Reset array keys
        $pesanan = array_values($pesanan);
        
        return response()->json([
            'success' => true,
            'data' => $pesanan,
            'total' => count($pesanan),
        ]);
    }
    
    /**
     * Memproses pesanan untuk dicetak
     */
    public function processPrint($id)
    {
        // Log untuk debugging
        Log::info('Memproses pesanan untuk dicetak', [
            'id' => $id
        ]);
        
        // Dalam implementasi nyata, akan melakukan update status ke "Sedang Diproses"
        // $pesanan = Pesanan::find($id);
        // $pesanan->status = 'Sedang Diproses';
        // $pesanan->save();
        
        // Tambahkan ke riwayat status
        // PesananStatus::create([
        //     'pesanan_id' => $id,
        //     'status' => 'Sedang Diproses',
        //     'keterangan' => 'Pesanan sedang diproses untuk dicetak',
        //     'user_id' => auth()->id()
        // ]);
        
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Pesanan #$id sedang diproses untuk dicetak"
            ]);
        }
        
        return redirect()->route('admin.pesanan.index')
            ->with('success', "Pesanan #$id sedang diproses untuk dicetak");
    }
    
    /**
     * Cari pesanan berdasarkan ID atau nama pelanggan
     */
    public function search(Request $request)
    {
        $search = $request->input('search');
        
        if (empty($search)) {
            return redirect()->route('admin.pesanan.index');
        }
        
        // Log pencarian
        Log::info('Pencarian pesanan', [
            'search' => $search
        ]);
        
        // Dalam implementasi nyata, akan melakukan query ke database
        // $pesanan = Pesanan::where('id', 'like', "%$search%")
        //     ->orWhereHas('user', function($q) use ($search) {
        //         $q->where('nama', 'like', "%$search%");
        //     })
        //     ->orWhereHas('detailPesanan.custom.item', function($q) use ($search) {
        //         $q->where('nama_item', 'like', "%$search%");
        //     })
        //     ->get();
        
        // Data dummy untuk hasil pencarian
        $pesanan = [
            [
                'id' => '0001',
                'tanggal' => '2025-04-05',
                'pelanggan' => 'Ahmad Fauzi',
                'status' => 'Pemesanan',
                'metode' => 'Ambil di Tempat',
                'produk' => 'Kaos Lengan Panjang',
                'produk_id' => 1,
                'total' => 250000,
            ],
        ];
        
        return view('admin.pesanan.index', compact('pesanan', 'search'));
    }
    
    /**
     * Mendapatkan data untuk grafik statistik (untuk dashboard)
     */
    public function getChartData()
    {
        // Data dummy untuk grafik
        $chartData = [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            'pesanan' => [20, 25, 30, 35, 45, 40, 50, 55, 60, 65, 70, 75],
            'pendapatan' => [
                1000000, 1250000, 1500000, 1750000, 2250000, 2000000, 
                2500000, 2750000, 3000000, 3250000, 3500000, 3750000
            ],
        ];
        
        return response()->json([
            'success' => true,
            'data' => $chartData
        ]);
    }
    
    /**
     * Export data pesanan ke Excel/CSV
     */
    public function export(Request $request)
    {
        $format = $request->input('format', 'excel');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        // Log untuk debugging
        Log::info('Export data pesanan', [
            'format' => $format,
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);
        
        // Dalam implementasi nyata, akan menggunakan paket seperti:
        // Maatwebsite\Excel\Facades\Excel
        
        // Implementasi dummy
        $fileName = 'pesanan_' . date('Y-m-d') . '.' . ($format == 'excel' ? 'xlsx' : 'csv');
        
        // Return dummy response untuk frontend
        return response()->json([
            'success' => true,
            'message' => "Data pesanan telah diexport ke $fileName",
            'file' => $fileName
        ]);
    }
    
    /**
     * Import data pesanan dari Excel/CSV
     */
    public function import(Request $request)
    {
        // Validasi input
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);
        
        // Log untuk debugging
        Log::info('Import data pesanan');
        
        // Dalam implementasi nyata, akan menggunakan paket seperti:
        // Maatwebsite\Excel\Facades\Excel
        
        // Implementasi dummy
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = $file->getClientOriginalName();
            
            // Dalam implementasi nyata, proses file di sini
            // Excel::import(new PesananImport, $file);
            
            return redirect()->route('admin.pesanan.index')
                ->with('success', "Data pesanan dari $fileName berhasil diimport");
        }
        
        return redirect()->route('admin.pesanan.index')
            ->with('error', 'Tidak ada file yang diupload');
    }
    
    /**
     * API untuk mendapatkan produk by ID
     */
    public function getProduk($id)
    {
        $produk = $this->getProdukById($id);
        
        if (!$produk) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $produk
        ]);
    }
}