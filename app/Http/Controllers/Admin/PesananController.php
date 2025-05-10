<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Models\User;
use App\Models\Custom;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PesananController extends Controller
{
    /**
     * Menampilkan daftar pesanan
     */
    public function index(Request $request)
    {
        try {
            // Ambil perpage dari request atau default ke 10
            $perPage = $request->input('perpage', 10);
            
            // Validasi perPage agar selalu berupa angka valid
            $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;
            
            // Ambil data pesanan dari database dengan eager loading dan pagination
            $pesananQuery = Pesanan::with([
                'user', 
                'admin',
                'ekspedisi',
                'detailPesanans.custom.item',
                'detailPesanans.custom.ukuran',
                'detailPesanans.custom.bahan',
                'detailPesanans.custom.jenis',
                'detailPesanans.prosesPesanan'
            ])
            ->where('status', '!=', 'Dibatalkan'); // Filter pesanan dibatalkan
            
            // Tambahkan filter berdasarkan status jika ada
            if ($request->has('status') && $request->status != 'all') {
                $pesananQuery->where('status', $request->status);
            }
            
            // Tambahkan filter pencarian jika ada
            if ($request->has('search') && !empty($request->search)) {
                $searchTerm = $request->search;
                $pesananQuery->where(function($query) use ($searchTerm) {
                    $query->where('id', 'like', "%{$searchTerm}%")
                        ->orWhereHas('user', function($q) use ($searchTerm) {
                            $q->where('nama', 'like', "%{$searchTerm}%");
                        });
                });
            }
            
            // Order by tanggal dipesan
            $pesananQuery->orderBy('tanggal_dipesan', 'desc');
            
            // Jalankan pagination
            $pesananPaginated = $pesananQuery->paginate($perPage);
            
            // Transform data pesanan untuk tampilan
            $pesanan = $pesananPaginated->map(function($item) {
                $totalHarga = 0;
                $produkNama = 'Unknown Product';
                
                // Hitung total harga dari semua detail pesanan
                if ($item->detailPesanans->isNotEmpty()) {
                    $totalHarga = $item->detailPesanans->sum('total_harga');
                    
                    // Ambil nama produk dari detail pesanan pertama
                    $detailPesanan = $item->detailPesanans->first();
                    if ($detailPesanan && $detailPesanan->custom && $detailPesanan->custom->item) {
                        $produkNama = $detailPesanan->custom->item->nama_item;
                    }
                }
                
                return [
                    'id' => $item->id,
                    'tanggal' => $item->tanggal_dipesan ? $item->tanggal_dipesan->format('Y-m-d') : date('Y-m-d'),
                    'pelanggan' => $item->user ? $item->user->nama : 'Unknown',
                    'status' => $item->status,
                    'metode' => $item->metode_pengambilan == 'ambil' ? 'Ambil di Tempat' : 'Dikirim',
                    'produk' => $produkNama,
                    'total' => $totalHarga,
                    'detail_pesanans' => $item->detailPesanans
                ];
            });
            
            // Kirim pagination metadata
            $pagination = [
                'current_page' => $pesananPaginated->currentPage(),
                'last_page' => $pesananPaginated->lastPage(),
                'per_page' => $perPage,
                'total' => $pesananPaginated->total()
            ];
            
            return view('admin.pesanan.index', compact('pesanan', 'pagination', 'perPage'));
        } catch (\Exception $e) {
            Log::error('Error saat memuat daftar pesanan: ' . $e->getMessage());
            return view('admin.pesanan.index', ['pesanan' => collect([]), 'pagination' => null])
                ->with('error', 'Terjadi kesalahan saat memuat data pesanan: ' . $e->getMessage());
        }
    }
    
    /**
     * Menampilkan detail pesanan
     */
    public function show($id)
    {
        try {
            // Ambil data pesanan dari database
            $pesanan = Pesanan::with([
                'user',
                'admin', 
                'ekspedisi',
                'detailPesanans.custom.item',
                'detailPesanans.custom.ukuran',
                'detailPesanans.custom.bahan',
                'detailPesanans.custom.jenis',
                'pembayaran'
            ])->find($id);
            
            if (!$pesanan) {
                return redirect()->route('admin.pesanan.index')
                    ->with('error', "Pesanan #$id tidak ditemukan");
            }
            
            // Transform data pesanan untuk template
            $pesananData = [
                'id' => $pesanan->id,
                'tanggal' => $pesanan->tanggal_dipesan ? $pesanan->tanggal_dipesan->format('Y-m-d') : date('Y-m-d'),
                'pelanggan' => $pesanan->user ? $pesanan->user->nama : 'Unknown',
                'pelanggan_id' => $pesanan->user ? $pesanan->user->id : 'Unknown',
                'status' => $pesanan->status,
                'alamat' => $pesanan->user && isset($pesanan->user->alamats[0]) ? $pesanan->user->alamats[0]->alamat_lengkap : 'Belum ada alamat',
                'metode' => $pesanan->metode_pengambilan == 'ambil' ? 'Ambil di Tempat' : 'Dikirim',
                'total' => $pesanan->detailPesanans->sum('total_harga'),
                'estimasi_selesai' => $pesanan->estimasi_waktu ? date('Y-m-d', strtotime('+' . $pesanan->estimasi_waktu . ' days')) : 'Belum ditentukan',
                'dengan_jasa_edit' => $pesanan->detailPesanans->where('tipe_desain', 'dibuatkan')->count() > 0,
                'catatan' => $pesanan->catatan ?? 'Tidak ada catatan',
                'produk_items' => []
            ];
            
            // Tambahkan detail produk
            foreach ($pesanan->detailPesanans as $detail) {
                $custom = $detail->custom;
                $item = $custom && $custom->item ? $custom->item : null;
                $ukuran = $custom && $custom->ukuran ? $custom->ukuran : null;
                $bahan = $custom && $custom->bahan ? $custom->bahan : null;
                $jenis = $custom && $custom->jenis ? $custom->jenis : null;
                
                $produkItem = [
                    'id' => $detail->id,
                    'nama' => $item ? $item->nama_item : 'Produk tidak diketahui',
                    'bahan' => $bahan ? $bahan->nama_bahan : 'Unknown',
                    'ukuran' => $ukuran ? $ukuran->size : 'Unknown',
                    'jumlah' => $detail->jumlah,
                    'harga_satuan' => $custom ? $custom->harga : 0,
                    'subtotal' => $detail->total_harga,
                    'desain_customer' => $detail->upload_desain,
                    'desain_final' => $detail->desain_revisi,
                    'detail' => [
                        'jenis' => $jenis ? $jenis->kategori : 'Unknown',
                        'gambar' => $item && $item->gambar ? $item->gambar : null,
                        'catatan' => $detail->catatan ?? 'Tidak ada catatan khusus.'
                    ]
                ];
                
                $pesananData['produk_items'][] = $produkItem;
            }
            
            return view('admin.pesanan.show', ['pesanan' => $pesananData]);
        } catch (\Exception $e) {
            Log::error('Error saat memuat detail pesanan: ' . $e->getMessage());
            return redirect()->route('admin.pesanan.index')
                ->with('error', "Terjadi kesalahan saat memuat detail pesanan #$id");
        }
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
        
        try {
            $pesanan = Pesanan::find($id);
            
            if (!$pesanan) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => "Pesanan #$id tidak ditemukan"
                    ], 404);
                }
                
                return redirect()->route('admin.pesanan.index')
                    ->with('error', "Pesanan #$id tidak ditemukan");
            }
            
            $oldStatus = $pesanan->status;
            $pesanan->status = $request->status;
            
            if ($request->has('catatan') && !empty($request->catatan)) {
                $pesanan->catatan = $request->catatan;
            }
            
            $pesanan->save();
            
            // Log perubahan status
            Log::info('Status pesanan diubah', [
                'id' => $id,
                'old_status' => $oldStatus,
                'new_status' => $request->status,
                'admin_id' => session('user')['id'] ?? null
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Status pesanan #$id berhasil diubah menjadi $request->status",
                    'status' => $request->status,
                    'badgeClass' => $this->getBadgeClassForStatus($request->status)
                ]);
            }
            
            return redirect()->route('admin.pesanan.show', $id)
                ->with('success', "Status pesanan #$id berhasil diubah menjadi $request->status");
        } catch (\Exception $e) {
            Log::error('Error saat update status pesanan: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => "Terjadi kesalahan saat mengubah status pesanan: " . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', "Terjadi kesalahan saat mengubah status pesanan: " . $e->getMessage());
        }
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
        
        try {
            $produkId = $request->produk_id;
            $tipe = $request->tipe;
            
            // Cek detail pesanan
            $detailPesanan = DetailPesanan::where('id', $produkId)
                ->where('pesanan_id', $id)
                ->first();
                
            if (!$detailPesanan) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Detail pesanan tidak ditemukan'
                    ], 404);
                }
                
                return redirect()->back()
                    ->with('error', 'Detail pesanan tidak ditemukan');
            }
            
            // Upload file
            if ($request->hasFile('desain')) {
                $file = $request->file('desain');
                $fileName = 'desain_' . $tipe . '_pesanan_' . $id . '_produk_' . $produkId . '_' . time() . '.' . $file->getClientOriginalExtension();
                
                // Simpan file ke storage
                $path = $file->storeAs('public/desain', $fileName);
                
                // Update detail pesanan
                if ($tipe == 'customer') {
                    $detailPesanan->upload_desain = $fileName;
                } else {
                    $detailPesanan->desain_revisi = $fileName;
                }
                
                if ($request->has('catatan') && !empty($request->catatan)) {
                    $detailPesanan->catatan = $request->catatan;
                }
                
                $detailPesanan->save();
                
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
        } catch (\Exception $e) {
            Log::error('Error saat upload desain: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => "Terjadi kesalahan saat mengupload desain: " . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', "Terjadi kesalahan saat mengupload desain: " . $e->getMessage());
        }
    }
    
    /**
     * Mencetak invoice pesanan
     */
    public function printInvoice($id)
    {
        try {
            $pesanan = Pesanan::with([
                'user',
                'admin',
                'ekspedisi',
                'detailPesanans.custom.item',
                'detailPesanans.custom.ukuran',
                'detailPesanans.custom.bahan',
                'detailPesanans.custom.jenis',
                'pembayaran'
            ])->find($id);
            
            if (!$pesanan) {
                return redirect()->route('admin.pesanan.index')
                    ->with('error', "Pesanan #$id tidak ditemukan");
            }
            
            // Transform data untuk template invoice
            $pesananData = [
                'id' => $pesanan->id,
                'tanggal' => $pesanan->tanggal_dipesan ? $pesanan->tanggal_dipesan->format('Y-m-d') : date('Y-m-d'),
                'pelanggan' => $pesanan->user ? $pesanan->user->nama : 'Unknown',
                'alamat' => $pesanan->user && isset($pesanan->user->alamats[0]) ? $pesanan->user->alamats[0]->alamat_lengkap : 'Belum ada alamat',
                'metode' => $pesanan->metode_pengambilan == 'ambil' ? 'Ambil di Tempat' : 'Dikirim',
                'status' => $pesanan->status,
                'total' => $pesanan->detailPesanans->sum('total_harga'),
                'produk_items' => []
            ];
            
            // Tambahkan detail produk
            foreach ($pesanan->detailPesanans as $detail) {
                $custom = $detail->custom;
                $item = $custom && $custom->item ? $custom->item : null;
                $ukuran = $custom && $custom->ukuran ? $custom->ukuran : null;
                $bahan = $custom && $custom->bahan ? $custom->bahan : null;
                
                $produkItem = [
                    'nama' => $item ? $item->nama_item : 'Produk tidak diketahui',
                    'bahan' => $bahan ? $bahan->nama_bahan : 'Unknown',
                    'ukuran' => $ukuran ? $ukuran->size : 'Unknown',
                    'jumlah' => $detail->jumlah,
                    'harga_satuan' => $custom ? $custom->harga : 0,
                    'subtotal' => $detail->total_harga,
                ];
                
                $pesananData['produk_items'][] = $produkItem;
            }
            
            return view('admin.pesanan.invoice', compact('pesananData'));
        } catch (\Exception $e) {
            Log::error('Error saat mencetak invoice: ' . $e->getMessage());
            return redirect()->route('admin.pesanan.index')
                ->with('error', "Terjadi kesalahan saat mencetak invoice #$id");
        }
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