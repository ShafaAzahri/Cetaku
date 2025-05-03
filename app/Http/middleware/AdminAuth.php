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
        // Debug
        Log::info('AdminAuth middleware running', [
            'path' => $request->path(),
            'has_token' => session()->has('api_token'),
            'user' => session('user')
        ]);
        // Check if user is logged in
        if (!session()->has('api_token') || !session()->has('user')) {
            Log::warning('AdminAuth: No API token or user in session');
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }

        // Check if token has expired
        if (session()->has('expires_at')) {
            try {
                $expiresAt = session('expires_at');
                
                // Convert to DateTime if it's not already
                if (is_string($expiresAt)) {
                    $expiresAt = new \DateTime($expiresAt);
                }
                
                if (now()->gt($expiresAt)) {
                    Log::warning('AdminAuth: Token expired');
                    session()->flush();
                    return redirect()->route('login')
                        ->with('error', 'Sesi telah berakhir, silakan login kembali');
                }
            } catch (\Exception $e) {
                Log::error('Error checking token expiry: ' . $e->getMessage());
                session()->flush();
                return redirect()->route('login')
                    ->with('error', 'Terjadi kesalahan. Silakan login kembali');
            }
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