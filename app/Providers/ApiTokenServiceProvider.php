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
    public function boot(): void
    {
        Auth::viaRequest('api-token', function (Request $request) {
            $token = $request->header('Authorization');
            
            if (str_starts_with($token, 'Bearer ')) {
                $token = substr($token, 7);
            }
            
            if (!$token) {
                return null;
            }
            
            $user = User::where('api_token', $token)->first();
            
            if (!$user || !$user->isTokenValid()) {
                return null;
            }
            
            return $user;
        });
    }
}