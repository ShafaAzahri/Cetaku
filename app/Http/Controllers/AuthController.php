<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
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

        return view('auth.register');
    }

    /**
     * Handle login request
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
            // Call API for login
            $response = Http::post(config('app.url') . '/api/auth/login', [
                'email' => $request->email,
                'password' => $request->password,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['success']) {
                    // Store data in session
                    session([
                        'api_token' => $data['api_token'],
                        'user' => $data['user'],
                        'expires_at' => $data['expires_at'],
                    ]);

                    Log::info('User logged in', [
                        'user_id' => $data['user']['id'] ?? null,
                        'email' => $data['user']['email'] ?? null,
                        'role' => $data['user']['role'] ?? null
                    ]);

                    // Redirect based on role
                    switch ($data['user']['role']) {
                        case 'admin':
                            return redirect()->route('admin.dashboard');
                        case 'super_admin':
                            return redirect()->route('superadmin.dashboard');
                        default:
                            return redirect()->route('user.welcome');
                    }
                }
            }

            Log::warning('Login failed', [
                'email' => $request->email,
                'response' => $response->json()
            ]);

            return redirect()->back()
                ->with('error', 'Email atau password salah')
                ->withInput($request->except('password'));

        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan. Silakan coba lagi.')
                ->withInput($request->except('password'));
        }
    }

    /**
     * Handle registration request
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
            // Call API for register
            $response = Http::post(config('app.url') . '/api/auth/register', [
                'nama' => $request->nama,
                'email' => $request->email,
                'password' => $request->password,
                'password_confirmation' => $request->password_confirmation,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['success']) {
                    // Store data in session
                    session([
                        'api_token' => $data['api_token'],
                        'user' => $data['user'],
                        'expires_at' => $data['expires_at'],
                    ]);

                    Log::info('User registered', [
                        'user_id' => $data['user']['id'] ?? null,
                        'email' => $data['user']['email'] ?? null,
                        'role' => $data['user']['role'] ?? null
                    ]);

                    // Redirect to welcome page
                    return redirect($data['redirect_url'] ?? '/user/welcome');
                }
            }

            Log::warning('Registration failed', [
                'email' => $request->email,
                'response' => $response->json()
            ]);

            return redirect()->back()
                ->withErrors(['error' => 'Gagal mendaftar. Silakan coba lagi.'])
                ->withInput($request->except(['password', 'password_confirmation']));

        } catch (\Exception $e) {
            Log::error('Register error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan. Silakan coba lagi.')
                ->withInput($request->except(['password', 'password_confirmation']));
        }
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        try {
            if (session()->has('api_token')) {
                Http::withToken(session('api_token'))
                    ->post(config('app.url') . '/api/auth/logout');
                
                Log::info('User logged out', [
                    'user' => session('user')
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Logout error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
        }

        // Clear session
        session()->flush();
        
        return redirect()->route('login')
            ->with('success', 'Berhasil logout');
    }
}