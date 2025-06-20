<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\TokoInfo; // Assuming you have a model for TokoInfo
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        try {

            // Ambil bulan dan tahun saat ini
            // $selectedMonth = $request->get('month', Carbon::now()->format('Y-m'));
            // $currentMonth = now()->month;
            // $currentYear = now()->year;

            // Ambil bulan dan tahun unik dari tabel pesanan
            $months = Pesanan::selectRaw('MONTH(tanggal_dipesan) as month, YEAR(tanggal_dipesan) as year')
                ->distinct()
                ->orderByDesc('year')
                ->orderByDesc('month')
                ->get();

            // Ambil bulan yang dipilih (dari query string atau default ke bulan saat ini)
            $selectedMonth = $request->get('month', now()->format('Y-m'));
            $date = Carbon::createFromFormat('Y-m', $selectedMonth);
            $currentMonth = $date->month;
            $currentYear = $date->year;

            // Hitung jumlah pesanan bulan ini
            $pesananBulanIni = Pesanan::whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->count();

            // Hitung jumlah pesanan selesai bulan ini
            $pesananSelesaiBulanIni = Pesanan::whereMonth('waktu_pengambilan', $currentMonth)
                ->whereYear('waktu_pengambilan', $currentYear)
                ->where('status', 'Selesai')
                ->count();

            // Hitung jumlah pesanan berjalan bulan ini
            $pesananBerjalan = Pesanan::whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->where('status', '!=', 'Selesai')
                ->count();

            // Ambil data Pesanan Terbaru
            $pesananTerbaru = Pesanan::join('users', 'users.id', '=', 'pesanans.user_id')
                ->join('detail_pesanans', 'detail_pesanans.pesanan_id', '=', 'pesanans.id')
                ->select(
                    'pesanans.id as pesanan_id',
                    'users.nama as pelanggan',
                    'pesanans.status',
                    'detail_pesanans.total_harga'
                )
                ->whereMonth('pesanans.created_at', $currentMonth)
                ->whereYear('pesanans.created_at', $currentYear)
                ->orderBy('pesanans.created_at', 'desc')
                ->take(7)  // Limit to the latest 7 orders
                ->get();

            // Hitung total penjualan bulan ini berdasarkan pesanan selesai
            $totalPenjualan = DetailPesanan::join('pesanans', 'pesanans.id', '=', 'detail_pesanans.pesanan_id')
                ->where('pesanans.status', 'Selesai')
                ->whereMonth('pesanans.created_at', $currentMonth)
                ->whereYear('pesanans.created_at', $currentYear)
                ->sum('detail_pesanans.total_harga');

            // RIWAYAT PESANAN 
            $riwayatPesanan = Pesanan::whereIn('status', ['Selesai', 'Dibatalkan'])
                ->whereNotNull('created_at') // Ensures that records with NULL created_at are excluded
                ->orderBy('created_at', 'desc')
                ->get(['id', 'created_at', 'status']);

            // Hitung jumlah pesanan per periode (1-5, 6-10, dst.)
            $pesananPerTanggal = [];
            $periods = ['1-5', '6-10', '11-15', '16-20', '21-25', '26-31'];

            foreach ($periods as $period) {
                // Ambil tanggal awal dan akhir periode
                list($start, $end) = explode('-', $period);
                $startDate = Carbon::createFromDate($currentYear, $currentMonth, $start);
                $endDate = Carbon::createFromDate($currentYear, $currentMonth, $end);

                // Hitung jumlah pesanan dalam periode ini
                $count = Pesanan::whereBetween('created_at', [$startDate, $endDate])
                    ->whereMonth('created_at', $currentMonth)
                    ->whereYear('created_at', $currentYear)
                    ->count();
                $pesananPerTanggal[] = $count;
            }
            $tokoInfo = TokoInfo::first();

            // Kirim variabel ke view
            return view('admin.dashboard', compact(
                'tokoInfo',
                'pesananBulanIni',
                'pesananSelesaiBulanIni',
                'pesananBerjalan',
                'totalPenjualan',
                'pesananPerTanggal',
                'pesananTerbaru',
                'months',
                'selectedMonth',
                'riwayatPesanan' // Pass the latest orders data to the view
            ));
        } catch (\Exception $e) {
            Log::error('Error calculating stats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching statistics',
            ], 500);
        }
    }
}
