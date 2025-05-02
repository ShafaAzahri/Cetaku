<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Cek apakah user sudah login
        if (!session()->has('api_token') || !session()->has('user')) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }

        // Cek apakah user adalah admin
        $user = session('user');
        if ($user['role'] !== 'admin' && $user['role'] !== 'super_admin') {
            return redirect()->route('login')->with('error', 'Anda tidak memiliki akses ke halaman ini');
        }

        return $next($request);
    }
}