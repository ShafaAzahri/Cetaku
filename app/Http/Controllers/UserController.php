<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Menampilkan halaman welcome untuk user yang sudah login
     */
    public function welcome()
    {
        // Cek akses terlebih dahulu
        if (!session()->has('api_token') || !session()->has('user')) {
            Log::warning('Akses User Welcome ditolak: Token tidak ada');
            return redirect()->route('login')->with('error', 'Sesi Anda telah berakhir. Silakan login kembali.');
        }
        
        // Ambil data user dari session
        $user = session('user');
        
        // Log informasi
        Log::info('User welcome page diakses', [
            'user' => $user
        ]);
        
        // Redirect ke halaman welcome utama
        return redirect()->route('welcome');
    }
}