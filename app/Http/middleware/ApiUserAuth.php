<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class ApiUserAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Ambil token dari header Authorization
        $token = $request->bearerToken();
        
        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - Token tidak ditemukan'
            ], 401);
        }
        
        // Cari user berdasarkan token
        $user = User::where('api_token', $token)->first();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - Token tidak valid'
            ], 401);
        }
        
        // Cek apakah token sudah expired
        if (now()->gt($user->token_expires_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - Token expired'
            ], 401);
        }
        
        // Simpan user ke request untuk digunakan di controller jika perlu
        $request->merge(['authenticated_user' => $user]);
        
        return $next($request);
    }
}