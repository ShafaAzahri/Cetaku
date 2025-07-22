<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthCheck
{
    public function handle(Request $request, Closure $next)
    {
        Log::info('AuthCheck middleware running', [
            'has_api_token' => session()->has('api_token'),
            'has_user' => session()->has('user'),
            'uri' => $request->getRequestUri()
        ]);

        if (!session()->has('api_token') || !session()->has('user')) {
            Log::warning('AuthCheck: No API token or user in session');
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu');
        }

        if (session()->has('expires_at')) {
            $expiresAt = session('expires_at');

            try {
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

        // ⬇ Tambahan penting: Set Auth user supaya bisa dipakai dengan auth()->user()
        if (session()->has('user')) {
            $userArray = session('user');
            if (isset($userArray['id'])) {
                $user = User::find($userArray['id']);
                if ($user) {
                    Auth::setUser($user);
                }
            }
        }

        return $next($request);
    }
}