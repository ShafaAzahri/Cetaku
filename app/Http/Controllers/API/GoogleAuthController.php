<?php

// ==============================================
// FILE: app/Http/Controllers/API/GoogleAuthController.php
// ==============================================

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    // Untuk direct callback dari Google (jika diperlukan)
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            return $this->processGoogleUser($googleUser);
        } catch (\Exception $e) {
            Log::error('Google Direct Callback Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal login dengan Google',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Khusus untuk menerima data dari web controller
    public function handleWebCallback(Request $request)
    {
        try {
            // Validasi data yang diterima
            $request->validate([
                'google_id' => 'required|string',
                'nama' => 'required|string',
                'email' => 'required|email',
                'avatar' => 'nullable|string',
            ]);

            // Buat object seperti Google user
            $googleUser = (object) [
                'id' => $request->google_id,
                'name' => $request->nama,
                'email' => $request->email,
                'avatar' => $request->avatar,
            ];

            return $this->processGoogleUser($googleUser);
        } catch (\Exception $e) {
            Log::error('Google Web Callback Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal login dengan Google',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Method untuk memproses user Google
    private function processGoogleUser($googleUser)
    {
        try {
            $user = User::where('google_id', $googleUser->id)->first();

            if (!$user) {
                $existingUser = User::where('email', $googleUser->email)->first();

                if ($existingUser) {
                    // Bind Google ID ke user yang sudah ada
                    $existingUser->update([
                        'google_id' => $googleUser->id,
                        'avatar' => $googleUser->avatar,
                    ]);
                    $user = $existingUser;
                } else {
                    // Dapatkan role user biasa
                    $role = Role::where('nama_role', 'user')->first() ?? Role::create(['nama_role' => 'user']);

                    // Buat user baru
                    $user = User::create([
                        'nama' => $googleUser->name,
                        'email' => $googleUser->email,
                        'google_id' => $googleUser->id,
                        'avatar' => $googleUser->avatar,
                        'email_verified_at' => now(),
                        'role_id' => $role->id,
                    ]);
                }
            }

            // Generate API token
            $token = Str::random(60);
            $expiresAt = now()->addDays(30);

            $user->update([
                'api_token' => $token,
                'token_expires_at' => $expiresAt,
                'last_login_at' => now(),
                'last_login_ip' => request()->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Login dengan Google berhasil',
                'user' => [
                    'id' => $user->id,
                    'nama' => $user->nama,
                    'email' => $user->email,
                    'role' => optional($user->role)->nama_role,
                    'avatar' => $user->avatar,
                ],
                'api_token' => $token,
                'expires_at' => $expiresAt,
                'redirect_url' => $user->getRedirectPath(),
            ]);
        } catch (\Exception $e) {
            Log::error('Process Google User Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses user Google',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}