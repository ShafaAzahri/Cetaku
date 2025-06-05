<?php

namespace App\Http\Controllers\API\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LaporanApiController extends Controller
{
    // LaporanApiController.php

    // public function getSalesData(Request $request)
    // {
    //     try {
    //         // Get the date range from the request, or default to the last month
    //         $startDate = $request->get('start_date', now()->subMonth()->startOfMonth()->toDateString());
    //         $endDate = $request->get('end_date', now()->subMonth()->endOfMonth()->toDateString());

    //         // Fetch the sales data for completed orders
    //         $salesData = Pesanan::select('pesanans.id', 'pesanans.created_at', 'pesanans.status', DB::raw('SUM(detail_pesanans.total_harga) as total_harga'))
    //             ->join('detail_pesanans', 'pesanans.id', '=', 'detail_pesanans.pesanan_id')
    //             ->where('pesanans.status', 'Selesai')
    //             ->whereBetween('pesanans.created_at', [$startDate, $endDate])
    //             ->groupBy('pesanans.id', 'pesanans.created_at', 'pesanans.status')
    //             ->orderBy('pesanans.created_at', 'desc')  // Order by date (latest first)
    //             ->get();

    //         return response()->json([
    //             'success' => true,
    //             'sales_data' => $salesData,


    //         ]);
    //     } catch (\Exception $e) {
    //         Log::error('Error fetching sales data: ' . $e->getMessage());
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Error fetching sales data',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function getSalesData(Request $request)
    {
        try {
            // Mendapatkan rentang tanggal dari request
            $startDate = $request->get('start_date', now()->subMonth()->startOfMonth()->toDateString());
            $endDate = $request->get('end_date', now()->subMonth()->endOfMonth()->toDateString());

            // Ambil data penjualan yang statusnya "Selesai"
            $salesData = Pesanan::select('pesanans.id', 'pesanans.created_at', 'pesanans.status', DB::raw('SUM(detail_pesanans.total_harga) as total_harga'))
                ->join('detail_pesanans', 'pesanans.id', '=', 'detail_pesanans.pesanan_id')
                ->where('pesanans.status', 'Selesai')
                ->whereBetween('pesanans.created_at', [$startDate, $endDate])
                ->groupBy('pesanans.id', 'pesanans.created_at', 'pesanans.status')
                ->orderBy('pesanans.created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'sales_data' => $salesData,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching sales data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching sales data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
