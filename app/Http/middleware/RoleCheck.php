<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleCheck
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!session()->has('user')) {
            return redirect()->route('login');
        }

        $user = session('user');
        
        if (!in_array($user['role'], $roles)) {
            abort(403, 'Unauthorized access');
        }

        return $next($request);
    }
}