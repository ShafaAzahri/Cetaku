<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class WelcomeController extends Controller
{
    /**
     * Menampilkan halaman welcome (landing page)
     */
    public function index()
{
    // Cek apakah pengguna sudah login
    if (session()->has('api_token') && session()->has('user')) {
        $user = session('user');
        $role = $user['role'] ?? 'user';
        
        // Redirect admin dan super_admin ke dashboard mereka
        if ($role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($role === 'super_admin') {
            return redirect()->route('superadmin.dashboard');
        }
        
        // Untuk user biasa, tampilkan welcome page dengan data user
        return view('welcome', ['user' => $user]);
    }
    
    // Pengguna belum login
    return view('welcome');
}
}