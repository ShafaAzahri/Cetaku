<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DetailPesanan;
use App\Models\Pesanan;
use App\Models\item;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PesananManagerController extends Controller
{
    protected $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = rtrim(env('API_URL', config('app.url')), '/');
    }

    public function getPesananStats()
    {
        try {
            $currentMonth = now()->month;
            $currentYear = now()->year;

            $pesananBulanIni = Pesanan::whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->count();

            $pesananSelesaiBulanIni = Pesanan::whereMonth('waktu_pengambilan', $currentMonth)
                ->whereYear('waktu_pengambilan', $currentYear)
                ->where('status', 'Selesai')
                ->count();

            $pesananBerjalan = Pesanan::whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->where('status', '!=', 'Selesai')
                ->count();

            return view('admin.dashboard', compact(
                'pesananBulanIni',
                'pesananSelesaiBulanIni',
                'pesananBerjalan'
            ));
        } catch (\Exception $e) {
            Log::error('Error calculating stats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching statistics',
            ], 500);
        }
    }

    public function index(Request $request)
    {
        try {
            $status = $request->get('status', 'Semua Status');
            $search = $request->get('search', '');
            $dariTanggal = $request->get('dari_tanggal', '');
            $sampaiTanggal = $request->get('sampai_tanggal', '');
            $perPage = $request->get('per_page', 10);

            $response = $this->sendApiRequest('get', '/admin/pesanan', [
                'status' => $status,
                'search' => $search,
                'dari_tanggal' => $dariTanggal,
                'sampai_tanggal' => $sampaiTanggal,
                'per_page' => $perPage
            ]);

            if (!($response['success'] ?? false)) {
                return redirect()->back()->with('error', $response['message'] ?? 'Gagal memuat data pesanan');
            }

            $pesanans = $response['pesanans'];
            $statusOptions = $response['status_options'] ?? ['Pemesanan', 'Dikonfirmasi', 'Sedang Diproses', 'Menunggu Pengambilan', 'Sedang Dikirim', 'Selesai', 'Dibatalkan'];

            $statsResponse = $this->sendApiRequest('get', '/admin/pesanan/statistics');
            $stats = ($statsResponse['success'] ?? false) ? $statsResponse['statistics'] : null;

            return view('admin.pesanan.index', compact(
                'pesanans',
                'status',
                'search',
                'dariTanggal',
                'sampaiTanggal',
                'statusOptions',
                'stats'
            ));
        } catch (\Exception $e) {
            Log::error('Error pada halaman daftar pesanan: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data');
        }
    }

    public function show($id)
    {
        try {
            $response = $this->sendApiRequest('get', "/admin/pesanan/{$id}");

            if (!($response['success'] ?? false)) {
                return redirect()->route('admin.pesanan.index')
                    ->with('error', $response['message'] ?? 'Pesanan tidak ditemukan');
            }

            $pesanan = $response['pesanan'];
            $statusOptions = $response['status_options'] ?? [];
            $mesinList = $response['available_machines'] ?? [];
            $operatorList = $response['active_operators'] ?? [];

            $biayaDesainResponse = $this->sendApiRequest('get', '/biaya-desains');
            $biayaDesain = 0;
            if (($biayaDesainResponse['success'] ?? false) &&
                isset($biayaDesainResponse['biaya_desains']) &&
                count($biayaDesainResponse['biaya_desains']) > 0) {
                $biayaDesain = $biayaDesainResponse['biaya_desains'][0]['biaya'] ?? 0;
            }

            return view('admin.pesanan.show.show', compact(
                'pesanan',
                'statusOptions',
                'mesinList',
                'operatorList',
                'biayaDesain'
            ));
        } catch (\Exception $e) {
            Log::error('Error pada halaman detail pesanan: ' . $e->getMessage());
            return redirect()->route('admin.pesanan.index')
                ->with('error', 'Terjadi kesalahan saat memuat detail pesanan');
        }
    }

    /**
     * Update resi pesanan (text) dan bukti pengiriman (image) ke tabel `pesanans`
     */
    public function updateResiDanBukti(Request $request, $id)
    {
        $request->validate([
            'resi_pesanan' => 'nullable|string|max:255',
            'bukti_pengiriman' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        try {
            $pesanan = Pesanan::findOrFail($id);
            $pesanan->resi_pesanan = $request->resi_pesanan;

            if ($request->hasFile('bukti_pengiriman')) {
                if ($pesanan->bukti_pengiriman && Storage::disk('public')->exists($pesanan->bukti_pengiriman)) {
                    Storage::disk('public')->delete($pesanan->bukti_pengiriman);
                }

                $path = $request->file('bukti_pengiriman')->store('bukti_pengiriman', 'public');
                $pesanan->bukti_pengiriman = $path;
            }

            $pesanan->save();

            return redirect()->back()->with('success', 'Resi dan bukti pengiriman berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Gagal update resi/bukti: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    public function uploadBukti(Request $request, $id)
    {
        $request->validate([
            'bukti_pengiriman' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        try {
            $pesanan = Pesanan::findOrFail($id);

            // Hapus gambar lama jika ada
            if ($pesanan->bukti_pengiriman && Storage::disk('public')->exists($pesanan->bukti_pengiriman)) {
                Storage::disk('public')->delete($pesanan->bukti_pengiriman);
            }

            $path = $request->file('bukti_pengiriman')->store('bukti_pengiriman', 'public');
            $pesanan->bukti_pengiriman = $path;
            $pesanan->save();

            return redirect()->back()->with('success', 'Bukti pengiriman berhasil diupload.');
        } catch (\Exception $e) {
            Log::error('Gagal upload bukti pengiriman: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengupload bukti pengiriman.');
        }
    }

    public function uploadResi(Request $request, $id)
    {
        $request->validate([
            'resi_pesanan' => 'required|string|max:255',
        ]);

        try {
            $pesanan = Pesanan::findOrFail($id);
            $pesanan->resi_pesanan = $request->input('resi_pesanan');
            $pesanan->save();

            return redirect()->back()->with('success', 'Resi berhasil diunggah.');
        } catch (\Exception $e) {
            Log::error('Gagal upload resi: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengunggah resi.');
        }
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|string'
            ]);

            $pesanan = Pesanan::findOrFail($id);
            $pesanan->status = $request->status;
            $pesanan->save();

            return redirect()->back()->with('success', 'Status pesanan berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Gagal update status: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui status.');
        }
    }

    public function assignProduction(Request $request, $id)
    {
        try {
            $request->validate([
                'detail_pesanan_id' => 'required',
                'mesin_id' => 'required',
                'operator_id' => 'required',
                'catatan' => 'nullable|string'
            ]);

            $response = $this->sendApiRequest('post', "/admin/pesanan/{$id}/assign-production", $request->all());

            if ($response['success'] ?? false) {
                return redirect()->back()->with('success', 'Proses produksi berhasil ditugaskan');
            }

            return redirect()->back()->with('error', $response['message'] ?? 'Gagal menugaskan proses produksi');
        } catch (\Exception $e) {
            Log::error('Error pada assign produksi: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menugaskan proses produksi');
        }
    }

    public function completeProduction(Request $request, $id)
    {
        try {
            $request->validate([
                'proses_pesanan_id' => 'required',
                'catatan' => 'nullable|string'
            ]);

            $response = $this->sendApiRequest('post', "/admin/pesanan/{$id}/complete-production", $request->all());

            if ($response['success'] ?? false) {
                return redirect()->back()->with('success', 'Proses produksi berhasil diselesaikan');
            }

            return redirect()->back()->with('error', $response['message'] ?? 'Gagal menyelesaikan proses produksi');
        } catch (\Exception $e) {
            Log::error('Error pada complete produksi: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyelesaikan proses produksi');
        }
    }

    public function confirmShipment(Request $request, $id)
    {
        try {
            $request->validate([
                'ekspedisi_id' => 'required',
                'nomor_resi' => 'nullable|string|max:100',
                'catatan' => 'nullable|string'
            ]);

            $response = $this->sendApiRequest('post', "/admin/pesanan/{$id}/confirm-shipment", $request->all());

            if ($response['success'] ?? false) {
                return redirect()->back()->with('success', 'Pengiriman pesanan berhasil dikonfirmasi');
            }

            return redirect()->back()->with('error', $response['message'] ?? 'Gagal mengkonfirmasi pengiriman');
        } catch (\Exception $e) {
            Log::error('Error pada konfirmasi pengiriman: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengkonfirmasi pengiriman');
        }
    }

    public function confirmPickup($id)
    {
        try {
            $response = $this->sendApiRequest('post', "/admin/pesanan/{$id}/confirm-pickup");

            if ($response['success'] ?? false) {
                return redirect()->back()->with('success', 'Pengambilan pesanan berhasil dikonfirmasi');
            }

            return redirect()->back()->with('error', $response['message'] ?? 'Gagal mengkonfirmasi pengambilan');
        } catch (\Exception $e) {
            Log::error('Error pada konfirmasi pengambilan: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengkonfirmasi pengambilan');
        }
    }

    public function uploadDesain(Request $request, $id)
    {
        try {
            $request->validate([
                'detail_pesanan_id' => 'required',
                'desain' => 'required|file|mimes:jpeg,png,jpg,pdf,ai,psd|max:10240',
                'tipe' => 'required|in:desain_toko,revisi'
            ]);

            $token = session('api_token');

            $response = Http::withToken($token)
                ->timeout(30)
                ->attach(
                    'desain',
                    file_get_contents($request->file('desain')->getRealPath()),
                    $request->file('desain')->getClientOriginalName()
                )
                ->post($this->apiBaseUrl . "/admin/pesanan/{$id}/upload-desain", [
                    'detail_pesanan_id' => $request->detail_pesanan_id,
                    'tipe' => $request->tipe
                ]);

            $responseData = $response->json();

            if ($responseData['success'] ?? false) {
                return redirect()->back()->with('success', 'Desain berhasil diupload');
            }

            return redirect()->back()->with('error', $responseData['message'] ?? 'Gagal mengupload desain');
        } catch (\Exception $e) {
            Log::error('Error pada upload desain: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengupload desain');
        }
    }

    public function cancelOrder(Request $request, $id)
    {
        try {
            $request->validate([
                'alasan_batal' => 'nullable|string'
            ]);

            $response = $this->sendApiRequest('post', "/admin/pesanan/{$id}/cancel", $request->all());

            if ($response['success'] ?? false) {
                return redirect()->route('admin.pesanan.index')->with('success', 'Pesanan berhasil dibatalkan');
            }

            return redirect()->back()->with('error', $response['message'] ?? 'Gagal membatalkan pesanan');
        } catch (\Exception $e) {
            Log::error('Error pada pembatalan pesanan: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat membatalkan pesanan');
        }
    }

    protected function sendApiRequest($method, $endpoint, $data = [])
    {
        try {
            $token = session('api_token');

            Log::debug('Mengirim permintaan API', [
                'method' => $method,
                'endpoint' => $endpoint,
                'has_token' => !empty($token)
            ]);

            $response = Http::withToken($token)
                ->withHeaders(['Accept' => 'application/json'])
                ->$method($this->apiBaseUrl . $endpoint, $data);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Error mengirim permintaan API: ' . $e->getMessage(), [
                'method' => $method,
                'endpoint' => $endpoint
            ]);

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan komunikasi dengan server'
            ];
        }
    }
}
