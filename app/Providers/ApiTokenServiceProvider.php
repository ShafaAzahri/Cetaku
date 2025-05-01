<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ApiTokenServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    // app/Providers/ApiTokenServiceProvider.php
public function boot(): void
{
    Auth::viaRequest('api-token', function (Request $request) {
        // Debug untuk melihat token yang dikirim
        \Log::info('API Token Request', [
            'token' => $request->bearerToken(),
            'headers' => $request->headers->all()
        ]);
        
        $token = $request->bearerToken();
        
        if (!$token) {
            return null;
        }
        
        $user = User::where('api_token', $token)->first();
        
        // Debug untuk melihat user yang ditemukan
        \Log::info('User found with token', [
            'user' => $user ? $user->id : 'null'
        ]);
        
        if (!$user || !$user->isTokenValid()) {
            return null;
        }
        
        return $user;
    });
}
}