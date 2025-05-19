<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard
     */
    public function dashboard(Request $request)
    {
        // Cek akses terlebih dahulu
        if (!session()->has('api_token') || !session()->has('user')) {
            Log::warning('Akses Admin Dashboard ditolak: Token tidak ada');
            return redirect()->route('login')->with('error', 'Sesi Anda telah berakhir. Silakan login kembali.');
        }
        
        // Cek peran pengguna
        $user = session('user');
        if (!isset($user['role']) || ($user['role'] !== 'admin' && $user['role'] !== 'super_admin')) {
            Log::warning('Akses Admin Dashboard ditolak: Bukan admin', [
                'role' => $user['role'] ?? 'tidak diketahui'
            ]);
            return redirect()->route('login')->with('error', 'Anda tidak memiliki akses ke halaman ini');
        }

        Log::info('Admin dashboard diakses', [
            'user' => session('user')
        ]);
        
        return view('admin.dashboard', compact('user'));
    }
    
    // Method-method lain tetap sama
}