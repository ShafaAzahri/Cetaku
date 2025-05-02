<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthCheck
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Log middleware execution for debugging
        Log::info('AuthCheck middleware running', [
            'has_api_token' => session()->has('api_token'),
            'uri' => $request->getRequestUri()
        ]);

        if (!session()->has('api_token')) {
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu');
        }

        // Check if token has expired
        if (session()->has('expires_at') && now()->gt(session('expires_at'))) {
            session()->flush();
            return redirect()->route('login')
                ->with('error', 'Sesi telah berakhir, silakan login kembali');
        }

        return $next($request);
    }
}