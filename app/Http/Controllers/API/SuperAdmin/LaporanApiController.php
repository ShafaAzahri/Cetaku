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


    public function getSalesData(Request $request)
    {
        try {
            // Get the date range from the request, or default to the last month
            $startDate = $request->get('start_date', now()->subMonth()->startOfMonth()->toDateString());
            $endDate = $request->get('end_date', now()->subMonth()->endOfMonth()->toDateString());

            // Fetch the sales data for completed orders
            $salesData = Pesanan::select('pesanans.id', 'pesanans.created_at', 'pesanans.status', DB::raw('SUM(detail_pesanans.total_harga) as total_harga'))
                ->join('detail_pesanans', 'pesanans.id', '=', 'detail_pesanans.pesanan_id')
                ->where('pesanans.status', 'Selesai')
                ->whereBetween('pesanans.created_at', [$startDate, $endDate])
                ->groupBy('pesanans.id', 'pesanans.created_at', 'pesanans.status')
                ->orderBy('pesanans.created_at', 'desc')  // Order by date (latest first)
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

    public function getRincianPesanan(Request $request)
    {
        try {
            $startDate = $request->get('start_date', now()->subMonth()->startOfMonth()->toDateString());
            $endDate = $request->get('end_date', now()->subMonth()->endOfMonth()->toDateString());

            $data = Pesanan::with(['user:id,nama', 'detailPesanans.item:id,nama,harga'])
                ->where('status', 'Selesai')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('created_at', 'desc')
                ->get();

            $result = $data->map(function ($pesanan) {
                return [
                    'tanggal' => $pesanan->created_at->format('d/m/Y'),
                    'nama_pemesan' => $pesanan->user->nama ?? '-',
                    'items' => $pesanan->detailPesanans->map(function ($detail) {
                        return [
                            'nama_item' => $detail->item->nama ?? '-',
                            'qty' => $detail->jumlah,
                            'harga_satuan' => $detail->harga_satuan,
                            'total' => $detail->total_harga
                        ];
                    }),
                    'total_pesanan' => $pesanan->detailPesanans->sum('total_harga')
                ];
            });

            return response()->json([
                'success' => true,
                'rincian' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching rincian pesanan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching rincian pesanan',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
