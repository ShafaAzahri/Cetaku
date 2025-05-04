<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WelcomeController extends Controller
{
    /**
     * Menampilkan halaman welcome (landing page)
     */
    public function index()
    {
        // Log informasi untuk debugging
        Log::info('Welcome page diakses', [
            'has_session' => session()->has('api_token'),
            'user' => session('user') ?? 'not logged in'
        ]);
        
        // Cek apakah pengguna sudah login
        if (session()->has('api_token') && session()->has('user')) {
            $user = session('user');
            $role = $user['role'] ?? 'user';
            
            // Redirect ke halaman yang sesuai kecuali untuk role user
            if ($role === 'admin') {
                Log::info('Admin terdeteksi, redirect ke admin dashboard');
                return redirect()->route('admin.dashboard');
            } elseif ($role === 'super_admin') {
                Log::info('Super Admin terdeteksi, redirect ke superadmin dashboard');
                return redirect()->route('superadmin.dashboard');
            }
            
            // Role user tetap di halaman welcome
            Log::info('Role user terdeteksi, tetap di halaman welcome');
        }
        
        // Tampilkan view welcome
        return view('welcome');
    }
}