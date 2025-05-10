<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Models\ProsesPesanan;
use App\Models\Operator;
use App\Models\Mesin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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
                'user', 
                'admin', 
                'ekspedisi', 
                'detailPesanans.custom',
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
            
            // Jika masih ingin mendukung pagination opsional:
            if ($request->has('paginate') && $request->paginate === 'true') {
                $perPage = $request->per_page ?? 15;
                $pesanan = $query->paginate($perPage);
            } else {
                // Get semua data tanpa pagination
                $pesanan = $query->get();
            }
            
            return response()->json([
                'success' => true,
                'data' => $pesanan,
                'message' => 'Daftar pesanan berhasil dimuat'
            ]);
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
                }
                
                // Ambil data custom untuk hitung harga
                $custom = \App\Models\Custom::find($detail['custom_id']);
                if ($custom) {
                    $hargaItem = $custom->harga * $detail['jumlah'];
                    $detailPesanan->total_harga = $hargaItem;
                    $totalHarga += $hargaItem;
                    
                    // Tambahkan biaya jasa jika ada
                    if (isset($detail['biaya_jasa']) && $detail['biaya_jasa'] > 0) {
                        $totalHarga += $detail['biaya_jasa'];
                        $detailPesanan->total_harga += $detail['biaya_jasa'];
                    }
                }
                
                $detailPesanan->save();
            }
            
            DB::commit();
            
            // Ambil pesanan yang telah disimpan dengan semua relasinya
            $savedPesanan = Pesanan::with([
                'user', 
                'admin', 
                'ekspedisi',
                'detailPesanans.custom',
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
                'user', 
                'admin', 
                'ekspedisi', 
                'detailPesanans.custom',
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
            
            $pesanan->save();
            
            DB::commit();
            
            // Ambil pesanan yang telah diupdate dengan semua relasinya
            $updatedPesanan = Pesanan::with([
                'user', 
                'admin', 
                'ekspedisi',
                'detailPesanans.custom',
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
                'status' => 'required|in:Pemesanan,Sedang Diproses,Menunggu Pengambilan,Sedang Dikirim,Selesai,Dibatalkan',
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
            $pesanan->save();
            
            // Jika perlu, bisa menambahkan log perubahan status di sini
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
                'file' => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120'
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
            
            // Upload file
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $extension = $file->getClientOriginalExtension();
                $filename = 'pesanan_' . $id . '_detail_' . $detailPesanan->id . '_' . $request->tipe . '_' . time() . '.' . $extension;
                
                // Simpan file ke storage
                $path = $file->storeAs('public/desain', $filename);
                
                // Update field sesuai tipe
                if ($request->tipe === 'upload_desain') {
                    // Jika ada file lama, hapus
                    if ($detailPesanan->upload_desain) {
                        Storage::delete('public/desain/' . $detailPesanan->upload_desain);
                    }
                    $detailPesanan->upload_desain = $filename;
                } else {
                    // Jika ada file lama, hapus
                    if ($detailPesanan->desain_revisi) {
                        Storage::delete('public/desain/' . $detailPesanan->desain_revisi);
                    }
                    $detailPesanan->desain_revisi = $filename;
                }
                
                $detailPesanan->save();
                
                return response()->json([
                    'success' => true,
                    'data' => [
                        'filename' => $filename,
                        'url' => asset('storage/desain/' . $filename)
                    ],
                    'message' => 'Desain berhasil diunggah'
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada file yang diunggah'
            ], 400);
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
            
            return response()->json([
                'success' => true,
                'data' => [
                    'pesanan_per_status' => $pesananPerStatus,
                    'total_pendapatan' => $totalPendapatan,
                    'pesanan_hari_ini' => $pesananHariIni,
                    'pesanan_belum_diproses' => $pesananBelumDiproses
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
}