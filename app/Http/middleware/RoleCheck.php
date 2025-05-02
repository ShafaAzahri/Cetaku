<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RoleCheck
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Log middleware execution for debugging
        Log::info('RoleCheck middleware running', [
            'has_user' => session()->has('user'),
            'allowed_roles' => $roles,
            'uri' => $request->getRequestUri()
        ]);

        if (!session()->has('user')) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }

        $user = session('user');
        
        // Check if user has role key
        if (!isset($user['role'])) {
            Log::warning('User session missing role key', [
                'user' => $user
            ]);
            return redirect()->route('login')->with('error', 'Sesi tidak valid, silakan login kembali');
        }
        
        // Check if user has required role
        if (!in_array($user['role'], $roles)) {
            Log::warning('Access denied: User does not have required role', [
                'user_role' => $user['role'],
                'required_roles' => $roles
            ]);
            return abort(403, 'Unauthorized access');
        }

        return $next($request);
    }
}