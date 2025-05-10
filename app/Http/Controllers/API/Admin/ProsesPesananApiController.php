<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProsesPesanan;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ProsesPesananApiController extends Controller
{
    /**
     * Menampilkan daftar proses pesanan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $query = ProsesPesanan::with(['detailPesanan.pesanan', 'detailPesanan.custom', 'operator', 'mesin']);
            
            // Filter berdasarkan status
            if ($request->has('status') && !empty($request->status)) {
                $query->where('status_proses', $request->status);
            }
            
            // Filter berdasarkan operator
            if ($request->has('operator_id') && !empty($request->operator_id)) {
                $query->where('operator_id', $request->operator_id);
            }
            
            // Filter berdasarkan mesin
            if ($request->has('mesin_id') && !empty($request->mesin_id)) {
                $query->where('mesin_id', $request->mesin_id);
            }
            
            // Filter berdasarkan pesanan
            if ($request->has('pesanan_id') && !empty($request->pesanan_id)) {
                $query->whereHas('detailPesanan', function($q) use ($request) {
                    $q->where('pesanan_id', $request->pesanan_id);
                });
            }
            
            // Filter berdasarkan rentang waktu mulai
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('waktu_mulai', [$request->start_date, $request->end_date]);
            } else if ($request->has('start_date')) {
                $query->where('waktu_mulai', '>=', $request->start_date);
            } else if ($request->has('end_date')) {
                $query->where('waktu_mulai', '<=', $request->end_date);
            }
            
            // Pengurutan
            $sortBy = $request->sort_by ?? 'waktu_mulai';
            $sortOrder = $request->sort_order ?? 'desc';
            $query->orderBy($sortBy, $sortOrder);
            
            // Get semua data tanpa pagination
            $prosesPesanan = $query->get();
            
            return response()->json([
                'success' => true,
                'data' => $prosesPesanan,
                'message' => 'Daftar proses pesanan berhasil dimuat'
            ]);
        } catch (\Exception $e) {
            Log::error('Error retrieving process list: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat daftar proses pesanan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Membuat proses pesanan baru.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            // Validasi input
            $validator = Validator::make($request->all(), [
                'detail_pesanan_id' => 'required|exists:detail_pesanans,id',
                'operator_id' => 'required|exists:operators,id',
                'mesin_id' => 'required|exists:mesins,id',
                'status_proses' => 'required|in:Ditugaskan,Dalam Antrian,Sedang Dikerjakan,Selesai Produksi',
                'catatan' => 'nullable|string'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            // Cek apakah detail pesanan sudah memiliki proses
            $existingProcess = ProsesPesanan::where('detail_pesanan_id', $request->detail_pesanan_id)->first();
            if ($existingProcess) {
                return response()->json([
                    'success' => false,
                    'message' => 'Detail pesanan ini sudah memiliki proses',
                    'data' => $existingProcess
                ], 400);
            }
            
            // Buat proses baru
            $prosesPesanan = new ProsesPesanan();
            $prosesPesanan->detail_pesanan_id = $request->detail_pesanan_id;
            $prosesPesanan->operator_id = $request->operator_id;
            $prosesPesanan->mesin_id = $request->mesin_id;
            $prosesPesanan->waktu_mulai = now();
            $prosesPesanan->status_proses = $request->status_proses;
            
            if ($request->has('catatan')) {
                $prosesPesanan->catatan = $request->catatan;
            }
            
            // Jika proses langsung selesai
            if ($request->status_proses === 'Selesai Produksi') {
                $prosesPesanan->waktu_selesai = now();
            }
            
            $prosesPesanan->save();
            
            // Update status pesanan jika diperlukan
            $detailPesanan = DetailPesanan::with('pesanan')->find($request->detail_pesanan_id);
            if ($detailPesanan && $detailPesanan->pesanan->status !== 'Sedang Diproses') {
                $detailPesanan->pesanan->status = 'Sedang Diproses';
                $detailPesanan->pesanan->save();
            }
            
            // Load relasi untuk response
            $prosesPesanan->load(['operator', 'mesin', 'detailPesanan.custom', 'detailPesanan.pesanan']);
            
            return response()->json([
                'success' => true,
                'data' => $prosesPesanan,
                'message' => 'Proses pesanan berhasil dibuat'
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating process: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat proses pesanan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan detail proses pesanan.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $prosesPesanan = ProsesPesanan::with([
                'detailPesanan.pesanan.user',
                'detailPesanan.custom.item',
                'detailPesanan.custom.ukuran',
                'detailPesanan.custom.bahan',
                'detailPesanan.custom.jenis',
                'operator',
                'mesin'
            ])->find($id);
            
            if (!$prosesPesanan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Proses pesanan tidak ditemukan'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'data' => $prosesPesanan,
                'message' => 'Detail proses pesanan berhasil dimuat'
            ]);
        } catch (\Exception $e) {
            Log::error('Error retrieving process detail: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat detail proses pesanan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengupdate data proses pesanan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $prosesPesanan = ProsesPesanan::find($id);
            
            if (!$prosesPesanan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Proses pesanan tidak ditemukan'
                ], 404);
            }
            
            // Validasi input
            $validator = Validator::make($request->all(), [
                'operator_id' => 'sometimes|exists:operators,id',
                'mesin_id' => 'sometimes|exists:mesins,id',
                'status_proses' => 'sometimes|in:Ditugaskan,Dalam Antrian,Sedang Dikerjakan,Selesai Produksi,Gagal,Dibatalkan',
                'catatan' => 'nullable|string'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            // Update data proses
            if ($request->has('operator_id')) {
                $prosesPesanan->operator_id = $request->operator_id;
            }
            
            if ($request->has('mesin_id')) {
                $prosesPesanan->mesin_id = $request->mesin_id;
            }
            
            if ($request->has('status_proses')) {
                $oldStatus = $prosesPesanan->status_proses;
                $prosesPesanan->status_proses = $request->status_proses;
                
                // Jika status berubah menjadi selesai, set waktu selesai
                if ($request->status_proses === 'Selesai Produksi' && $oldStatus !== 'Selesai Produksi') {
                    $prosesPesanan->waktu_selesai = now();
                    
                    // Check if all processes for this order are completed
                    $this->checkAndUpdateOrderStatus($prosesPesanan->detailPesanan->pesanan_id);
                }
                
                // Jika status berubah dari selesai, kosongkan waktu selesai
                if ($oldStatus === 'Selesai Produksi' && $request->status_proses !== 'Selesai Produksi') {
                    $prosesPesanan->waktu_selesai = null;
                }
            }
            
            if ($request->has('catatan')) {
                $prosesPesanan->catatan = $request->catatan;
            }
            
            $prosesPesanan->save();
            
            // Load relasi untuk response
            $prosesPesanan->load(['operator', 'mesin', 'detailPesanan.custom', 'detailPesanan.pesanan']);
            
            return response()->json([
                'success' => true,
                'data' => $prosesPesanan,
                'message' => 'Proses pesanan berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating process: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui proses pesanan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengubah status proses menjadi selesai.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function complete(Request $request, $id)
    {
        try {
            $prosesPesanan = ProsesPesanan::find($id);
            
            if (!$prosesPesanan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Proses pesanan tidak ditemukan'
                ], 404);
            }
            
            // Validasi input
            $validator = Validator::make($request->all(), [
                'catatan' => 'nullable|string'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            // Set status selesai dan waktu selesai
            $prosesPesanan->status_proses = 'Selesai Produksi';
            $prosesPesanan->waktu_selesai = now();
            
            if ($request->has('catatan')) {
                $prosesPesanan->catatan = $request->catatan;
            }
            
            $prosesPesanan->save();
            
            // Check if all processes for this order are completed
            $this->checkAndUpdateOrderStatus($prosesPesanan->detailPesanan->pesanan_id);
            
            // Load relasi untuk response
            $prosesPesanan->load(['operator', 'mesin', 'detailPesanan.custom', 'detailPesanan.pesanan']);
            
            return response()->json([
                'success' => true,
                'data' => $prosesPesanan,
                'message' => 'Proses pesanan berhasil diselesaikan'
            ]);
        } catch (\Exception $e) {
            Log::error('Error completing process: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyelesaikan proses pesanan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Membatalkan proses pesanan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancel(Request $request, $id)
    {
        try {
            $prosesPesanan = ProsesPesanan::find($id);
            
            if (!$prosesPesanan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Proses pesanan tidak ditemukan'
                ], 404);
            }
            
            // Validasi input
            $validator = Validator::make($request->all(), [
                'alasan' => 'nullable|string'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            // Set status dibatalkan
            $prosesPesanan->status_proses = 'Dibatalkan';
            
            if ($request->has('alasan')) {
                $prosesPesanan->catatan = 'Dibatalkan: ' . $request->alasan;
            }
            
            $prosesPesanan->save();
            
            return response()->json([
                'success' => true,
                'data' => $prosesPesanan,
                'message' => 'Proses pesanan berhasil dibatalkan'
            ]);
        } catch (\Exception $e) {
            Log::error('Error canceling process: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan proses pesanan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan proses pesanan berdasarkan pesanan ID.
     *
     * @param  int  $pesananId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByPesanan($pesananId)
    {
        try {
            $prosesPesanan = ProsesPesanan::with([
                'detailPesanan',
                'operator',
                'mesin'
            ])->whereHas('detailPesanan', function($q) use ($pesananId) {
                $q->where('pesanan_id', $pesananId);
            })->get();
            
            return response()->json([
                'success' => true,
                'data' => $prosesPesanan,
                'message' => 'Daftar proses pesanan berhasil dimuat'
            ]);
        } catch (\Exception $e) {
            Log::error('Error retrieving processes by order: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat daftar proses pesanan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan proses pesanan yang ditugaskan kepada operator tertentu.
     *
     * @param  int  $operatorId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByOperator($operatorId)
    {
        try {
            $prosesPesanan = ProsesPesanan::with([
                'detailPesanan.pesanan',
                'detailPesanan.custom',
                'mesin'
            ])->where('operator_id', $operatorId)
              ->whereNotIn('status_proses', ['Selesai Produksi', 'Dibatalkan'])
              ->orderBy('waktu_mulai', 'asc')
              ->get();
            
            return response()->json([
                'success' => true,
                'data' => $prosesPesanan,
                'message' => 'Daftar proses pesanan operator berhasil dimuat'
            ]);
        } catch (\Exception $e) {
            Log::error('Error retrieving processes by operator: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat daftar proses pesanan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan statistik proses pesanan (jumlah per status, rata-rata waktu penyelesaian, dll).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStatistics()
    {
        try {
            // Jumlah proses per status
            $prosesPerStatus = ProsesPesanan::select('status_proses', DB::raw('count(*) as total'))
                ->groupBy('status_proses')
                ->get()
                ->pluck('total', 'status_proses')
                ->toArray();
            
            // Jumlah proses per operator
            $prosesPerOperator = ProsesPesanan::select('operator_id', DB::raw('count(*) as total'))
                ->groupBy('operator_id')
                ->get();
            
            // Load nama operator
            foreach ($prosesPerOperator as $proses) {
                $operator = \App\Models\Operator::find($proses->operator_id);
                $proses->operator_nama = $operator ? $operator->nama : 'Unknown';
            }
            
            // Jumlah proses per mesin
            $prosesPerMesin = ProsesPesanan::select('mesin_id', DB::raw('count(*) as total'))
                ->groupBy('mesin_id')
                ->get();
            
            // Load nama mesin
            foreach ($prosesPerMesin as $proses) {
                $mesin = \App\Models\Mesin::find($proses->mesin_id);
                $proses->mesin_nama = $mesin ? $mesin->nama_mesin : 'Unknown';
            }
            
            // Rata-rata waktu penyelesaian (dalam menit)
            $avgCompletionTime = ProsesPesanan::whereNotNull('waktu_selesai')
                ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, waktu_mulai, waktu_selesai)) as avg_time')
                ->first();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'proses_per_status' => $prosesPerStatus,
                    'proses_per_operator' => $prosesPerOperator,
                    'proses_per_mesin' => $prosesPerMesin,
                    'rata_rata_waktu_penyelesaian' => $avgCompletionTime->avg_time ?? 0
                ],
                'message' => 'Statistik proses pesanan berhasil dimuat'
            ]);
        } catch (\Exception $e) {
            Log::error('Error retrieving process statistics: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat statistik proses pesanan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Memeriksa dan memperbarui status pesanan berdasarkan proses yang telah selesai.
     *
     * @param  int  $pesananId
     * @return void
     */
    private function checkAndUpdateOrderStatus($pesananId)
    {
        try {
            $pesanan = Pesanan::with('detailPesanans.prosesPesanan')->find($pesananId);
            
            if (!$pesanan) {
                return;
            }
            
            // Periksa apakah semua proses telah selesai
            $allCompleted = true;
            foreach ($pesanan->detailPesanans as $detailPesanan) {
                if (!$detailPesanan->prosesPesanan || $detailPesanan->prosesPesanan->status_proses !== 'Selesai Produksi') {
                    $allCompleted = false;
                    break;
                }
            }
            
            // Jika semua proses telah selesai, update status pesanan
            if ($allCompleted) {
                if ($pesanan->metode_pengambilan === 'ambil') {
                    $pesanan->status = 'Menunggu Pengambilan';
                } else {
                    $pesanan->status = 'Sedang Dikirim';
                }
                $pesanan->save();
                
                Log::info('Order status automatically updated', [
                    'pesanan_id' => $pesanan->id,
                    'new_status' => $pesanan->status
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error checking order status: ' . $e->getMessage());
        }
    }
}