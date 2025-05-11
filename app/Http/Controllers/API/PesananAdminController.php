<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Models\ProsesPesanan;
use App\Models\Mesin;
use App\Models\Operator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class PesananAdminController extends Controller
{
    /**
     * Mendapatkan daftar semua pesanan dengan filter
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $query = Pesanan::with(['user', 'detailPesanans.custom.item', 'admin', 'ekspedisi']);
            
            // Filter berdasarkan status jika ada
            if ($request->has('status') && $request->status != 'Semua Status') {
                $query->where('status', $request->status);
            }
            
            // Filter berdasarkan periode tanggal jika ada
            if ($request->has('dari_tanggal') && $request->dari_tanggal) {
                $query->whereDate('tanggal_dipesan', '>=', $request->dari_tanggal);
            }
            
            if ($request->has('sampai_tanggal') && $request->sampai_tanggal) {
                $query->whereDate('tanggal_dipesan', '<=', $request->sampai_tanggal);
            }
            
            // Filter berdasarkan ID atau nama pelanggan
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('id', 'like', "%{$search}%")
                      ->orWhereHas('user', function($q2) use ($search) {
                          $q2->where('nama', 'like', "%{$search}%");
                      });
                });
            }
            
            // Sorting
            $sortField = $request->get('sort_field', 'tanggal_dipesan');
            $sortDirection = $request->get('sort_direction', 'desc');
            $query->orderBy($sortField, $sortDirection);
            
            // Pagination
            $perPage = $request->get('per_page', 10);
            $pesanans = $query->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'pesanans' => $pesanans,
                'status_options' => [
                    'Pemesanan', 'Dikonfirmasi', 'Sedang Diproses', 
                    'Menunggu Pengambilan', 'Sedang Dikirim', 'Selesai', 'Dibatalkan'
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('API Error: Gagal mendapatkan daftar pesanan - ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil daftar pesanan',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Mendapatkan detail pesanan berdasarkan ID
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
                'detailPesanans.custom.item',
                'detailPesanans.custom.bahan',
                'detailPesanans.custom.ukuran',
                'detailPesanans.custom.jenis',
                'detailPesanans.prosesPesanan.operator',
                'detailPesanans.prosesPesanan.mesin'
            ])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'pesanan' => $pesanan,
                'status_options' => [
                    'Pemesanan', 'Dikonfirmasi', 'Sedang Diproses', 
                    'Menunggu Pengambilan', 'Sedang Dikirim', 'Selesai', 'Dibatalkan'
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('API Error: Gagal mendapatkan detail pesanan - ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil detail pesanan',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Mengupdate status pesanan
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required|string|in:Pemesanan,Dikonfirmasi,Sedang Diproses,Menunggu Pengambilan,Sedang Dikirim,Selesai,Dibatalkan',
                'catatan' => 'nullable|string'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            DB::beginTransaction();
            
            $pesanan = Pesanan::findOrFail($id);
            $oldStatus = $pesanan->status;
            $newStatus = $request->status;
            
            // Update status pesanan
            $pesanan->status = $newStatus;
            
            // Jika pesanan baru dikonfirmasi, set admin_id
            if ($oldStatus == 'Pemesanan' && $newStatus == 'Dikonfirmasi') {
                $user = Auth::user();
                    $pesanan->admin_id = $user ? $user->id : null; // Dengan pengecekan null
            }
            
            // Jika pesanan selesai, catat waktu selesai
            if ($newStatus == 'Selesai') {
                $pesanan->waktu_pengambilan = now();
            }
            
            $pesanan->save();
            
            // Catat aktivitas perubahan status
            // (bisa implementasikan sistem logging aktivitas jika diperlukan)
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Status pesanan berhasil diperbarui',
                'pesanan' => $pesanan
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('API Error: Gagal update status pesanan - ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui status pesanan',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Menugaskan proses produksi ke mesin dan operator
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignProduction(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'detail_pesanan_id' => 'required|exists:detail_pesanans,id',
                'mesin_id' => 'required|exists:mesins,id',
                'operator_id' => 'required|exists:operators,id',
                'catatan' => 'nullable|string'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            DB::beginTransaction();
            
            // Periksa apakah pesanan sudah ada
            $pesanan = Pesanan::findOrFail($id);
            
            // Periksa status pesanan
            if ($pesanan->status != 'Dikonfirmasi') {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan harus dalam status Dikonfirmasi untuk memulai produksi'
                ], 400);
            }
            
            // Periksa ketersediaan mesin
            $mesin = Mesin::findOrFail($request->mesin_id);
            if ($mesin->status != 'aktif') {
                return response()->json([
                    'success' => false,
                    'message' => 'Mesin tidak tersedia saat ini'
                ], 400);
            }
            
            // Buat record proses pesanan
            $prosesPesanan = new ProsesPesanan();
            $prosesPesanan->detail_pesanan_id = $request->detail_pesanan_id;
            $prosesPesanan->mesin_id = $request->mesin_id;
            $prosesPesanan->operator_id = $request->operator_id;
            $prosesPesanan->waktu_mulai = now();
            $prosesPesanan->status_proses = 'Mulai';
            $prosesPesanan->catatan = $request->catatan;
            $prosesPesanan->save();
            
            // Update status mesin menjadi digunakan
            $mesin->status = 'digunakan';
            $mesin->save();
            
            // Update status pesanan
            $pesanan->status = 'Sedang Diproses';
            $pesanan->save();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Proses produksi berhasil ditugaskan',
                'proses_pesanan' => $prosesPesanan
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('API Error: Gagal assign produksi - ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menugaskan proses produksi',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Menyelesaikan proses produksi
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function completeProduction(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'proses_pesanan_id' => 'required|exists:proses_pesanans,id',
                'catatan' => 'nullable|string'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            DB::beginTransaction();
            
            // Ambil data proses pesanan
            $prosesPesanan = ProsesPesanan::with('detailPesanan.pesanan', 'mesin')->findOrFail($request->proses_pesanan_id);
            
            // Update status proses pesanan
            $prosesPesanan->status_proses = 'Selesai';
            $prosesPesanan->waktu_selesai = now();
            $prosesPesanan->catatan = $request->catatan ?? $prosesPesanan->catatan;
            $prosesPesanan->save();
            
            // Update status mesin menjadi tersedia kembali
            $mesin = $prosesPesanan->mesin;
            $mesin->status = 'aktif';
            $mesin->save();
            
            // Update status pesanan
            $pesanan = $prosesPesanan->detailPesanan->pesanan;
            
            // Cek metode pengambilan untuk menentukan status berikutnya
            if ($pesanan->metode_pengambilan == 'ambil') {
                $pesanan->status = 'Menunggu Pengambilan';
            } else {
                $pesanan->status = 'Sedang Dikirim';
            }
            $pesanan->save();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Proses produksi berhasil diselesaikan',
                'proses_pesanan' => $prosesPesanan,
                'pesanan' => $pesanan
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('API Error: Gagal menyelesaikan produksi - ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyelesaikan proses produksi',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Konfirmasi pengiriman pesanan
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function confirmShipment(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'ekspedisi_id' => 'required|exists:ekspedisis,id',
                'nomor_resi' => 'nullable|string|max:100',
                'catatan' => 'nullable|string'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            DB::beginTransaction();
            
            $pesanan = Pesanan::findOrFail($id);
            
            if ($pesanan->metode_pengambilan != 'antar') {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan ini bukan untuk pengiriman'
                ], 400);
            }
            
            // Update ekspedisi dan status
            $pesanan->ekspedisi_id = $request->ekspedisi_id;
            $pesanan->status = 'Sedang Dikirim';
            $pesanan->save();
            
            // Simpan nomor resi jika ada
            if ($request->has('nomor_resi')) {
                // Ini bisa disimpan di tabel tambahan jika diperlukan
                // atau bisa ditambahkan kolom nomor_resi di tabel pesanans
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Pengiriman pesanan berhasil dikonfirmasi',
                'pesanan' => $pesanan
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('API Error: Gagal konfirmasi pengiriman - ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengkonfirmasi pengiriman',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Konfirmasi pengambilan pesanan
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function confirmPickup(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            
            $pesanan = Pesanan::findOrFail($id);
            
            if ($pesanan->metode_pengambilan != 'ambil') {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan ini bukan untuk diambil'
                ], 400);
            }
            
            if ($pesanan->status != 'Menunggu Pengambilan') {
                return response()->json([
                    'success' => false,
                    'message' => 'Status pesanan harus Menunggu Pengambilan'
                ], 400);
            }
            
            // Update status dan waktu pengambilan
            $pesanan->status = 'Selesai';
            $pesanan->waktu_pengambilan = now();
            $pesanan->save();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Pengambilan pesanan berhasil dikonfirmasi',
                'pesanan' => $pesanan
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('API Error: Gagal konfirmasi pengambilan - ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengkonfirmasi pengambilan',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Mengupload desain dari toko
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadDesain(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'detail_pesanan_id' => 'required|exists:detail_pesanans,id',
                'desain' => 'required|file|mimes:jpeg,png,jpg,pdf,ai,psd|max:10240', // 10MB max
                'tipe' => 'required|in:desain_toko,revisi'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            DB::beginTransaction();
            
            // Periksa detail pesanan
            $detailPesanan = DetailPesanan::findOrFail($request->detail_pesanan_id);
            
            // Upload file
            $file = $request->file('desain');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('desain', $fileName, 'public');
            
            // Simpan path file tanpa update timestamps
            if ($request->tipe == 'desain_toko') {
                // Manual update tanpa timestamps
                DB::table('detail_pesanans')
                    ->where('id', $detailPesanan->id)
                    ->update(['upload_desain' => $path]);
            } else {
                // Revisi desain - juga manual update
                DB::table('detail_pesanans')
                    ->where('id', $detailPesanan->id)
                    ->update(['desain_revisi' => $path]);
            }
            
            // Refresh model data
            $detailPesanan = DetailPesanan::findOrFail($request->detail_pesanan_id);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Desain berhasil diupload',
                'detail_pesanan' => $detailPesanan,
                'path' => $path
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('API Error: Gagal upload desain - ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengupload desain',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Membatalkan pesanan
     * 
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelOrder(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'alasan_batal' => 'nullable|string'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            DB::beginTransaction();
            
            $pesanan = Pesanan::findOrFail($id);
            
            // Cek apakah pesanan masih bisa dibatalkan
            if (in_array($pesanan->status, ['Selesai', 'Dibatalkan'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan tidak dapat dibatalkan'
                ], 400);
            }
            
            // Jika pesanan sedang diproses, periksa proses pesanan
            if ($pesanan->status == 'Sedang Diproses') {
                $prosesPesanans = ProsesPesanan::whereHas('detailPesanan', function($query) use($id) {
                    $query->where('pesanan_id', $id);
                })->get();
                
                // Update status mesin menjadi tersedia kembali
                foreach ($prosesPesanans as $proses) {
                    $mesin = $proses->mesin;
                    if ($mesin) {
                        $mesin->status = 'aktif';
                        $mesin->save();
                    }
                    
                    // Update status proses
                    $proses->status_proses = 'Dibatalkan';
                    $proses->waktu_selesai = now();
                    $proses->save();
                }
            }
            
            // Update status pesanan
            $pesanan->status = 'Dibatalkan';
            // Simpan alasan pembatalan jika ada kolom untuk itu
            // $pesanan->alasan_batal = $request->alasan_batal;
            $pesanan->save();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibatalkan',
                'pesanan' => $pesanan
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('API Error: Gagal batalkan pesanan - ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat membatalkan pesanan',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Mendapatkan daftar mesin yang tersedia
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAvailableMachines()
    {
        try {
            $machines = Mesin::where('status', 'aktif')->get();
            
            return response()->json([
                'success' => true,
                'machines' => $machines
            ]);
        } catch (\Exception $e) {
            Log::error('API Error: Gagal mendapatkan daftar mesin - ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil daftar mesin',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Mendapatkan daftar operator
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOperators()
    {
        try {
            $operators = Operator::where('status', 'aktif')->get();
            
            return response()->json([
                'success' => true,
                'operators' => $operators
            ]);
        } catch (\Exception $e) {
            Log::error('API Error: Gagal mendapatkan daftar operator - ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil daftar operator',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Mendapatkan statistik pesanan
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStatistics()
    {
        try {
            // Hitung jumlah pesanan per status
            $statusCounts = Pesanan::select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->get()
                ->pluck('total', 'status')
                ->toArray();
            
            // Hitung jumlah pesanan hari ini
            $today = Pesanan::whereDate('created_at', today())->count();
            
            // Hitung jumlah pesanan yang perlu diproses
            $needsProcessing = Pesanan::where('status', 'Dikonfirmasi')->count();
            
            // Hitung jumlah pesanan yang siap diambil
            $readyForPickup = Pesanan::where('status', 'Menunggu Pengambilan')->count();
            
            return response()->json([
                'success' => true,
                'statistics' => [
                    'status_counts' => $statusCounts,
                    'today_orders' => $today,
                    'needs_processing' => $needsProcessing,
                    'ready_for_pickup' => $readyForPickup
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('API Error: Gagal mendapatkan statistik - ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil statistik',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}