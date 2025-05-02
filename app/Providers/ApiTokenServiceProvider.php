<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
            try {
                $token = $request->header('Authorization');
                
                if (str_starts_with($token, 'Bearer ')) {
                    $token = substr($token, 7);
                }
                
                if (!$token) {
                    Log::debug('API Token Auth: No token provided');
                    return null;
                }
                
                $user = User::where('api_token', $token)->first();
                
                if (!$user) {
                    Log::debug('API Token Auth: User not found with token');
                    return null;
                }
                
                if (empty($user->token_expires_at) || now()->gt($user->token_expires_at)) {
                    Log::debug('API Token Auth: Token expired', [
                        'user_id' => $user->id,
                        'token_expires_at' => $user->token_expires_at,
                        'now' => now()
                    ]);
                    return null;
                }
                
                Log::debug('API Token Auth: Valid token', [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);
                
                return $user;
            } catch (\Exception $e) {
                Log::error('API Token Auth error: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString()
                ]);
                return null;
            }
        });
    }
}