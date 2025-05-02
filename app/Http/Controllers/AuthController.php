<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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
            return $this->redirectBasedOnRole(session('user'));
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
            // Attempt direct database login
            $user = User::where('email', $request->email)->first();
            
            if (!$user || !Hash::check($request->password, $user->password)) {
                Log::warning('Login failed - Invalid credentials', [
                    'email' => $request->email
                ]);
                
                return redirect()->back()
                    ->with('error', 'Email atau password salah')
                    ->withInput($request->except('password'));
            }
            
            // Role check before login
            if (!$user->role) {
                Log::warning('Login failed - User has no role', [
                    'user_id' => $user->id
                ]);
                
                return redirect()->back()
                    ->with('error', 'Akun Anda tidak memiliki peran yang valid. Hubungi administrator.')
                    ->withInput($request->except('password'));
            }
            
            // Generate a new API token
            $token = Str::random(60);
            $expiresAt = now()->addDays(30);
            
            $user->api_token = $token;
            $user->token_expires_at = $expiresAt;
            $user->last_login_at = now();
            $user->last_login_ip = $request->ip();
            $user->save();
            
            // Get role information
            $roleName = $user->role ? $user->role->nama_role : 'user';
            
            // Store session data
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
            
            Log::info('User berhasil login', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $roleName
            ]);
            
            // Redirect based on role
            return $this->redirectBasedOnRole(['role' => $roleName]);
            
        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi (' . $e->getMessage() . ')')
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
            // Create user directly
            $userRole = \App\Models\Role::where('nama_role', 'user')->first();
            if (!$userRole) {
                $userRole = \App\Models\Role::create(['nama_role' => 'user']);
            }
            
            $token = Str::random(60);
            $expiresAt = now()->addDays(30);
            
            $user = User::create([
                'nama' => $request->nama,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $userRole->id,
                'api_token' => $token,
                'token_expires_at' => $expiresAt,
            ]);
            
            // Store session data
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
            
            Log::info('User registered successfully', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);
            
            return redirect()->route('user.welcome');
            
        } catch (\Exception $e) {
            Log::error('Register error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan. Silakan coba lagi (' . $e->getMessage() . ')')
                ->withInput($request->except(['password', 'password_confirmation']));
        }
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        try {
            // Directly update database
            if (session()->has('api_token') && session()->has('user')) {
                $userId = session('user')['id'] ?? null;
                if ($userId) {
                    User::where('id', $userId)->update([
                        'api_token' => null,
                        'token_expires_at' => null
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Logout error: ' . $e->getMessage());
        }

        // Clear session
        session()->flush();
        
        return redirect()->route('login')
            ->with('success', 'Berhasil logout');
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