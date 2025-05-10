<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Models\ProsesPesanan;
use App\Models\Operator;
use App\Models\Mesin;
use App\Models\BiayaDesain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PesananApiController extends Controller
{
    /**
     * Menampilkan daftar pesanan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            // Buat query awal dengan eager loading relasi
            $query = Pesanan::with([
                'user.alamats', 
                'admin', 
                'ekspedisi', 
                'detailPesanans.custom.item',
                'detailPesanans.custom.ukuran',
                'detailPesanans.custom.bahan',
                'detailPesanans.custom.jenis',
                'detailPesanans.prosesPesanan',
                'pembayaran'
            ]);
            
            // Filter berdasarkan status
            if ($request->has('status') && !empty($request->status)) {
                $query->where('status', $request->status);
            }
            
            // Filter berdasarkan range tanggal
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('tanggal_dipesan', [$request->start_date, $request->end_date]);
            } else if ($request->has('start_date')) {
                $query->where('tanggal_dipesan', '>=', $request->start_date);
            } else if ($request->has('end_date')) {
                $query->where('tanggal_dipesan', '<=', $request->end_date);
            }
            
            // Filter berdasarkan metode pengambilan
            if ($request->has('metode_pengambilan') && !empty($request->metode_pengambilan)) {
                $query->where('metode_pengambilan', $request->metode_pengambilan);
            }
            
            // Pencarian berdasarkan ID atau nama pelanggan
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
            $sortBy = $request->sort_by ?? 'created_at';
            $sortOrder = $request->sort_order ?? 'desc';
            $query->orderBy($sortBy, $sortOrder);
            
            // Handle pagination
            if ($request->has('paginate') && $request->paginate === 'true') {
                $perPage = $request->per_page ?? 15;
                $pesanan = $query->paginate($perPage);
                
                return response()->json([
                    'success' => true,
                    'data' => $pesanan,
                    'message' => 'Daftar pesanan berhasil dimuat'
                ]);
            } else {
                // Get semua data tanpa pagination
                $pesanan = $query->get();
                
                return response()->json([
                    'success' => true,
                    'data' => $pesanan,
                    'message' => 'Daftar pesanan berhasil dimuat'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error retrieving pesanan list: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat daftar pesanan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menyimpan pesanan baru.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            // Validasi input
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'metode_pengambilan' => 'required|in:ambil,antar',
                'ekspedisi_id' => 'nullable|required_if:metode_pengambilan,antar|exists:ekspedisis,id',
                'waktu_pengambilan' => 'nullable|date|required_if:metode_pengambilan,ambil',
                'estimasi_waktu' => 'nullable|integer',
                'detail_pesanan' => 'required|array',
                'detail_pesanan.*.custom_id' => 'required|exists:customs,id',
                'detail_pesanan.*.jumlah' => 'required|integer|min:1',
                'detail_pesanan.*.tipe_desain' => 'required|in:sendiri,dibuatkan',
                'detail_pesanan.*.biaya_jasa' => 'nullable|numeric'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            DB::beginTransaction();
            
            // Buat pesanan baru
            $pesanan = new Pesanan();
            $pesanan->user_id = $request->user_id;
            $pesanan->admin_id = Auth::id(); // Admin yang sedang login
            $pesanan->status = 'Pemesanan';
            $pesanan->metode_pengambilan = $request->metode_pengambilan;
            $pesanan->tanggal_dipesan = now();
            
            if ($request->metode_pengambilan === 'antar' && $request->has('ekspedisi_id')) {
                $pesanan->ekspedisi_id = $request->ekspedisi_id;
            }
            
            if ($request->metode_pengambilan === 'ambil' && $request->has('waktu_pengambilan')) {
                $pesanan->waktu_pengambilan = $request->waktu_pengambilan;
            }
            
            if ($request->has('estimasi_waktu')) {
                $pesanan->estimasi_waktu = $request->estimasi_waktu;
            }
            
            $pesanan->save();
            
            // Simpan detail pesanan
            $totalHarga = 0;
            
            foreach ($request->detail_pesanan as $detail) {
                $detailPesanan = new DetailPesanan();
                $detailPesanan->pesanan_id = $pesanan->id;
                $detailPesanan->custom_id = $detail['custom_id'];
                $detailPesanan->jumlah = $detail['jumlah'];
                $detailPesanan->tipe_desain = $detail['tipe_desain'];
                
                // Jika ada biaya jasa desain
                if (isset($detail['biaya_jasa']) && $detail['biaya_jasa'] > 0) {
                    $detailPesanan->biaya_jasa = $detail['biaya_jasa'];
                } elseif ($detail['tipe_desain'] === 'dibuatkan') {
                    // Jika tidak diisi tapi tipe desain dibuatkan, ambil default biaya desain
                    $biayaDesain = BiayaDesain::first();
                    if ($biayaDesain) {
                        $detailPesanan->biaya_jasa = $biayaDesain->biaya;
                    }
                }
                
                // Ambil data custom untuk hitung harga
                $custom = \App\Models\Custom::find($detail['custom_id']);
                if ($custom) {
                    $hargaItem = $custom->harga * $detail['jumlah'];
                    $detailPesanan->total_harga = $hargaItem;
                    
                    // Tambahkan biaya jasa jika ada
                    if ($detailPesanan->biaya_jasa > 0) {
                        $detailPesanan->total_harga += $detailPesanan->biaya_jasa;
                    }
                    
                    $totalHarga += $detailPesanan->total_harga;
                }
                
                $detailPesanan->save();
            }
            
            DB::commit();
            
            // Ambil pesanan yang telah disimpan dengan semua relasinya
            $savedPesanan = Pesanan::with([
                'user.alamats', 
                'admin', 
                'ekspedisi',
                'detailPesanans.custom.item',
                'detailPesanans.custom.ukuran',
                'detailPesanans.custom.bahan',
                'detailPesanans.custom.jenis',
            ])->find($pesanan->id);
            
            return response()->json([
                'success' => true,
                'data' => $savedPesanan,
                'total_harga' => $totalHarga,
                'message' => 'Pesanan berhasil dibuat'
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating pesanan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat pesanan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan detail pesanan.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $pesanan = Pesanan::with([
                'user.alamats', 
                'admin', 
                'ekspedisi', 
                'detailPesanans.custom.item',
                'detailPesanans.custom.ukuran',
                'detailPesanans.custom.bahan',
                'detailPesanans.custom.jenis',
                'detailPesanans.prosesPesanan',
                'pembayaran'
            ])->find($id);
            
            if (!$pesanan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan tidak ditemukan'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'data' => $pesanan,
                'message' => 'Detail pesanan berhasil dimuat'
            ]);
        } catch (\Exception $e) {
            Log::error('Error retrieving pesanan detail: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat detail pesanan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengupdate data pesanan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $pesanan = Pesanan::find($id);
            
            if (!$pesanan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan tidak ditemukan'
                ], 404);
            }
            
            // Validasi input
            $validator = Validator::make($request->all(), [
                'metode_pengambilan' => 'sometimes|in:ambil,antar',
                'ekspedisi_id' => 'nullable|required_if:metode_pengambilan,antar|exists:ekspedisis,id',
                'waktu_pengambilan' => 'nullable|date|required_if:metode_pengambilan,ambil',
                'estimasi_waktu' => 'nullable|integer',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            DB::beginTransaction();
            
            // Update data pesanan
            if ($request->has('metode_pengambilan')) {
                $pesanan->metode_pengambilan = $request->metode_pengambilan;
                
                // Jika metode berubah, update field terkait
                if ($request->metode_pengambilan === 'antar') {
                    $pesanan->ekspedisi_id = $request->ekspedisi_id;
                    $pesanan->waktu_pengambilan = null;
                } else if ($request->metode_pengambilan === 'ambil') {
                    $pesanan->ekspedisi_id = null;
                    $pesanan->waktu_pengambilan = $request->waktu_pengambilan;
                }
            } else {
                // Jika metode tidak berubah, update field individual
                if ($request->has('ekspedisi_id') && $pesanan->metode_pengambilan === 'antar') {
                    $pesanan->ekspedisi_id = $request->ekspedisi_id;
                }
                
                if ($request->has('waktu_pengambilan') && $pesanan->metode_pengambilan === 'ambil') {
                    $pesanan->waktu_pengambilan = $request->waktu_pengambilan;
                }
            }
            
            if ($request->has('estimasi_waktu')) {
                $pesanan->estimasi_waktu = $request->estimasi_waktu;
            }
            
            if ($request->has('catatan')) {
                $pesanan->catatan = $request->catatan;
            }
            
            $pesanan->save();
            
            DB::commit();
            
            // Ambil pesanan yang telah diupdate dengan semua relasinya
            $updatedPesanan = Pesanan::with([
                'user.alamats', 
                'admin', 
                'ekspedisi',
                'detailPesanans.custom.item',
                'detailPesanans.custom.ukuran',
                'detailPesanans.custom.bahan',
                'detailPesanans.custom.jenis',
            ])->find($pesanan->id);
            
            return response()->json([
                'success' => true,
                'data' => $updatedPesanan,
                'message' => 'Pesanan berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating pesanan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui pesanan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengubah status pesanan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $pesanan = Pesanan::find($id);
            
            if (!$pesanan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan tidak ditemukan'
                ], 404);
            }
            
            // Validasi input
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:Pemesanan,Dikonfirmasi,Sedang Diproses,Menunggu Pengambilan,Sedang Dikirim,Selesai,Dibatalkan',
                'catatan' => 'nullable|string'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            // Update status
            $oldStatus = $pesanan->status;
            $pesanan->status = $request->status;
            
            // Update catatan jika diberikan
            if ($request->has('catatan') && !empty($request->catatan)) {
                $pesanan->catatan = $request->catatan;
            }
            
            $pesanan->save();
            
            // TODO: Jika diperlukan, tambahkan log history status
            // LogPesanan::create([
            //     'pesanan_id' => $pesanan->id,
            //     'admin_id' => Auth::id(),
            //     'status_lama' => $oldStatus,
            //     'status_baru' => $request->status,
            //     'catatan' => $request->catatan
            // ]);
            
            return response()->json([
                'success' => true,
                'data' => $pesanan,
                'message' => 'Status pesanan berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating pesanan status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status pesanan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menugaskan operator dan mesin untuk memproses pesanan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignProcess(Request $request, $id)
    {
        try {
            // Validasi input
            $validator = Validator::make($request->all(), [
                'detail_pesanan_id' => 'required|exists:detail_pesanans,id',
                'operator_id' => 'required|exists:operators,id',
                'mesin_id' => 'required|exists:mesins,id',
                'catatan' => 'nullable|string'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            // Verifikasi detail pesanan
            $detailPesanan = DetailPesanan::where('id', $request->detail_pesanan_id)
                ->where('pesanan_id', $id)
                ->first();
            
            if (!$detailPesanan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Detail pesanan tidak ditemukan atau tidak terkait dengan pesanan ini'
                ], 404);
            }
            
            // Cek apakah detail pesanan sudah memiliki proses
            $existingProcess = ProsesPesanan::where('detail_pesanan_id', $detailPesanan->id)->first();
            if ($existingProcess) {
                return response()->json([
                    'success' => false,
                    'message' => 'Detail pesanan ini sudah memiliki proses',
                    'data' => $existingProcess
                ], 400);
            }
            
            // Buat proses baru
            $prosesPesanan = new ProsesPesanan();
            $prosesPesanan->detail_pesanan_id = $detailPesanan->id;
            $prosesPesanan->operator_id = $request->operator_id;
            $prosesPesanan->mesin_id = $request->mesin_id;
            $prosesPesanan->waktu_mulai = now();
            $prosesPesanan->status_proses = 'Ditugaskan';
            
            if ($request->has('catatan')) {
                $prosesPesanan->catatan = $request->catatan;
            }
            
            $prosesPesanan->save();
            
            // Update status pesanan menjadi "Sedang Diproses" jika belum
            $pesanan = $detailPesanan->pesanan;
            if ($pesanan->status !== 'Sedang Diproses') {
                $pesanan->status = 'Sedang Diproses';
                $pesanan->save();
            }
            
            // Load relasi untuk response
            $prosesPesanan->load(['operator', 'mesin', 'detailPesanan.custom']);
            
            return response()->json([
                'success' => true,
                'data' => $prosesPesanan,
                'message' => 'Proses pesanan berhasil ditugaskan'
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error assigning process: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menugaskan proses: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan detail produk dalam pesanan
     * 
     * @param int $id ID pesanan
     * @param int $detailId ID detail pesanan
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProductDetail($id, $detailId)
    {
        try {
            $pesanan = Pesanan::with(['user.alamats'])->find($id);
            
            if (!$pesanan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan tidak ditemukan'
                ], 404);
            }
            
            $detailPesanan = DetailPesanan::with([
                    'custom.item', 
                    'custom.ukuran', 
                    'custom.bahan', 
                    'custom.jenis'
                ])
                ->where('id', $detailId)
                ->where('pesanan_id', $id)
                ->first();
            
            if (!$detailPesanan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Detail pesanan tidak ditemukan'
                ], 404);
            }
            
            // Dapatkan alamat pelanggan jika ada
            $alamat = null;
            if ($pesanan->user && $pesanan->user->alamats->isNotEmpty()) {
                $alamatObj = $pesanan->user->alamats->first();
                $alamat = [
                    'alamat_lengkap' => $alamatObj->alamat_lengkap,
                    'kelurahan' => $alamatObj->kelurahan,
                    'kecamatan' => $alamatObj->kecamatan,
                    'kota' => $alamatObj->kota,
                    'provinsi' => $alamatObj->provinsi,
                    'kode_pos' => $alamatObj->kode_pos
                ];
            }
            
            // Dapatkan detail produk
            $custom = $detailPesanan->custom;
            $item = $custom ? $custom->item : null;
            $ukuran = $custom ? $custom->ukuran : null;
            $bahan = $custom ? $custom->bahan : null;
            $jenis = $custom ? $custom->jenis : null;
            
            // Biaya desain
            $biayaDesain = 0;
            if ($detailPesanan->tipe_desain === 'dibuatkan') {
                $biayaDesain = $detailPesanan->biaya_jasa ?? 0;
            }
            
            $productDetail = [
                'id' => $detailPesanan->id,
                'nama_item' => $item ? $item->nama_item : null,
                'deskripsi' => $item ? $item->deskripsi : null,
                'bahan' => $bahan ? $bahan->nama_bahan : null,
                'ukuran' => $ukuran ? $ukuran->size : null,
                'jenis' => $jenis ? $jenis->kategori : null,
                'jumlah' => $detailPesanan->jumlah,
                'harga_satuan' => $custom ? $custom->harga : 0,
                'total_harga' => $detailPesanan->total_harga,
                'tipe_desain' => $detailPesanan->tipe_desain,
                'biaya_desain' => $biayaDesain,
                'gambar' => $item && $item->gambar ? asset('storage/' . $item->gambar) : null,
                'upload_desain' => $detailPesanan->upload_desain ? asset('storage/desain/' . $detailPesanan->upload_desain) : null,
                'desain_revisi' => $detailPesanan->desain_revisi ? asset('storage/desain/' . $detailPesanan->desain_revisi) : null,
                'catatan' => $detailPesanan->catatan
            ];
            
            return response()->json([
                'success' => true,
                'data' => [
                    'product' => $productDetail,
                    'pesanan' => [
                        'id' => $pesanan->id,
                        'status' => $pesanan->status,
                        'metode_pengambilan' => $pesanan->metode_pengambilan,
                        'tanggal_dipesan' => $pesanan->tanggal_dipesan ? $pesanan->tanggal_dipesan->format('Y-m-d H:i:s') : null
                    ],
                    'alamat' => $alamat,
                    'customer' => [
                        'id' => $pesanan->user ? $pesanan->user->id : null,
                        'nama' => $pesanan->user ? $pesanan->user->nama : null,
                        'email' => $pesanan->user ? $pesanan->user->email : null
                    ]
                ],
                'message' => 'Detail produk berhasil dimuat'
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting product detail: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memuat detail produk: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengunggah desain untuk detail pesanan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadDesain(Request $request, $id)
    {
        try {
            // Validasi input
            $validator = Validator::make($request->all(), [
                'detail_pesanan_id' => 'required|exists:detail_pesanans,id',
                'tipe' => 'required|in:upload_desain,desain_revisi',
                'file_name' => 'required|string',
                'catatan' => 'nullable|string'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            // Verifikasi detail pesanan
            $detailPesanan = DetailPesanan::where('id', $request->detail_pesanan_id)
                ->where('pesanan_id', $id)
                ->first();
            
            if (!$detailPesanan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Detail pesanan tidak ditemukan atau tidak terkait dengan pesanan ini'
                ], 404);
            }
            
            // Update field sesuai tipe
            if ($request->tipe === 'upload_desain') {
                // Jika ada file lama, hapus dari database saja (file fisik tetap ada)
                $detailPesanan->upload_desain = $request->file_name;
            } else {
                // Jika ada file lama, hapus dari database saja (file fisik tetap ada)
                $detailPesanan->desain_revisi = $request->file_name;
            }
            
            // Update catatan jika ada
            if ($request->has('catatan') && !empty($request->catatan)) {
                $detailPesanan->catatan = $request->catatan;
            }
            
            $detailPesanan->save();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'detail_pesanan' => $detailPesanan,
                    'file_url' => $request->tipe === 'upload_desain' 
                        ? asset('storage/desain/' . $detailPesanan->upload_desain)
                        : asset('storage/desain/' . $detailPesanan->desain_revisi)
                ],
                'message' => 'Desain berhasil diunggah'
            ]);
        } catch (\Exception $e) {
            Log::error('Error uploading design: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunggah desain: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan daftar operator yang tersedia.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOperators()
    {
        try {
            $operators = Operator::where('status', 'aktif')->get();
            
            return response()->json([
                'success' => true,
                'data' => $operators,
                'message' => 'Daftar operator berhasil dimuat'
            ]);
        } catch (\Exception $e) {
            Log::error('Error retrieving operators: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat daftar operator: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan daftar mesin yang tersedia.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMesins()
    {
        try {
            $mesins = Mesin::where('status', 'aktif')->get();
            
            return response()->json([
                'success' => true,
                'data' => $mesins,
                'message' => 'Daftar mesin berhasil dimuat'
            ]);
        } catch (\Exception $e) {
            Log::error('Error retrieving machines: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat daftar mesin: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan statistik pesanan (jumlah per status, pendapatan, dll).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStatistics()
    {
        try {
            // Jumlah pesanan per status
            $pesananPerStatus = Pesanan::select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->get()
                ->pluck('total', 'status')
                ->toArray();
            
            // Total pendapatan (dari semua detail pesanan)
            $totalPendapatan = DetailPesanan::sum('total_harga');
            
            // Pesanan hari ini
            $pesananHariIni = Pesanan::whereDate('created_at', today())->count();
            
            // Pesanan belum diproses
            $pesananBelumDiproses = Pesanan::where('status', 'Pemesanan')->count();
            
            // Pesanan berdasarkan tipe desain
            $pesananPerTipeDesain = DetailPesanan::select('tipe_desain', DB::raw('count(*) as total'))
                ->groupBy('tipe_desain')
                ->get()
                ->pluck('total', 'tipe_desain')
                ->toArray();
            
            // Total biaya desain
            $totalBiayaDesain = DetailPesanan::where('tipe_desain', 'dibuatkan')
                ->sum('biaya_jasa');
            
            // 5 produk terlaris
            $produkTerlaris = DB::table('detail_pesanans')
                ->join('customs', 'detail_pesanans.custom_id', '=', 'customs.id')
                ->join('items', 'customs.item_id', '=', 'items.id')
                ->select('items.id', 'items.nama_item', DB::raw('SUM(detail_pesanans.jumlah) as total_terjual'))
                ->groupBy('items.id', 'items.nama_item')
                ->orderBy('total_terjual', 'desc')
                ->limit(5)
                ->get();
            
            // Data grafik pendapatan 7 hari terakhir
            $pendapatanMingguIni = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                $pendapatan = DB::table('detail_pesanans')
                    ->join('pesanans', 'detail_pesanans.pesanan_id', '=', 'pesanans.id')
                    ->whereDate('pesanans.tanggal_dipesan', $date)
                    ->sum('detail_pesanans.total_harga');
                
                $pendapatanMingguIni[] = [
                    'tanggal' => $date,
                    'pendapatan' => $pendapatan
                ];
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'pesanan_per_status' => $pesananPerStatus,
                    'total_pendapatan' => $totalPendapatan,
                    'pesanan_hari_ini' => $pesananHariIni,
                    'pesanan_belum_diproses' => $pesananBelumDiproses,
                    'pesanan_per_tipe_desain' => $pesananPerTipeDesain,
                    'total_biaya_desain' => $totalBiayaDesain,
                    'produk_terlaris' => $produkTerlaris,
                    'pendapatan_minggu_ini' => $pendapatanMingguIni
                ],
                'message' => 'Statistik pesanan berhasil dimuat'
            ]);
        } catch (\Exception $e) {
            Log::error('Error retrieving order statistics: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat statistik pesanan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Konfirmasi pengambilan pesanan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function confirmPickup(Request $request, $id)
    {
        try {
            $pesanan = Pesanan::find($id);
            
            if (!$pesanan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan tidak ditemukan'
                ], 404);
            }
            
            // Validasi status
            if ($pesanan->status !== 'Menunggu Pengambilan') {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan tidak dalam status Menunggu Pengambilan'
                ], 400);
            }
            
            // Validasi metode pengambilan
            if ($pesanan->metode_pengambilan !== 'ambil') {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan ini bukan pesanan dengan pengambilan di tempat'
                ], 400);
            }
            
            // Update status pesanan
            $pesanan->status = 'Selesai';
            
            // Tambahkan catatan jika ada
            if ($request->has('catatan') && !empty($request->catatan)) {
                $pesanan->catatan = $pesanan->catatan 
                    ? $pesanan->catatan . "\n\nKonfirmasi pengambilan: " . $request->catatan
                    : "Konfirmasi pengambilan: " . $request->catatan;
            }
            
            $pesanan->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Pengambilan pesanan berhasil dikonfirmasi',
                'data' => $pesanan
            ]);
        } catch (\Exception $e) {
            Log::error('Error confirming pickup: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengkonfirmasi pengambilan pesanan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Konfirmasi pengiriman pesanan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function confirmShipment(Request $request, $id)
    {
        try {
            // Validasi input
            $validator = Validator::make($request->all(), [
                'no_resi' => 'nullable|string|max:50',
                'catatan' => 'nullable|string'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $pesanan = Pesanan::find($id);
            
            if (!$pesanan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan tidak ditemukan'
                ], 404);
            }
            
            // Validasi status
            if ($pesanan->status !== 'Sedang Diproses') {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan tidak dalam status Sedang Diproses'
                ], 400);
            }
            
            // Validasi metode pengambilan
            if ($pesanan->metode_pengambilan !== 'antar') {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan ini bukan pesanan dengan pengiriman'
                ], 400);
            }
            
            // Update status pesanan
            $pesanan->status = 'Sedang Dikirim';
            
            // Simpan nomor resi jika ada
            if ($request->has('no_resi') && !empty($request->no_resi)) {
                // Karena no_resi tidak ada di tabel pesanans, kita bisa menyimpannya di catatan
                $pesanan->catatan = $pesanan->catatan 
                    ? $pesanan->catatan . "\n\nNo. Resi: " . $request->no_resi
                    : "No. Resi: " . $request->no_resi;
            }
            
            // Tambahkan catatan jika ada
            if ($request->has('catatan') && !empty($request->catatan)) {
                $pesanan->catatan = $pesanan->catatan 
                    ? $pesanan->catatan . "\n\nCatatan pengiriman: " . $request->catatan
                    : "Catatan pengiriman: " . $request->catatan;
            }
            
            $pesanan->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Pengiriman pesanan berhasil dikonfirmasi',
                'data' => $pesanan
            ]);
        } catch (\Exception $e) {
            Log::error('Error confirming shipment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengkonfirmasi pengiriman pesanan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Konfirmasi barang sudah diterima (selesai).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function confirmReceived(Request $request, $id)
    {
        try {
            $pesanan = Pesanan::find($id);
            
            if (!$pesanan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan tidak ditemukan'
                ], 404);
            }
            
            // Validasi status
            if ($pesanan->status !== 'Sedang Dikirim') {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan tidak dalam status Sedang Dikirim'
                ], 400);
            }
            
            // Update status pesanan
            $pesanan->status = 'Selesai';
            
            // Tambahkan catatan jika ada
            if ($request->has('catatan') && !empty($request->catatan)) {
                $pesanan->catatan = $pesanan->catatan 
                    ? $pesanan->catatan . "\n\nKonfirmasi penerimaan: " . $request->catatan
                    : "Konfirmasi penerimaan: " . $request->catatan;
            }
            
            $pesanan->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Penerimaan pesanan berhasil dikonfirmasi',
                'data' => $pesanan
            ]);
        } catch (\Exception $e) {
            Log::error('Error confirming receipt: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengkonfirmasi penerimaan pesanan: ' . $e->getMessage()
            ], 500);
        }
    }
}