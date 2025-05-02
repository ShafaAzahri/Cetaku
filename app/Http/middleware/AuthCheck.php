<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthCheck
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('api_token')) {
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu');
        }

        // Cek jika token expired
        if (session()->has('expires_at') && now()->gt(session('expires_at'))) {
            session()->flush();
            return redirect()->route('login')
                ->with('error', 'Sesi telah berakhir, silakan login kembali');
        }

        return $next($request);
    }
}