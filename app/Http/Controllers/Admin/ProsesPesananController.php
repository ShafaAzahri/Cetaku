<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProsesPesananController extends Controller
{
    protected $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = rtrim(env('API_URL', config('app.url')), '/');
    }

    /**
     * Menampilkan halaman daftar proses pesanan
     */
    public function index(Request $request)
    {
        try {
            // Ambil parameter filter jika ada
            $filters = [];
            if ($request->has('status')) {
                $filters['status'] = $request->status;
            }
            if ($request->has('operator_id')) {
                $filters['operator_id'] = $request->operator_id;
            }
            if ($request->has('mesin_id')) {
                $filters['mesin_id'] = $request->mesin_id;
            }
            if ($request->has('start_date')) {
                $filters['start_date'] = $request->start_date;
            }
            if ($request->has('end_date')) {
                $filters['end_date'] = $request->end_date;
            }
            
            // Ambil data proses pesanan dari API
            $response = Http::withToken(session('api_token'))
                ->get($this->apiBaseUrl . '/api/proses', $filters);
            
            if (!$response->successful()) {
                // Handle error
                $errorMessage = $response->json()['message'] ?? 'Gagal memuat data proses pesanan';
                return view('admin.proses.index')->with('error', $errorMessage);
            }
            
            $data = $response->json();
            
            // Ambil data operator dan mesin untuk form filter
            $operatorResponse = Http::withToken(session('api_token'))
                ->get($this->apiBaseUrl . '/api/operator/list');
            
            $mesinResponse = Http::withToken(session('api_token'))
                ->get($this->apiBaseUrl . '/api/mesin/list');
            
            // Persiapkan data untuk view
            $viewData = [
                'prosesPesanan' => $data['data'] ?? [],
                'operators' => $operatorResponse->successful() ? $operatorResponse->json()['data'] : [],
                'mesins' => $mesinResponse->successful() ? $mesinResponse->json()['data'] : [],
                'filters' => $filters
            ];
            
            return view('admin.proses.index', $viewData);
        } catch (\Exception $e) {
            Log::error('Error saat memuat halaman proses pesanan: ' . $e->getMessage());
            return view('admin.proses.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan halaman detail proses pesanan
     */
    public function show($id)
    {
        try {
            // Ambil detail proses pesanan dari API
            $response = Http::withToken(session('api_token'))
                ->get($this->apiBaseUrl . '/api/proses/' . $id);
            
            if (!$response->successful()) {
                // Handle error
                $errorMessage = $response->json()['message'] ?? 'Gagal memuat detail proses pesanan';
                return redirect()->route('admin.proses.index')->with('error', $errorMessage);
            }
            
            $prosesPesanan = $response->json()['data'];
            
            return view('admin.proses.show', ['prosesPesanan' => $prosesPesanan]);
        } catch (\Exception $e) {
            Log::error('Error saat memuat detail proses pesanan: ' . $e->getMessage());
            return redirect()->route('admin.proses.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Menangani update status proses
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            // Validasi input
            $request->validate([
                'status_proses' => 'required|string'
            ]);
            
            // Kirim request ke API
            $response = Http::withToken(session('api_token'))
                ->put($this->apiBaseUrl . '/api/proses/' . $id, [
                    'status_proses' => $request->status_proses,
                    'catatan' => $request->catatan
                ]);
            
            if (!$response->successful()) {
                $errorMessage = $response->json()['message'] ?? 'Gagal mengupdate status proses';
                return back()->with('error', $errorMessage);
            }
            
            return back()->with('success', 'Status proses berhasil diperbarui');
        } catch (\Exception $e) {
            Log::error('Error saat update status proses: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Menangani penyelesaian proses
     */
    public function complete(Request $request, $id)
    {
        try {
            // Kirim request ke API
            $response = Http::withToken(session('api_token'))
                ->put($this->apiBaseUrl . '/api/proses/' . $id . '/selesai', [
                    'catatan' => $request->catatan
                ]);
            
            if (!$response->successful()) {
                $errorMessage = $response->json()['message'] ?? 'Gagal menyelesaikan proses';
                return back()->with('error', $errorMessage);
            }
            
            return back()->with('success', 'Proses berhasil diselesaikan');
        } catch (\Exception $e) {
            Log::error('Error saat menyelesaikan proses: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Menangani pembatalan proses
     */
    public function cancel(Request $request, $id)
    {
        try {
            // Kirim request ke API
            $response = Http::withToken(session('api_token'))
                ->put($this->apiBaseUrl . '/api/proses/' . $id . '/batal', [
                    'alasan' => $request->alasan
                ]);
            
            if (!$response->successful()) {
                $errorMessage = $response->json()['message'] ?? 'Gagal membatalkan proses';
                return back()->with('error', $errorMessage);
            }
            
            return back()->with('success', 'Proses berhasil dibatalkan');
        } catch (\Exception $e) {
            Log::error('Error saat membatalkan proses: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}