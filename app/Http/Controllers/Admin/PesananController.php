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
                'produk' => 'Multiple Produk (3)',
                'total' => 285000,
            ],
            [
                'id' => '0002',
                'tanggal' => '2025-04-06',
                'pelanggan' => 'Budi Santoso',
                'status' => 'Sedang Diproses',
                'metode' => 'Dikirim',
                'produk' => 'Multiple Produk (2)',
                'total' => 450000,
            ],
            // Data lainnya
        ];
        
        return view('admin.pesanan.index', compact('pesanan'));
    }
    
    /**
     * Menampilkan detail pesanan
     */
    public function show($id)
    {
        // Data pesanan (contoh untuk ID 0001 dengan multiple produk)
        $pesanan = [
            'id' => $id,
            'tanggal' => '2025-04-05',
            'pelanggan' => 'Ahmad Fauzi',
            'pelanggan_id' => 'A1',
            'status' => 'Pemesanan',
            'alamat' => 'Bandungan',
            'metode' => 'Ambil di Tempat',
            'total' => 285000,
            'estimasi_selesai' => '2025-04-10',
            'dengan_jasa_edit' => true,
            'catatan' => 'Tolong dikirim secepatnya.',
            'produk_items' => [
                [
                    'id' => 1,
                    'nama' => 'Kaos Lengan Panjang',
                    'bahan' => 'Katun',
                    'ukuran' => 'XL',
                    'jumlah' => 3,
                    'harga_satuan' => 50000,
                    'subtotal' => 150000,
                    'desain_customer' => 'desain_customer_1.jpg',
                    'desain_final' => 'desain_final_1.jpg',
                    'detail' => [
                        'jenis' => 'Lengan Panjang',
                        'gambar' => 'kaos_lengan_panjang.jpg',
                        'catatan' => 'Tidak ada catatan khusus.'
                    ]
                ],
                [
                    'id' => 2,
                    'nama' => 'Stiker',
                    'bahan' => 'Vinyl',
                    'ukuran' => '10x10 cm',
                    'jumlah' => 5,
                    'harga_satuan' => 20000,
                    'subtotal' => 100000,
                    'desain_customer' => 'desain_customer_2.jpg',
                    'desain_final' => 'desain_final_2.jpg',
                    'detail' => [
                        'jenis' => 'Stiker',
                        'gambar' => 'stiker.jpg',
                        'catatan' => 'Stiker cutting untuk outdoor.'
                    ]
                ],
                [
                    'id' => 3,
                    'nama' => 'Topi Sablon',
                    'bahan' => 'Canvas',
                    'ukuran' => 'All Size',
                    'jumlah' => 1,
                    'harga_satuan' => 35000,
                    'subtotal' => 35000,
                    'desain_customer' => 'desain_customer_3.jpg',
                    'desain_final' => 'desain_final_3.jpg',
                    'detail' => [
                        'jenis' => 'Topi',
                        'gambar' => 'topi_sablon.jpg',
                        'catatan' => 'Topi snapback dengan sablon custom'
                    ]
                ]
            ]
        ];
        
        // Jika tidak ada pesanan dengan ID yang diberikan
        if ($id != '0001' && $id != '0002') {
            return redirect()->route('admin.pesanan.index')
                ->with('error', "Pesanan #$id tidak ditemukan");
        }
        
        return view('admin.pesanan.show', compact('pesanan'));
    }
    
    /**
     * Mengubah status pesanan
     */
    public function updateStatus(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'status' => 'required|string|in:Pemesanan,Sedang Diproses,Menunggu Pengambilan,Sedang Dikirim,Selesai,Dibatalkan',
            'catatan' => 'nullable|string|max:255',
        ]);
        
        $status = $request->status;
        $catatan = $request->catatan;
        
        // Log untuk debugging
        Log::info('Mengubah status pesanan', [
            'id' => $id,
            'status' => $status,
            'catatan' => $catatan
        ]);
        
        // Dalam implementasi nyata, akan melakukan update ke database
        // $pesanan = Pesanan::find($id);
        // $pesanan->status = $status;
        // $pesanan->save();
        
        // Tambahkan ke riwayat status jika ada model yang sesuai
        // PesananStatus::create([
        //     'pesanan_id' => $id,
        //     'status' => $status,
        //     'keterangan' => $catatan,
        //     'user_id' => auth()->id()
        // ]);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Status pesanan #$id berhasil diubah menjadi $status",
                'status' => $status,
                'badgeClass' => $this->getBadgeClassForStatus($status)
            ]);
        }
        
        return redirect()->route('admin.pesanan.show', $id)
            ->with('success', "Status pesanan #$id berhasil diubah menjadi $status");
    }
    
    /**
     * Upload desain untuk produk dalam pesanan
     */
    public function uploadDesain(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'desain' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'produk_id' => 'required|integer',
            'tipe' => 'required|in:customer,final',
            'catatan' => 'nullable|string|max:255',
        ]);
        
        $produkId = $request->produk_id;
        $tipe = $request->tipe;
        
        // Log untuk debugging
        Log::info('Upload desain untuk produk dalam pesanan', [
            'pesanan_id' => $id,
            'produk_id' => $produkId,
            'tipe' => $tipe
        ]);
        
        // Upload file
        if ($request->hasFile('desain')) {
            $file = $request->file('desain');
            $fileName = 'desain_' . $tipe . '_pesanan_' . $id . '_produk_' . $produkId . '_' . time() . '.' . $file->getClientOriginalExtension();
            
            // Dalam implementasi nyata, simpan file ke storage
            $path = $file->storeAs('public/desain', $fileName);
            
            // Update database (implementasi nyata)
            // $detailPesanan = DetailPesanan::where('pesanan_id', $id)
            //     ->where('produk_id', $produkId)
            //     ->first();
            // 
            // if ($detailPesanan) {
            //     if ($tipe == 'customer') {
            //         $detailPesanan->desain_customer = $fileName;
            //     } else {
            //         $detailPesanan->desain_final = $fileName;
            //     }
            //     $detailPesanan->save();
            // }
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Desain berhasil diupload',
                    'file_name' => $fileName,
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
     * Mencetak invoice pesanan
     */
    public function printInvoice($id)
    {
        // Dalam aplikasi nyata, kode ini akan mengambil data pesanan termasuk semua item produk
        // dan mengirimkannya ke view invoice yang dirancang untuk dicetak
        
        // Contoh untuk simulasi:
        $pesanan = [
            'id' => $id,
            'tanggal' => '2025-04-05',
            'pelanggan' => 'Ahmad Fauzi',
            'alamat' => 'Bandungan',
            'metode' => 'Ambil di Tempat',
            'status' => 'Pemesanan',
            'total' => 285000,
            'produk_items' => [
                [
                    'nama' => 'Kaos Lengan Panjang',
                    'bahan' => 'Katun',
                    'ukuran' => 'XL',
                    'jumlah' => 3,
                    'harga_satuan' => 50000,
                    'subtotal' => 150000,
                ],
                [
                    'nama' => 'Stiker',
                    'bahan' => 'Vinyl',
                    'ukuran' => '10x10 cm',
                    'jumlah' => 5,
                    'harga_satuan' => 20000,
                    'subtotal' => 100000,
                ],
                [
                    'nama' => 'Topi Sablon',
                    'bahan' => 'Canvas',
                    'ukuran' => 'All Size',
                    'jumlah' => 1,
                    'harga_satuan' => 35000,
                    'subtotal' => 35000,
                ]
            ]
        ];
        
        return view('admin.pesanan.invoice', compact('pesanan'));
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
     * Proses pesanan untuk dicetak
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
        
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Pesanan #$id sedang diproses untuk dicetak"
            ]);
        }
        
        return redirect()->route('admin.pesanan.show', $id)
            ->with('success', "Pesanan #$id sedang diproses untuk dicetak");
    }
    
    /**
     * Mendapatkan detail produk dalam pesanan
     */
    public function getDetailProduk($pesananId, $produkId)
    {
        // Dalam implementasi nyata, ini akan mengambil detail dari database
        // Untuk contoh, kita gunakan data hard-coded
        
        $produkData = [
            '1' => [
                'id' => 1,
                'nama' => 'Kaos Lengan Panjang',
                'jenis' => 'Lengan Panjang',
                'bahan' => 'Katun',
                'ukuran' => 'XL',
                'harga' => 50000,
                'gambar' => 'kaos_lengan_panjang.jpg',
                'catatan' => 'Tidak ada catatan khusus.',
                'desain_customer' => 'desain_customer_1.jpg',
                'desain_final' => 'desain_final_1.jpg',
            ],
            '2' => [
                'id' => 2,
                'nama' => 'Stiker',
                'jenis' => 'Stiker',
                'bahan' => 'Vinyl',
                'ukuran' => '10x10 cm',
                'harga' => 20000,
                'gambar' => 'stiker.jpg',
                'catatan' => 'Stiker cutting untuk outdoor.',
                'desain_customer' => 'desain_customer_2.jpg',
                'desain_final' => 'desain_final_2.jpg',
            ],
            '3' => [
                'id' => 3,
                'nama' => 'Topi Sablon',
                'jenis' => 'Topi',
                'bahan' => 'Canvas',
                'ukuran' => 'All Size',
                'harga' => 35000,
                'gambar' => 'topi_sablon.jpg',
                'catatan' => 'Topi snapback dengan sablon custom',
                'desain_customer' => 'desain_customer_3.jpg',
                'desain_final' => 'desain_final_3.jpg',
            ],
        ];
        
        if (!isset($produkData[$produkId])) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }
        
        $produk = $produkData[$produkId];
        
        // Tambahkan URL untuk gambar
        $produk['gambar_url'] = asset('storage/product-images/' . $produk['gambar']);
        $produk['desain_customer_url'] = asset('storage/desain/' . $produk['desain_customer']);
        $produk['desain_final_url'] = asset('storage/desain/' . $produk['desain_final']);
        
        return response()->json([
            'success' => true,
            'produk' => $produk
        ]);
    }
    
    /**
     * Helper method untuk mendapatkan kelas badge berdasarkan status
     */
    private function getBadgeClassForStatus($status)
    {
        switch($status) {
            case 'Pemesanan':
                return 'bg-warning text-dark';
            case 'Sedang Diproses':
                return 'bg-info text-dark';
            case 'Menunggu Pengambilan':
                return 'bg-primary';
            case 'Sedang Dikirim':
                return 'bg-info';
            case 'Selesai':
                return 'bg-success';
            case 'Dibatalkan':
                return 'bg-danger';
            default:
                return 'bg-secondary';
        }
    }
}