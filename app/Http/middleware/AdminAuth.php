<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Log middleware execution for debugging
        Log::info('AdminAuth middleware check', [
            'has_api_token' => session()->has('api_token'),
            'has_user' => session()->has('user'),
            'user_data' => session('user'),
            'uri' => $request->getRequestUri()
        ]);

        // Check if user is logged in
        if (!session()->has('api_token') || !session()->has('user')) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }

        // Check if token has expired
        if (session()->has('expires_at') && now()->gt(session('expires_at'))) {
            session()->flush();
            return redirect()->route('login')
                ->with('error', 'Sesi telah berakhir, silakan login kembali');
        }

        // Check if user is admin
        $user = session('user');
        if (!isset($user['role']) || ($user['role'] !== 'admin' && $user['role'] !== 'super_admin')) {
            Log::warning('Access denied: User does not have admin role', [
                'user_role' => $user['role'] ?? 'undefined',
                'uri' => $request->getRequestUri()
            ]);
            return redirect()->route('login')->with('error', 'Anda tidak memiliki akses ke halaman ini');
        }

        return $next($request);
    }
}