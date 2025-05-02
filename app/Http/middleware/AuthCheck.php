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
            'has_user' => session()->has('user'),
            'uri' => $request->getRequestUri()
        ]);

        // Check if user is logged in
        if (!session()->has('api_token') || !session()->has('user')) {
            Log::warning('AuthCheck: No API token or user in session');
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu');
        }

        // Check if token has expired
        if (session()->has('expires_at')) {
            $expiresAt = session('expires_at');
            
            try {
                // Convert to DateTime if it's not already
                if (is_string($expiresAt)) {
                    $expiresAt = new \DateTime($expiresAt);
                }
                
                if (now()->gt($expiresAt)) {
                    Log::warning('AuthCheck: Token expired');
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

        return $next($request);
    }
}