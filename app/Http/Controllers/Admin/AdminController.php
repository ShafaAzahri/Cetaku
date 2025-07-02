<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\TokoInfo;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        try {
            // Ambil daftar bulan-tahun unik dari tabel pesanan
            $months = Pesanan::selectRaw('MONTH(tanggal_dipesan) as month, YEAR(tanggal_dipesan) as year')
                ->distinct()
                ->orderByDesc('year')
                ->orderByDesc('month')
                ->get();

            // Ambil bulan yang dipilih atau default ke bulan sekarang
            $selectedMonth = $request->get('month', now()->format('Y-m'));
            $date = Carbon::createFromFormat('Y-m', $selectedMonth);
            $currentMonth = $date->month;
            $currentYear = $date->year;

            // Hitung jumlah pesanan bulan ini
            $pesananBulanIni = Pesanan::whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->count();

            // Jumlah pesanan selesai
            $pesananSelesaiBulanIni = Pesanan::whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->where('status', 'Selesai')
                ->count();

            // Jumlah pesanan berjalan
            $pesananBerjalan = Pesanan::whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->whereNotIn('status', ['Selesai', 'Dibatalkan'])
                ->count();

            // Jumlah pesanan dibatalkan
            $pesananDibatalkan = Pesanan::whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->where('status', 'Dibatalkan')
                ->count();

            // Pesanan terbaru
            $pesananTerbaru = Pesanan::join('users', 'users.id', '=', 'pesanans.user_id')
                ->join('detail_pesanans', 'detail_pesanans.pesanan_id', '=', 'pesanans.id')
                ->select(
                    'pesanans.id as pesanan_id',
                    'users.nama as pelanggan',
                    'pesanans.status',
                    'pesanans.total'
                )
                ->whereMonth('pesanans.created_at', $currentMonth)
                ->whereYear('pesanans.created_at', $currentYear)
                ->orderBy('pesanans.created_at', 'desc')
                ->take(5)
                ->get();

            // Total penjualan
            $totalPenjualan = DetailPesanan::join('pesanans', 'pesanans.id', '=', 'detail_pesanans.pesanan_id')
                ->where('pesanans.status', '!=', 'Dibatalkan')
                ->whereMonth('pesanans.created_at', $currentMonth)
                ->whereYear('pesanans.created_at', $currentYear)
                ->sum('pesanans.total');

            // Riwayat pesanan selesai atau dibatalkan
            $riwayatPesanan = Pesanan::whereIn('status', ['Selesai', 'Dibatalkan'])
                ->whereNotNull('created_at')
                ->orderBy('created_at', 'desc')
                ->get(['id', 'created_at', 'status']);

            // Data grafik: pesanan per tanggal
            $jumlahHari = Carbon::create($currentYear, $currentMonth)->daysInMonth;
            $dataPerTanggal = Pesanan::selectRaw('DAY(created_at) as tanggal, COUNT(*) as jumlah')
                ->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->groupByRaw('DAY(created_at)')
                ->pluck('jumlah', 'tanggal')
                ->toArray();

            $pesananPerTanggal = [];
            for ($i = 1; $i <= $jumlahHari; $i++) {
                $pesananPerTanggal[$i] = $dataPerTanggal[$i] ?? 0;
            }

            // Informasi toko
            $tokoInfo = TokoInfo::first();

            // Return ke view
            return view('admin.dashboard', compact(
                'tokoInfo',
                'pesananBulanIni',
                'pesananSelesaiBulanIni',
                'pesananBerjalan',
                'pesananDibatalkan',
                'totalPenjualan',
                'pesananPerTanggal',
                'pesananTerbaru',
                'months',
                'selectedMonth',
                'riwayatPesanan',
                'jumlahHari'
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