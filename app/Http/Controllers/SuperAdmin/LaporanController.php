<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalesExport;
use App\Models\Pesanan;
use App\Models\Item;

class LaporanController extends Controller
{
    protected $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = rtrim(env('API_URL', config('app.url')), '/') . '/superadmin';
    }

    public function index(Request $request)
{
    // Tanggal default (awal & akhir bulan ini)
    $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
    $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());

    // Ambil data penjualan dari API
    $response = $this->sendApiRequest('get', '/superadmin/sales', [
        'start_date' => $startDate,
        'end_date' => $endDate,
    ]);

    // Cek jika response tidak sukses atau sales_data tidak ada
    if (!isset($response['success']) || !$response['success'] || !isset($response['sales_data'])) {
        return view('superadmin.laporan.index')->with('error', $response['message'] ?? 'Gagal mengambil data penjualan');
    }

    // Ambil total harga penjualan dari tabel pesanans
    $totalPrice = DB::table('pesanans')
        ->where('status', 'Selesai')  // Pastikan hanya mengambil pesanan yang sudah selesai
        ->whereBetween('created_at', [$startDate, $endDate])  // Filter berdasarkan rentang tanggal
        ->sum('total');  // Menjumlahkan nilai kolom 'total' dari tabel pesanans

    // Ambil produk unggulan (top selling items)
    $topItems = $this->getTopSellingItems($startDate, $endDate);

    // Ambil data rincian detail pesanan dari database
    $detailRincian = DB::table('detail_pesanans')
        ->join('customs', 'detail_pesanans.custom_id', '=', 'customs.id')
        ->join('items', 'customs.item_id', '=', 'items.id')
        ->join('pesanans', 'detail_pesanans.pesanan_id', '=', 'pesanans.id')
        ->join('users', 'pesanans.user_id', '=', 'users.id')
        ->leftJoin('ekspedisis', 'pesanans.id', '=', 'ekspedisis.pesanan_id')
        ->select(
            'pesanans.id as pesanan_id',
            'pesanans.created_at as tanggal_pesanan',
            'users.nama as nama_pemesan',
            'items.nama_item',
            'customs.harga as harga_satuan',
            'detail_pesanans.jumlah',
            'pesanans.total',
            'detail_pesanans.biaya_jasa',
            'ekspedisis.ongkos_kirim'
        )
        ->where('pesanans.status', 'Selesai')  // Status pesanan Selesai
        ->whereBetween('pesanans.created_at', [$startDate, $endDate])  // Filter berdasarkan rentang tanggal
        ->orderBy('pesanans.id')
        ->get();

    // Kembalikan data ke view
    return view('superadmin.laporan.index', [
        'salesData' => $response['sales_data'],
        'topItems' => $topItems,
        'detailRincian' => $detailRincian,
        'startDate' => $startDate,
        'endDate' => $endDate,
        'totalPrice' => $totalPrice,  // Total harga dari tabel pesanans
    ]);
}


    // Fungsi bantu untuk request API
    protected function sendApiRequest($method, $endpoint, $data = [])
    {
        try {
            $token = session('api_token');

            $response = Http::withToken($token)
                ->accept('application/json')
                ->$method($this->apiBaseUrl . $endpoint, $data);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('API request failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Gagal terhubung ke server',
            ];
        }
    }

    // Fungsi untuk produk unggulan
    public function getTopSellingItems($startDate, $endDate)
    {
        return Item::join('customs', 'items.id', '=', 'customs.item_id')
            ->join('detail_pesanans', 'customs.id', '=', 'detail_pesanans.custom_id')
            ->join('pesanans', 'detail_pesanans.pesanan_id', '=', 'pesanans.id')
            ->where('pesanans.status', 'Selesai')
            ->whereBetween('pesanans.created_at', [$startDate, $endDate])
            ->select(
                'items.nama_item',
                DB::raw('SUM(detail_pesanans.jumlah) as total_terjual'),
                DB::raw('SUM(detail_pesanans.total_harga) as total_pendapatan')
            )
            ->groupBy('items.id', 'items.nama_item')
            ->orderByDesc('total_terjual')
            ->limit(10)
            ->get();
    }

    // Fungsi export ke Excel
    public function exportExcel(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());

        $salesData = Pesanan::select('pesanans.created_at', 'pesanans.status', 'pesanans.total')
            ->join('detail_pesanans', 'pesanans.id', '=', 'detail_pesanans.pesanan_id')
            ->where('pesanans.status', 'Selesai')
            ->whereBetween('pesanans.created_at', [$startDate, $endDate])
            ->get();


        $totalPrice = $salesData->sum('total');

        $topSellingItems = $this->getTopSellingItems($startDate, $endDate);
        // Ambil data rincian detail pesanan
        $detailRincian = DB::table('detail_pesanans')
            ->join('customs', 'detail_pesanans.custom_id', '=', 'customs.id')
            ->join('items', 'customs.item_id', '=', 'items.id')
            ->join('pesanans', 'detail_pesanans.pesanan_id', '=', 'pesanans.id')
            ->join('users', 'pesanans.user_id', '=', 'users.id')
            ->leftJoin('ekspedisis', 'pesanans.id', '=', 'ekspedisis.pesanan_id')
            ->select(
                'pesanans.id as pesanan_id',
                'pesanans.created_at as tanggal_pesanan',
                'users.nama as nama_pemesan',
                'items.nama_item',
                'customs.harga as harga_satuan',
                'detail_pesanans.jumlah',
                'pesanans.total',
                'detail_pesanans.biaya_jasa',
                'ekspedisis.ongkos_kirim'
            )

            ->where('pesanans.status', 'Selesai')
            ->whereBetween('pesanans.created_at', [$startDate, $endDate])
            ->orderByDesc('pesanans.created_at')
            ->get();

        $adminName = auth()->check() ? auth()->user()->nama : 'Superadmin';

        return Excel::download(
            new SalesExport(
                $salesData,
                $totalPrice,
                $topSellingItems,
                $detailRincian,
                $adminName,       // sekarang aman
                $startDate,
                $endDate
            ),
            'laporan_penjualan.xlsx'
        );



    }
}