<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProsesPesananController extends Controller
{
    private readonly string $apiUrl;
    
    public function __construct()
    {
        $this->apiUrl = rtrim(config('app.api_url', config('app.url')), '/') . '/api';
    }
    
    /**
     * Menampilkan daftar proses pesanan
     */
    public function index(Request $request)
    {
        try {
            $params = $this->buildFilterParams($request);
            $response = $this->callApi('GET', '/proses', $params);
            
            $data = [
                'prosesPesanan' => $this->formatProsesList($response['data'] ?? []),
                'operators' => $this->getOperators(),
                'mesins' => $this->getMesins(),
                'filters' => $request->only(['status', 'operator_id', 'mesin_id', 'start_date', 'end_date']),
                'statusOptions' => $this->getStatusOptions()
            ];
            
            return view('admin.proses.index', $data);
        } catch (\Exception $e) {
            Log::error('Error loading proses list: ' . $e->getMessage());
            return view('admin.proses.index')
                ->with('error', 'Gagal memuat daftar proses: ' . $e->getMessage());
        }
    }
    
    /**
     * Menampilkan detail proses pesanan
     */
    public function show($id)
    {
        try {
            $proses = $this->callApi('GET', "/proses/{$id}")['data'];
            
            return view('admin.proses.show', [
                'prosesPesanan' => $this->formatProsesDetail($proses),
                'operators' => $this->getOperators(),
                'mesins' => $this->getMesins(),
                'statusOptions' => $this->getStatusOptions()
            ]);
        } catch (\Exception $e) {
            Log::error("Error loading proses {$id}: " . $e->getMessage());
            return redirect()->route('admin.proses.index')
                ->with('error', 'Proses tidak ditemukan');
        }
    }
    
    /**
     * Update status proses
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status_proses' => 'required|in:' . implode(',', array_keys($this->getStatusOptions())),
            'catatan' => 'nullable|string|max:500'
        ]);
        
        try {
            $this->callApi('PUT', "/proses/{$id}", $request->only(['status_proses', 'catatan']));
            
            return redirect()->route('admin.proses.show', $id)
                ->with('success', 'Status proses berhasil diperbarui');
        } catch (\Exception $e) {
            return $this->handleException($e, 'admin.proses.show', $id);
        }
    }
    
    /**
     * Menyelesaikan proses
     */
    public function complete(Request $request, $id)
    {
        $request->validate([
            'catatan' => 'nullable|string|max:500'
        ]);
        
        try {
            $this->callApi('PUT', "/proses/{$id}/selesai", $request->only(['catatan']));
            
            return redirect()->route('admin.proses.show', $id)
                ->with('success', 'Proses berhasil diselesaikan');
        } catch (\Exception $e) {
            return $this->handleException($e, 'admin.proses.show', $id);
        }
    }
    
    /**
     * Membatalkan proses
     */
    public function cancel(Request $request, $id)
    {
        $request->validate([
            'alasan' => 'nullable|string|max:500'
        ]);
        
        try {
            $this->callApi('PUT', "/proses/{$id}/batal", $request->only(['alasan']));
            
            return redirect()->route('admin.proses.show', $id)
                ->with('success', 'Proses berhasil dibatalkan');
        } catch (\Exception $e) {
            return $this->handleException($e, 'admin.proses.show', $id);
        }
    }
    
    /**
     * Menampilkan proses untuk operator
     */
    public function byOperator($operatorId)
    {
        try {
            $response = $this->callApi('GET', "/proses/operator/{$operatorId}");
            $operator = $this->getOperatorById($operatorId);
            
            return view('admin.proses.by-operator', [
                'prosesPesanan' => $this->formatProsesList($response['data'] ?? []),
                'operator' => $operator,
                'statusOptions' => $this->getStatusOptions()
            ]);
        } catch (\Exception $e) {
            Log::error("Error loading proses for operator {$operatorId}: " . $e->getMessage());
            return redirect()->route('admin.proses.index')
                ->with('error', 'Gagal memuat proses operator');
        }
    }
    
    /**
     * Menampilkan proses untuk pesanan
     */
    public function byPesanan($pesananId)
    {
        try {
            $response = $this->callApi('GET', "/proses/pesanan/{$pesananId}");
            $pesanan = $this->getPesananById($pesananId);
            
            return view('admin.proses.by-pesanan', [
                'prosesPesanan' => $this->formatProsesList($response['data'] ?? []),
                'pesanan' => $pesanan,
                'statusOptions' => $this->getStatusOptions()
            ]);
        } catch (\Exception $e) {
            Log::error("Error loading proses for pesanan {$pesananId}: " . $e->getMessage());
            return redirect()->route('admin.proses.index')
                ->with('error', 'Gagal memuat proses pesanan');
        }
    }
    
    /**
     * Menampilkan statistik proses
     */
    public function statistics()
    {
        try {
            $stats = $this->callApi('GET', '/proses/statistik')['data'];
            
            return view('admin.proses.statistics', [
                'statistics' => $this->formatStatistics($stats)
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading proses statistics: ' . $e->getMessage());
            return view('admin.proses.statistics')
                ->with('error', 'Gagal memuat statistik proses');
        }
    }
    
    /**
     * Ajax endpoint untuk update status
     */
    public function ajaxUpdateStatus(Request $request, $id)
    {
        $request->validate([
            'status_proses' => 'required|in:' . implode(',', array_keys($this->getStatusOptions()))
        ]);
        
        try {
            $response = $this->callApi('PUT', "/proses/{$id}", [
                'status_proses' => $request->status_proses
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diperbarui',
                'data' => $response['data']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    // Private Helper Methods
    
    private function callApi(string $method, string $endpoint, ?array $data = null)
    {
        $response = Http::withToken(session('api_token'))
            ->timeout(30)
            ->$method($this->apiUrl . $endpoint, $data);
        
        if (!$response->successful()) {
            throw new \Exception($response->json('message', 'API Error'));
        }
        
        return $response->json();
    }
    
    private function buildFilterParams(Request $request): array
    {
        $params = [];
        
        if ($request->filled('status')) {
            $params['status'] = $request->status;
        }
        
        if ($request->filled('operator_id')) {
            $params['operator_id'] = $request->operator_id;
        }
        
        if ($request->filled('mesin_id')) {
            $params['mesin_id'] = $request->mesin_id;
        }
        
        if ($request->filled('start_date')) {
            $params['start_date'] = $request->start_date;
        }
        
        if ($request->filled('end_date')) {
            $params['end_date'] = $request->end_date;
        }
        
        if ($request->filled('pesanan_id')) {
            $params['pesanan_id'] = $request->pesanan_id;
        }
        
        return $params;
    }
    
    private function formatProsesList(array $proses): array
    {
        return array_map(function ($item) {
            $detailPesanan = $item['detailPesanan'] ?? [];
            $pesanan = $detailPesanan['pesanan'] ?? [];
            $custom = $detailPesanan['custom'] ?? [];
            $item_data = $custom['item'] ?? [];
            
            return [
                'id' => $item['id'],
                'pesanan_id' => $pesanan['id'] ?? null,
                'detail_pesanan_id' => $item['detail_pesanan_id'],
                'produk' => $item_data['nama_item'] ?? 'Unknown',
                'operator' => $item['operator']['nama'] ?? 'Unknown',
                'mesin' => $item['mesin']['nama_mesin'] ?? 'Unknown',
                'status_proses' => $item['status_proses'],
                'waktu_mulai' => $this->formatDateTime($item['waktu_mulai']),
                'waktu_selesai' => $this->formatDateTime($item['waktu_selesai']),
                'pelanggan' => $pesanan['user']['nama'] ?? 'Unknown',
                'catatan' => $item['catatan'] ?? ''
            ];
        }, $proses);
    }
    
    private function formatProsesDetail(array $proses): array
    {
        $detailPesanan = $proses['detailPesanan'] ?? [];
        $pesanan = $detailPesanan['pesanan'] ?? [];
        $custom = $detailPesanan['custom'] ?? [];
        $item = $custom['item'] ?? [];
        $ukuran = $custom['ukuran'] ?? [];
        $bahan = $custom['bahan'] ?? [];
        $jenis = $custom['jenis'] ?? [];
        
        return [
            'id' => $proses['id'],
            'pesanan_id' => $pesanan['id'] ?? null,
            'detail_pesanan_id' => $proses['detail_pesanan_id'],
            'produk' => [
                'nama' => $item['nama_item'] ?? 'Unknown',
                'ukuran' => $ukuran['size'] ?? 'Unknown',
                'bahan' => $bahan['nama_bahan'] ?? 'Unknown',
                'jenis' => $jenis['kategori'] ?? 'Unknown',
                'jumlah' => $detailPesanan['jumlah'] ?? 0,
                'gambar' => $item['gambar'] ?? null
            ],
            'operator' => $proses['operator'] ?? [],
            'mesin' => $proses['mesin'] ?? [],
            'status_proses' => $proses['status_proses'],
            'waktu_mulai' => $proses['waktu_mulai'],
            'waktu_selesai' => $proses['waktu_selesai'],
            'catatan' => $proses['catatan'] ?? '',
            'pelanggan' => $pesanan['user'] ?? [],
            'pesanan' => $pesanan
        ];
    }
    
    private function formatStatistics(array $stats): array
    {
        return [
            'proses_per_status' => $this->formatStatusStatistics($stats['proses_per_status'] ?? []),
            'proses_per_operator' => $this->formatOperatorStatistics($stats['proses_per_operator'] ?? []),
            'proses_per_mesin' => $this->formatMesinStatistics($stats['proses_per_mesin'] ?? []),
            'rata_rata_waktu_penyelesaian' => $this->formatDuration($stats['rata_rata_waktu_penyelesaian'] ?? 0),
            'total_proses' => array_sum($stats['proses_per_status'] ?? []),
            'chart_data' => $this->prepareChartData($stats)
        ];
    }
    
    private function formatStatusStatistics(array $data): array
    {
        $formatted = [];
        foreach ($this->getStatusOptions() as $key => $label) {
            $formatted[$key] = [
                'label' => $label,
                'count' => $data[$key] ?? 0,
                'percentage' => $this->calculatePercentage($data[$key] ?? 0, array_sum($data))
            ];
        }
        return $formatted;
    }
    
    private function formatOperatorStatistics(array $data): array
    {
        if (empty($data)) {
            return [];
        }
        
        $total = array_sum(array_column($data, 'total'));
        
        return array_map(function ($item) use ($total) {
            return [
                'operator_id' => $item['operator_id'] ?? null,
                'nama' => $item['operator_nama'] ?? 'Unknown',
                'total' => $item['total'] ?? 0,
                'percentage' => $this->calculatePercentage($item['total'] ?? 0, $total)
            ];
        }, $data);
    }
    
    private function formatMesinStatistics(array $data): array
    {
        if (empty($data)) {
            return [];
        }
        
        $total = array_sum(array_column($data, 'total'));
        
        return array_map(function ($item) use ($total) {
            return [
                'mesin_id' => $item['mesin_id'] ?? null,
                'nama' => $item['mesin_nama'] ?? 'Unknown',
                'total' => $item['total'] ?? 0,
                'percentage' => $this->calculatePercentage($item['total'] ?? 0, $total)
            ];
        }, $data);
    }
    
    private function prepareChartData(array $stats): array
    {
        return [
            'status_chart' => [
                'labels' => array_keys($stats['proses_per_status'] ?? []),
                'data' => array_values($stats['proses_per_status'] ?? [])
            ],
            'operator_chart' => [
                'labels' => array_column($stats['proses_per_operator'] ?? [], 'operator_nama'),
                'data' => array_column($stats['proses_per_operator'] ?? [], 'total')
            ]
        ];
    }
    
    private function getOperators(): array
    {
        try {
            return $this->callApi('GET', '/operator/list')['data'] ?? [];
        } catch (\Exception $e) {
            return [];
        }
    }
    
    private function getMesins(): array
    {
        try {
            return $this->callApi('GET', '/mesin/list')['data'] ?? [];
        } catch (\Exception $e) {
            return [];
        }
    }
    
    private function getOperatorById($id)
    {
        $operators = $this->getOperators();
        return collect($operators)->firstWhere('id', $id) ?? ['id' => $id, 'nama' => 'Unknown'];
    }
    
    private function getPesananById($id)
    {
        try {
            return $this->callApi('GET', "/pesanan/{$id}")['data'];
        } catch (\Exception $e) {
            return ['id' => $id, 'status' => 'Unknown'];
        }
    }
    
    private function getStatusOptions(): array
    {
        return [
            'Ditugaskan' => 'Ditugaskan',
            'Dalam Antrian' => 'Dalam Antrian',
            'Sedang Dikerjakan' => 'Sedang Dikerjakan',
            'Selesai Produksi' => 'Selesai Produksi',
            'Gagal' => 'Gagal',
            'Dibatalkan' => 'Dibatalkan'
        ];
    }
    
    private function formatDateTime(?string $datetime): ?string
    {
        if (!$datetime) {
            return null;
        }
        
        try {
            return date('Y-m-d H:i:s', strtotime($datetime));
        } catch (\Exception $e) {
            return $datetime;
        }
    }
    
    private function formatDuration($minutes): string
    {
        if ($minutes < 60) {
            return round($minutes) . ' menit';
        }
        
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;
        
        if ($hours < 24) {
            return $hours . ' jam ' . round($remainingMinutes) . ' menit';
        }
        
        $days = floor($hours / 24);
        $remainingHours = $hours % 24;
        
        return $days . ' hari ' . $remainingHours . ' jam ' . round($remainingMinutes) . ' menit';
    }
    
    private function calculatePercentage($value, $total): float
    {
        if ($total == 0) {
            return 0;
        }
        
        return round(($value / $total) * 100, 1);
    }
    
    private function handleException(\Exception $e, string $route, $params = null)
    {
        Log::error($e->getMessage());
        
        $url = is_array($params) ? route($route, $params) : route($route, $params);
        return redirect($url)->with('error', $e->getMessage());
    }
}