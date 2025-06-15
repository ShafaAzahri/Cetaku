<?php


namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalesExport;
use App\Models\Pesanan;
use App\Models\Item;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    protected $apiBaseUrl;

    // Initialize API base URL
    public function __construct()
    {
        $this->apiBaseUrl = rtrim(env('API_URL', config('app.url')), '/') . '/superadmin';
    }

    // Show report with filter by date range
    // public function index(Request $request)
    // {
    //     // Set default start and end date if not provided
    //     $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());  // Default to the start of the current month
    //     $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());  // Default to the end of the current month

    //     // Get sales data with status "Selesai" within the date range
    //     $response = $this->sendApiRequest('get', '/superadmin/sales', [
    //         'start_date' => $startDate,
    //         'end_date' => $endDate,
    //     ]);

    //     // Log the response for debugging purposes
    //     Log::debug('Sales data response:', $response);

    //     // Handle if the response doesn't contain the expected data
    //     if (!isset($response['success']) || !$response['success']) {
    //         return view('superadmin.laporan.index')->with('error', $response['message'] ?? 'Failed to fetch sales data');
    //     }

    //     // Calculate the sum of total prices
    //     $totalPrice = 0;
    //     foreach ($response['sales_data'] as $sale) {
    //         $totalPrice += $sale['total_harga'] ?? 0;
    //     }

    //     // Pass the sales data, total price, and the date range to the view
    //     return view('superadmin.laporan.index', [
    //         'salesData' => $response['sales_data'],
    //         'startDate' => $startDate,
    //         'endDate' => $endDate,
    //         'totalPrice' => $totalPrice, // Pass total price
    //     ]);;
    // }
    public function index(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());

        // Mengambil data penjualan
        $response = $this->sendApiRequest('get', '/superadmin/sales', [
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        // Jika response gagal
        if (!isset($response['success']) || !$response['success']) {
            return view('superadmin.laporan.index')->with('error', $response['message'] ?? 'Failed to fetch sales data');
        }

        $totalPrice = 0;
        foreach ($response['sales_data'] as $sale) {
            $totalPrice += $sale['total_harga'] ?? 0;
        }

        // Mendapatkan produk unggulan
        $topItems = $this->getTopSellingItems($startDate, $endDate);

        return view('superadmin.laporan.index', [
            'salesData' => $response['sales_data'],
            'topItems' => $topItems,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalPrice' => $totalPrice,
        ]);
    }





    // Fetch API request helper function
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
                'message' => 'Failed to communicate with server'
            ];
        }
    }

    // Export the sales data and top-selling items to Excel
    public function exportExcel(Request $request)
    {
        // Get date range for filter
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());

        // Fetch sales data from the database
        // $salesData = Pesanan::select('pesanans.tanggal_dipesan', 'pesanans.status', 'detail_pesanans.total_harga')
        //     ->join('detail_pesanans', 'pesanans.id', '=', 'detail_pesanans.pesanan_id')
        //     ->where('pesanans.status', 'Selesai')
        //     ->whereBetween('pesanans.tanggal_dipesan', [$startDate, $endDate])
        //     ->get();

        // Ambil data dari API biar sesuai tampilan laporan
        $response = $this->sendApiRequest('get', '/superadmin/sales', [
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        // Cek respon API
        if (!isset($response['success']) || !$response['success']) {
            return back()->with('error', $response['message'] ?? 'Gagal mengambil data penjualan');
        }

        $salesData = collect($response['sales_data']);
        $totalPrice = $salesData->sum('total_harga');


        // Calculate total price of sales data
        $totalPrice = $salesData->sum('total_harga');

        // Fetch top-selling items
        $topSellingItems = $this->getTopSellingItems($startDate, $endDate);

        // Export data to Excel with multiple sheets (Sales Data and Top Selling Items)
        return Excel::download(new SalesExport($salesData, $totalPrice, $topSellingItems), 'laporan_penjualan.xlsx');
    }

    // Fetch top-selling items from the database
    public function getTopSellingItems($startDate, $endDate)
    {
        return Item::join('customs', 'items.id', '=', 'customs.item_id')
            ->join('detail_pesanans', 'customs.id', '=', 'detail_pesanans.custom_id')
            ->select('items.nama_item', DB::raw('SUM(detail_pesanans.jumlah) as total_terjual'), DB::raw('SUM(detail_pesanans.total_harga) as total_pendapatan'))
            ->groupBy('items.id', 'items.nama_item')
            ->orderByDesc('total_terjual') // Urutkan berdasarkan jumlah terjual
            ->limit(10) // Ambil 10 produk terlaris
            ->get();
    }
}
