<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Models\ProsesPesanan;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class PesananController extends Controller
{
    protected $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = rtrim(env('API_URL', config('app.url')), '/');
    }

    /**
     * Menampilkan daftar pesanan
     */
    public function index(Request $request)
    {
        // Buat query awal
        $query = Pesanan::with([
            'user', 
            'admin', 
            'ekspedisi', 
            'detailPesanans.custom.item'
        ]);
        
        // Filter status
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }
        
        // Filter status: Hanya tampilkan pesanan yang belum selesai/batal
        $query->whereNotIn('status', ['Selesai', 'Dibatalkan']);
        
        // Pencarian
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('id', 'like', "%{$searchTerm}%")
                ->orWhereHas('user', function($userQuery) use ($searchTerm) {
                    $userQuery->where('nama', 'like', "%{$searchTerm}%");
                });
            });
        }
        
        // Pengurutan
        $query->orderBy('tanggal_dipesan', 'desc');
        
        // Pagination
        $perPage = $request->input('perpage', 10);
        $pesananData = $query->paginate($perPage);
        
        // Format data untuk view
        $pesanan = collect($pesananData->items())->map(function($item) {
            $totalHarga = 0;
            $produkNama = '';
            
            // Dapatkan detail produk dari detail pesanan pertama
            if (!empty($item->detailPesanans)) {
                $detailPesanan = $item->detailPesanans->first();
                $produkNama = $detailPesanan->custom->item->nama_item ?? 'Produk tidak diketahui';
                
                // Hitung total harga
                foreach ($item->detailPesanans as $detail) {
                    $totalHarga += $detail->total_harga;
                }
            }
            
            return [
                'id' => $item->id,
                'tanggal' => isset($item->tanggal_dipesan) ? date('Y-m-d', strtotime($item->tanggal_dipesan)) : date('Y-m-d'),
                'pelanggan' => $item->user->nama ?? 'Unknown',
                'status' => $item->status,
                'metode' => $item->metode_pengambilan == 'ambil' ? 'Ambil di Tempat' : 'Dikirim',
                'produk' => $produkNama,
                'total' => $totalHarga,
                'detail_pesanans' => $item->detailPesanans
            ];
        });
        
        $pagination = [
            'current_page' => $pesananData->currentPage(),
            'last_page' => $pesananData->lastPage(),
            'per_page' => $pesananData->perPage(),
            'total' => $pesananData->total()
        ];
        
        // Ambil daftar operator dan mesin untuk halaman proses
        $operators = \App\Models\Operator::where('status', 'aktif')->get();
        $mesins = \App\Models\Mesin::where('status', 'aktif')->get();
        
        return view('admin.pesanan.index', compact('pesanan', 'pagination', 'perPage', 'operators', 'mesins'));
    }
    
    /**
     * Menampilkan detail pesanan
     */
    public function show($id)
    {
        $pesanan = Pesanan::with([
            'user.alamats', 
            'admin', 
            'ekspedisi', 
            'detailPesanans.custom.item',
            'detailPesanans.custom.ukuran',
            'detailPesanans.custom.bahan',
            'detailPesanans.custom.jenis',
            'detailPesanans.prosesPesanan'
        ])->find($id);
        
        if (!$pesanan) {
            return redirect()->route('admin.pesanan.index')
                ->with('error', 'Pesanan tidak ditemukan');
        }
        
        // Format data untuk view
        $pesananFormatted = [
            'id' => $pesanan->id,
            'tanggal' => date('Y-m-d', strtotime($pesanan->tanggal_dipesan ?? 'now')),
            'pelanggan' => $pesanan->user->nama ?? 'Unknown',
            'pelanggan_id' => $pesanan->user->id ?? null,
            'status' => $pesanan->status,
            'alamat' => !empty($pesanan->user->alamats) ? $pesanan->user->alamats->first()->alamat_lengkap : 'Belum ada alamat',
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
            $item = $custom->item ?? null;
            $ukuran = $custom->ukuran ?? null;
            $bahan = $custom->bahan ?? null;
            $jenis = $custom->jenis ?? null;
            
            $produkItem = [
                'id' => $detail->id,
                'nama' => $item ? $item->nama_item : 'Produk tidak diketahui',
                'bahan' => $bahan ? $bahan->nama_bahan : 'Unknown',
                'ukuran' => $ukuran ? $ukuran->size : 'Unknown',
                'jumlah' => $detail->jumlah,
                'harga_satuan' => $custom ? $custom->harga : 0,
                'subtotal' => $detail->total_harga,
                'desain_customer' => $detail->upload_desain ?? null,
                'desain_final' => $detail->desain_revisi ?? null,
                'detail' => [
                    'jenis' => $jenis ? $jenis->kategori : 'Unknown',
                    'gambar' => $item && $item->gambar ? $item->gambar : null,
                    'catatan' => $detail->catatan ?? 'Tidak ada catatan khusus.'
                ]
            ];
            
            $pesananFormatted['produk_items'][] = $produkItem;
        }
        
        // Daftar status untuk form update
        $statusList = [
            'Pemesanan' => 'Pemesanan',
            'Dikonfirmasi' => 'Dikonfirmasi',
            'Sedang Diproses' => 'Sedang Diproses',
            'Menunggu Pengambilan' => 'Menunggu Pengambilan',
            'Sedang Dikirim' => 'Sedang Dikirim',
            'Selesai' => 'Selesai',
            'Dibatalkan' => 'Dibatalkan'
        ];
        
        return view('admin.pesanan.show', [
            'pesanan' => $pesananFormatted,
            'statusList' => $statusList,
            'operators' => \App\Models\Operator::where('status', 'aktif')->get(),
            'mesins' => \App\Models\Mesin::where('status', 'aktif')->get()
        ]);
    }
    
    /**
     * Konfirmasi pesanan - halaman form
     */
    public function konfirmasi($id)
    {
        $pesanan = Pesanan::find($id);
        
        if (!$pesanan) {
            return redirect()->route('admin.pesanan.index')
                ->with('error', 'Pesanan tidak ditemukan');
        }
        
        if ($pesanan->status !== 'Pemesanan') {
            return redirect()->route('admin.pesanan.show', $id)
                ->with('error', 'Pesanan ini tidak dalam status Pemesanan');
        }
        
        return view('admin.pesanan.konfirmasi', ['pesanan' => $pesanan]);
    }
    
    /**
     * Proses konfirmasi pesanan
     */
    public function prosesKonfirmasi(Request $request, $id)
    {
        $pesanan = Pesanan::find($id);
        
        if (!$pesanan) {
            return redirect()->route('admin.pesanan.index')
                ->with('error', 'Pesanan tidak ditemukan');
        }
        
        if ($pesanan->status !== 'Pemesanan') {
            return redirect()->route('admin.pesanan.show', $id)
                ->with('error', 'Pesanan ini tidak dalam status Pemesanan');
        }
        
        $validator = Validator::make($request->all(), [
            'catatan' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Update status pesanan dan admin_id
        $pesanan->status = 'Dikonfirmasi';
        $pesanan->admin_id = Auth::id();
        
        if ($request->has('catatan') && !empty($request->catatan)) {
            $pesanan->catatan = $request->catatan;
        }
        
        $pesanan->save();
        
        return redirect()->route('admin.pesanan.show', $id)
            ->with('success', 'Pesanan berhasil dikonfirmasi');
    }
    
    /**
     * Halaman proses print
     */
    public function proses($id)
    {
        $pesanan = Pesanan::with('detailPesanans.custom.item')->find($id);
        
        if (!$pesanan) {
            return redirect()->route('admin.pesanan.index')
                ->with('error', 'Pesanan tidak ditemukan');
        }
        
        if ($pesanan->status !== 'Dikonfirmasi' && $pesanan->status !== 'Sedang Diproses') {
            return redirect()->route('admin.pesanan.show', $id)
                ->with('error', 'Pesanan ini tidak dalam status yang valid untuk diproses');
        }
        
        $operators = \App\Models\Operator::where('status', 'aktif')->get();
        $mesins = \App\Models\Mesin::where('status', 'aktif')->get();
        
        return view('admin.pesanan.proses', [
            'pesanan' => $pesanan,
            'operators' => $operators,
            'mesins' => $mesins
        ]);
    }
    
    /**
     * Proses cetak pesanan
     */
    public function prosesPrint(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'operator_id' => 'required|exists:operators,id',
            'mesin_id' => 'required|exists:mesins,id',
            'detail_pesanan_id' => 'nullable|exists:detail_pesanans,id',
            'catatan' => 'nullable|string|max:255'
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $pesanan = Pesanan::with('detailPesanans')->find($id);
        
        if (!$pesanan) {
            return redirect()->route('admin.pesanan.index')
                ->with('error', 'Pesanan tidak ditemukan');
        }
        
        // Jika detail_pesanan_id kosong, proses semua detail pesanan
        if (empty($request->detail_pesanan_id)) {
            foreach ($pesanan->detailPesanans as $detail) {
                // Cek apakah detail ini sudah memiliki proses
                $existingProcess = ProsesPesanan::where('detail_pesanan_id', $detail->id)->first();
                if (!$existingProcess) {
                    // Buat proses baru
                    $prosesPesanan = new ProsesPesanan();
                    $prosesPesanan->detail_pesanan_id = $detail->id;
                    $prosesPesanan->operator_id = $request->operator_id;
                    $prosesPesanan->mesin_id = $request->mesin_id;
                    $prosesPesanan->waktu_mulai = now();
                    $prosesPesanan->status_proses = 'Ditugaskan';
                    
                    if ($request->has('catatan')) {
                        $prosesPesanan->catatan = $request->catatan;
                    }
                    
                    $prosesPesanan->save();
                }
            }
        } else {
            // Proses hanya detail yang dipilih
            $detail = DetailPesanan::find($request->detail_pesanan_id);
            
            if (!$detail || $detail->pesanan_id != $pesanan->id) {
                return redirect()->back()
                    ->with('error', 'Detail pesanan tidak valid');
            }
            
            // Cek apakah detail ini sudah memiliki proses
                            $existingProcess = ProsesPesanan::where('detail_pesanan_id', $detail->id)->first();
            if (!$existingProcess) {
                // Buat proses baru
                $prosesPesanan = new ProsesPesanan();
                $prosesPesanan->detail_pesanan_id = $detail->id;
                $prosesPesanan->operator_id = $request->operator_id;
                $prosesPesanan->mesin_id = $request->mesin_id;
                $prosesPesanan->waktu_mulai = now();
                $prosesPesanan->status_proses = 'Ditugaskan';
                
                if ($request->has('catatan')) {
                    $prosesPesanan->catatan = $request->catatan;
                }
                
                $prosesPesanan->save();
            } else {
                return redirect()->back()
                    ->with('error', 'Detail pesanan ini sudah memiliki proses');
            }
        }
        
        // Update status pesanan menjadi "Sedang Diproses" jika belum
        if ($pesanan->status !== 'Sedang Diproses') {
            $pesanan->status = 'Sedang Diproses';
            $pesanan->save();
        }
        
        return redirect()->route('admin.pesanan.show', $id)
            ->with('success', 'Pesanan berhasil masuk proses cetak');
    }
    
    /**
     * Halaman konfirmasi pengiriman
     */
    public function kirim($id)
    {
        $pesanan = Pesanan::find($id);
        
        if (!$pesanan) {
            return redirect()->route('admin.pesanan.index')
                ->with('error', 'Pesanan tidak ditemukan');
        }
        
        if ($pesanan->status !== 'Sedang Diproses' || $pesanan->metode_pengambilan !== 'antar') {
            return redirect()->route('admin.pesanan.show', $id)
                ->with('error', 'Pesanan ini tidak dalam status yang valid untuk pengiriman');
        }
        
        return view('admin.pesanan.kirim', ['pesanan' => $pesanan]);
    }
    
    /**
     * Proses konfirmasi pengiriman
     */
    public function prosesKirim(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'no_resi' => 'nullable|string|max:50',
            'catatan' => 'nullable|string|max:255'
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $pesanan = Pesanan::find($id);
        
        if (!$pesanan) {
            return redirect()->route('admin.pesanan.index')
                ->with('error', 'Pesanan tidak ditemukan');
        }
        
        if ($pesanan->status !== 'Sedang Diproses') {
            return redirect()->route('admin.pesanan.show', $id)
                ->with('error', 'Pesanan tidak dalam status Sedang Diproses');
        }
        
        // Update status pesanan
        $pesanan->status = 'Sedang Dikirim';
        
        // Simpan no resi dan catatan
        if ($request->has('no_resi') && !empty($request->no_resi)) {
            $pesanan->catatan = $pesanan->catatan 
                ? $pesanan->catatan . "\n\nNo. Resi: " . $request->no_resi
                : "No. Resi: " . $request->no_resi;
        }
        
        if ($request->has('catatan') && !empty($request->catatan)) {
            $pesanan->catatan = $pesanan->catatan 
                ? $pesanan->catatan . "\n\nCatatan pengiriman: " . $request->catatan
                : "Catatan pengiriman: " . $request->catatan;
        }
        
        $pesanan->save();
        
        return redirect()->route('admin.pesanan.show', $id)
            ->with('success', 'Pesanan berhasil dikirim');
    }
    
    /**
     * Konfirmasi pengambilan pesanan
     */
    public function confirmPickup($id)
    {
        $pesanan = Pesanan::find($id);
        
        if (!$pesanan) {
            return redirect()->route('admin.pesanan.index')
                ->with('error', 'Pesanan tidak ditemukan');
        }
        
        if ($pesanan->status !== 'Menunggu Pengambilan') {
            return redirect()->route('admin.pesanan.show', $id)
                ->with('error', 'Pesanan tidak dalam status Menunggu Pengambilan');
        }
        
        // Update status pesanan
        $pesanan->status = 'Selesai';
        $pesanan->save();
        
        return redirect()->route('admin.pesanan.index')
            ->with('success', 'Pengambilan pesanan berhasil dikonfirmasi');
    }
    
    /**
     * Konfirmasi penerimaan pesanan
     */
    public function confirmDelivery($id)
    {
        $pesanan = Pesanan::find($id);
        
        if (!$pesanan) {
            return redirect()->route('admin.pesanan.index')
                ->with('error', 'Pesanan tidak ditemukan');
        }
        
        if ($pesanan->status !== 'Sedang Dikirim') {
            return redirect()->route('admin.pesanan.show', $id)
                ->with('error', 'Pesanan tidak dalam status Sedang Dikirim');
        }
        
        // Update status pesanan
        $pesanan->status = 'Selesai';
        $pesanan->save();
        
        return redirect()->route('admin.pesanan.index')
            ->with('success', 'Penerimaan pesanan berhasil dikonfirmasi');
    }
    
    /**
     * Batalkan pesanan
     */
    public function cancel(Request $request, $id)
    {
        $pesanan = Pesanan::find($id);
        
        if (!$pesanan) {
            return redirect()->route('admin.pesanan.index')
                ->with('error', 'Pesanan tidak ditemukan');
        }
        
        if ($pesanan->status == 'Selesai' || $pesanan->status == 'Dibatalkan') {
            return redirect()->route('admin.pesanan.show', $id)
                ->with('error', 'Pesanan ini tidak dapat dibatalkan');
        }
        
        // Update status pesanan
        $pesanan->status = 'Dibatalkan';
        
        if ($request->has('alasan') && !empty($request->alasan)) {
            $pesanan->catatan = $pesanan->catatan 
                ? $pesanan->catatan . "\n\nDibatalkan dengan alasan: " . $request->alasan
                : "Dibatalkan dengan alasan: " . $request->alasan;
        } else {
            $pesanan->catatan = $pesanan->catatan 
                ? $pesanan->catatan . "\n\nPesanan dibatalkan oleh admin"
                : "Pesanan dibatalkan oleh admin";
        }
        
        $pesanan->save();
        
        return redirect()->route('admin.pesanan.index')
            ->with('success', 'Pesanan berhasil dibatalkan');
    }
    
    /**
     * Mengubah status pesanan.
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:Pemesanan,Dikonfirmasi,Sedang Diproses,Menunggu Pengambilan,Sedang Dikirim,Selesai,Dibatalkan',
            'catatan' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return back()->with('error', $validator->errors()->first());
        }

        $pesanan = Pesanan::find($id);
        
        if (!$pesanan) {
            return back()->with('error', 'Pesanan tidak ditemukan');
        }
        
        // Update status
        $oldStatus = $pesanan->status;
        $pesanan->status = $request->status;
        
        // Set admin_id jika status berubah menjadi Dikonfirmasi
        if ($request->status === 'Dikonfirmasi' && $oldStatus !== 'Dikonfirmasi') {
            $pesanan->admin_id = Auth::id();
        }
        
        // Update catatan jika diberikan
        if ($request->has('catatan') && !empty($request->catatan)) {
            $pesanan->catatan = $request->catatan;
        }
        
        $pesanan->save();
        
        return redirect()->route('admin.pesanan.show', $id)
            ->with('success', 'Status pesanan berhasil diperbarui');
    }
    
    /**
     * Menampilkan detail produk dalam pesanan
     */
    public function getDetailProduk($id, $produkId)
    {
        $pesanan = Pesanan::find($id);
        
        if (!$pesanan) {
            return redirect()->route('admin.pesanan.index')
                ->with('error', 'Pesanan tidak ditemukan');
        }
        
        $detailPesanan = DetailPesanan::with([
                'custom.item', 
                'custom.ukuran', 
                'custom.bahan', 
                'custom.jenis'
            ])
            ->where('id', $produkId)
            ->where('pesanan_id', $id)
            ->first();
        
        if (!$detailPesanan) {
            return redirect()->route('admin.pesanan.show', $id)
                ->with('error', 'Detail pesanan tidak ditemukan');
        }
        
        // Format data untuk view
        $produk = [
            'id' => $detailPesanan->id,
            'nama' => $detailPesanan->custom->item->nama_item ?? 'Produk tidak diketahui',
            'jenis' => $detailPesanan->custom->jenis->kategori ?? 'Unknown',
            'bahan' => $detailPesanan->custom->bahan->nama_bahan ?? 'Unknown',
            'ukuran' => $detailPesanan->custom->ukuran->size ?? 'Unknown',
            'jumlah' => $detailPesanan->jumlah,
            'harga' => $detailPesanan->custom->harga ?? 0,
            'subtotal' => $detailPesanan->total_harga,
            'tipe_desain' => $detailPesanan->tipe_desain,
            'biaya_desain' => $detailPesanan->biaya_jasa ?? 0,
            'catatan' => $detailPesanan->catatan ?? 'Tidak ada catatan khusus',
            'gambar_url' => $detailPesanan->custom->item && $detailPesanan->custom->item->gambar 
                ? asset('storage/' . $detailPesanan->custom->item->gambar) 
                : asset('images/no-image.png'),
            'desain_customer_url' => $detailPesanan->upload_desain 
                ? asset('storage/desain/' . $detailPesanan->upload_desain) 
                : null,
            'desain_final_url' => $detailPesanan->desain_revisi 
                ? asset('storage/desain/' . $detailPesanan->desain_revisi) 
                : null,
        ];
        
        return view('admin.pesanan.detail-produk', [
            'pesanan' => $pesanan,
            'produk' => $produk,
            'alamat' => $pesanan->user && $pesanan->user->alamats->isNotEmpty() 
                ? $pesanan->user->alamats->first()->alamat_lengkap 
                : 'Belum ada alamat',
            'pelanggan' => $pesanan->user
        ]);
    }
    
    /**
     * Upload desain untuk produk dalam pesanan
     */
    public function uploadDesain(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'desain' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'produk_id' => 'required|integer',
            'tipe' => 'required|in:customer,final',
            'catatan' => 'nullable|string|max:255',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $detailPesanan = DetailPesanan::where('id', $request->produk_id)
            ->where('pesanan_id', $id)
            ->first();
        
        if (!$detailPesanan) {
            return redirect()->back()
                ->with('error', 'Detail pesanan tidak ditemukan');
        }
        
        // Upload file
        if ($request->hasFile('desain') && $request->file('desain')->isValid()) {
            $file = $request->file('desain');
            $fileName = 'desain_' . $request->tipe . '_pesanan_' . $id . '_produk_' . $request->produk_id . '_' . time() . '.' . $file->getClientOriginalExtension();
            
            // Simpan file
            $path = $file->storeAs('public/desain', $fileName);
            
            // Update detail pesanan
            if ($request->tipe === 'customer') {
                $detailPesanan->upload_desain = $fileName;
            } else {
                $detailPesanan->desain_revisi = $fileName;
            }
            
            // Update catatan jika diberikan
            if ($request->has('catatan') && !empty($request->catatan)) {
                $detailPesanan->catatan = $request->catatan;
            }
            
            $detailPesanan->save();
            
            return redirect()->route('admin.pesanan.detail-produk', ['id' => $id, 'produk_id' => $request->produk_id])
                ->with('success', 'Desain berhasil diupload');
        }
        
        return redirect()->back()
            ->with('error', 'Gagal mengupload desain');
    }
}