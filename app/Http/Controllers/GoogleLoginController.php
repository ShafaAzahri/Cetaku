<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleLoginController extends Controller
{
    protected $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = rtrim(env('API_URL', config('app.url')), '/');
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            // Dapatkan user dari Google
            $googleUser = Socialite::driver('google')->stateless()->user();

            Log::info('Google User Data:', [
                'id' => $googleUser->id,
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'avatar' => $googleUser->avatar,
            ]);

            // Kirim data ke API backend khusus endpoint web
            $response = Http::timeout(30)->post($this->apiBaseUrl . '/web-callback', [
                'google_id' => $googleUser->id,
                'nama' => $googleUser->name,
                'email' => $googleUser->email,
                'avatar' => $googleUser->avatar,
            ]);

            Log::info('API Response:', [
                'status' => $response->status(),
                'successful' => $response->successful(),
                'body' => $response->body(),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['success'] ?? false) {
                    session([
                        'api_token' => $data['api_token'],
                        'user' => $data['user'],
                        'expires_at' => $data['expires_at'],
                    ]);

                    return redirect($data['redirect_url'] ?? '/')->with('success', 'Login berhasil!');
                }
                
                return redirect()->route('login')->with('error', $data['message'] ?? 'Gagal login Google');
            }

            $errorData = $response->json();
            Log::error('API Google Login Failed:', [
                'status' => $response->status(),
                'body' => $response->body(),
                'error' => $errorData['message'] ?? 'Unknown error'
            ]);

            return redirect()->route('login')->with('error', 
                $errorData['message'] ?? 'Gagal login Google: HTTP ' . $response->status()
            );

        } catch (\Exception $e) {
            Log::error('Google Login Exception:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('login')->with('error', 'Gagal autentikasi Google: ' . $e->getMessage());
        }
    }
}