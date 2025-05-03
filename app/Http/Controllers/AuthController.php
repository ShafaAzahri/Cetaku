<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        // Cek apakah sudah login
        if (session()->has('api_token')) {
            $user = session('user');
            if (isset($user['role'])) {
                switch ($user['role']) {
                    case 'super_admin':
                        return redirect()->route('superadmin.dashboard');
                    case 'admin':
                        return redirect()->route('admin.dashboard');
                    default:
                        return redirect()->route('user.welcome');
                }
            }
        }

        return view('auth.login');
    }

    /**
     * Show the registration form
     */
    public function showRegistrationForm()
    {
        // Cek apakah sudah login
        if (session()->has('api_token')) {
            return $this->redirectBasedOnRole(session('user'));
        }

        return view('auth.register');
    }

    /**
     * Handle login request using API
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password'));
        }

        try {
            // Persiapkan data untuk dikirim ke API
            $apiUrl = config('app.url') . '/api/auth/login';
            $data = [
                'email' => $request->email,
                'password' => $request->password
            ];
            
            // Inisialisasi curl
            $ch = curl_init($apiUrl);
            
            // Siapkan opsi curl
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Accept: application/json',
                'X-CSRF-TOKEN: ' . csrf_token()
            ]);
            
            // Eksekusi permintaan API
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);
            
            // Debug
            Log::debug('API Login Response', [
                'response' => $response,
                'httpCode' => $httpCode,
                'error' => $curlError
            ]);
            
            // Jika permintaan berhasil
            if ($httpCode === 200 && $response) {
                $responseData = json_decode($response, true);
                
                if (isset($responseData['success']) && $responseData['success']) {
                    // Simpan token dan data pengguna dalam sesi
                    session([
                        'api_token' => $responseData['api_token'],
                        'user' => $responseData['user'],
                        'expires_at' => $responseData['expires_at'],
                    ]);
                    
                    Log::info('Pengguna berhasil login melalui API', [
                        'user_id' => $responseData['user']['id'],
                        'email' => $responseData['user']['email']
                    ]);
                    
                    // Redirect berdasarkan peran
                    return redirect($responseData['redirect_url'] ?? $this->getRedirectPathByRole($responseData['user']['role']));
                }
            }
            
            // Jika API gagal, coba fallback
            Log::warning('API login gagal, mencoba metode fallback', [
                'httpCode' => $httpCode,
                'error' => $curlError
            ]);
            
            if ($this->loginFallback($request)) {
                Log::info('Login fallback berhasil');
                return $this->redirectBasedOnRole(session('user'));
            }
            
            // Jika masih gagal juga
            $errorMessage = 'Email atau password salah';
            
            if ($response) {
                $responseData = json_decode($response, true);
                $errorMessage = $responseData['message'] ?? $errorMessage;
            }
            
            return redirect()->back()
                ->with('error', $errorMessage)
                ->withInput($request->except('password'));
                
        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            // Jika error, coba fallback
            if ($this->loginFallback($request)) {
                Log::info('Login fallback berhasil setelah exception');
                return $this->redirectBasedOnRole(session('user'));
            }
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi nanti.')
                ->withInput($request->except('password'));
        }
    }

    /**
     * Handle registration request using API
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except(['password', 'password_confirmation']));
        }

        try {
            // Persiapkan data untuk dikirim ke API
            $apiUrl = config('app.url') . '/api/auth/register';
            $data = [
                'nama' => $request->nama,
                'email' => $request->email,
                'password' => $request->password,
                'password_confirmation' => $request->password_confirmation
            ];
            
            // Inisialisasi curl
            $ch = curl_init($apiUrl);
            
            // Siapkan opsi curl
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Accept: application/json',
                'X-CSRF-TOKEN: ' . csrf_token()
            ]);
            
            // Eksekusi permintaan API
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);
            
            // Debug
            Log::debug('API Register Response', [
                'response' => $response,
                'httpCode' => $httpCode,
                'error' => $curlError
            ]);
            
            // Jika permintaan berhasil
            if (($httpCode === 201 || $httpCode === 200) && $response) {
                $responseData = json_decode($response, true);
                
                if (isset($responseData['success']) && $responseData['success']) {
                    // Simpan token dan data pengguna dalam sesi
                    session([
                        'api_token' => $responseData['api_token'],
                        'user' => $responseData['user'],
                        'expires_at' => $responseData['expires_at'],
                    ]);
                    
                    Log::info('Pengguna berhasil mendaftar melalui API', [
                        'user_id' => $responseData['user']['id'],
                        'email' => $responseData['user']['email']
                    ]);
                    
                    // Redirect berdasarkan peran
                    return redirect($responseData['redirect_url'] ?? $this->getRedirectPathByRole($responseData['user']['role']));
                }
            }
            
            // Jika API gagal, coba fallback
            Log::warning('API register gagal, mencoba metode fallback', [
                'httpCode' => $httpCode,
                'error' => $curlError
            ]);
            
            if ($this->registerFallback($request)) {
                Log::info('Register fallback berhasil');
                return $this->redirectBasedOnRole(session('user'));
            }
            
            // Jika masih gagal juga
            $errorMessage = 'Gagal mendaftar. Silakan coba lagi.';
            
            if ($response) {
                $responseData = json_decode($response, true);
                $errorMessage = $responseData['message'] ?? $errorMessage;
            }
            
            return redirect()->back()
                ->with('error', $errorMessage)
                ->withInput($request->except(['password', 'password_confirmation']));
                
        } catch (\Exception $e) {
            Log::error('Register error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            // Jika error, coba fallback
            if ($this->registerFallback($request)) {
                Log::info('Register fallback berhasil setelah exception');
                return $this->redirectBasedOnRole(session('user'));
            }
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi nanti.')
                ->withInput($request->except(['password', 'password_confirmation']));
        }
    }

    /**
     * Handle logout request using API
     */
    public function logout(Request $request)
    {
        try {
            // Ambil token dari sesi
            $token = session('api_token');
            
            if ($token) {
                // Persiapkan untuk memanggil API logout
                $apiUrl = config('app.url') . '/api/auth/logout';
                
                // Inisialisasi curl
                $ch = curl_init($apiUrl);
                
                // Siapkan opsi curl
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json',
                    'Accept: application/json',
                    'Authorization: Bearer ' . $token,
                    'X-CSRF-TOKEN: ' . csrf_token()
                ]);
                
                // Eksekusi permintaan API
                curl_exec($ch);
                curl_close($ch);
            }
            
            // Hapus sesi terlepas dari respons API
            session()->flush();
            
            return redirect()->route('login')
                ->with('success', 'Berhasil logout');
                
        } catch (\Exception $e) {
            Log::error('Logout error: ' . $e->getMessage());
            
            // Tetap hapus sesi meskipun terjadi kesalahan
            session()->flush();
            
            return redirect()->route('login')
                ->with('success', 'Berhasil logout');
        }
    }
    
    /**
     * Fallback method jika API tidak tersedia atau ada masalah koneksi
     * Ini adalah metode login langsung dari database (tanpa API)
     */
    private function loginFallback(Request $request)
    {
        try {
            $user = User::where('email', $request->email)->first();
            
            if (!$user || !Hash::check($request->password, $user->password)) {
                Log::warning('Login fallback gagal - Kredensial tidak valid', [
                    'email' => $request->email
                ]);
                
                return false;
            }
            
            // Cek peran user
            if (!$user->role) {
                Log::warning('Login fallback gagal - User tidak memiliki peran', [
                    'user_id' => $user->id
                ]);
                
                return false;
            }
            
            // Generate API token baru
            $token = Str::random(60);
            $expiresAt = now()->addDays(30);
            
            $user->api_token = $token;
            $user->token_expires_at = $expiresAt;
            $user->last_login_at = now();
            $user->last_login_ip = $request->ip();
            $user->save();
            
            // Ambil nama peran
            $roleName = $user->role ? $user->role->nama_role : 'user';
            
            // Simpan data sesi
            session([
                'api_token' => $token,
                'user' => [
                    'id' => $user->id,
                    'nama' => $user->nama,
                    'email' => $user->email,
                    'role' => $roleName
                ],
                'expires_at' => $expiresAt,
            ]);
            
            Log::info('User berhasil login menggunakan fallback', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $roleName
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Login fallback error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return false;
        }
    }
    
    /**
     * Fallback method jika API tidak tersedia atau ada masalah koneksi
     * Ini adalah metode register langsung ke database (tanpa API)
     */
    private function registerFallback(Request $request)
    {
        try {
            // Get user role ID (default: user)
            $userRole = Role::where('nama_role', 'user')->first();
            if (!$userRole) {
                // If user role doesn't exist, create it
                $userRole = Role::create(['nama_role' => 'user']);
            }
            
            // Generate token for new user
            $token = Str::random(60);
            $expiresAt = now()->addDays(30);
            
            // Create user
            $user = User::create([
                'nama' => $request->nama,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $userRole->id,
                'api_token' => $token,
                'token_expires_at' => $expiresAt,
            ]);
            
            // Simpan data sesi
            session([
                'api_token' => $token,
                'user' => [
                    'id' => $user->id,
                    'nama' => $user->nama,
                    'email' => $user->email,
                    'role' => 'user'
                ],
                'expires_at' => $expiresAt,
            ]);
            
            Log::info('User berhasil register menggunakan fallback', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Register fallback error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return false;
        }
    }
    
    /**
     * Get redirect path based on role
     */
    private function getRedirectPathByRole($role)
    {
        switch ($role) {
            case 'super_admin':
                return '/superadmin/dashboard';
            case 'admin':
                return '/admin/dashboard';
            default:
                return '/user/welcome';
        }
    }
    
    /**
     * Helper method to redirect based on role
     */
    private function redirectBasedOnRole($user)
    {
        $role = $user['role'] ?? null;
        
        switch ($role) {
            case 'super_admin':
                return redirect()->route('superadmin.dashboard');
            case 'admin':
                return redirect()->route('admin.dashboard');
            default:
                return redirect()->route('user.welcome');
        }
    }
}