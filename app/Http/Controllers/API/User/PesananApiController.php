<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pesanan;
use Illuminate\Support\Facades\Auth;

class PesananApiController extends Controller
{
    /**
     * Ambil daftar pesanan berdasarkan status
     */
public function index(Request $request)
{
    $user = $request->get('authenticated_user');
    $status = $request->query('status');

    $query = \App\Models\Pesanan::with([
        'detailPesanans.custom.item',
        'detailPesanans.custom.bahan',
        'detailPesanans.custom.jenis',
        'detailPesanans.custom.ukuran',
    ])->where('user_id', $user->id);

    if ($status && $status !== 'Semua') {
        $query->where('status', $status);
    }

    $pesanan = $query->latest()->get();

    return response()->json([
        'status' => 'success',  
        'data' => $pesanan
    ]);
}
}

