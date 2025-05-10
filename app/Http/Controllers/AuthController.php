<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    protected $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = rtrim(env('API_URL', config('app.url')), '/');
    }

    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        if (session()->has('api_token') && isset(session('user')['role'])) {
            return $this->redirectBasedOnRole(session('user'));
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
            $response = Http::post($this->apiBaseUrl . '/login', [
                'email' => $request->email,
                'password' => $request->password,
            ]);
            
            $data = $response->json();
            
            if ($response->successful() && ($data['success'] ?? false)) {
                $this->storeUserSession($data);
                return redirect($data['redirect_url'] ?? $this->getRedirectPathByRole($data['user']['role']));
            }
            
            return redirect()->back()
                ->with('error', $data['message'] ?? 'Authentication failed')
                ->withInput($request->except('password'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Server error. Please try again later.')
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
            $response = Http::post($this->apiBaseUrl . '/register', [
                'nama' => $request->nama,
                'email' => $request->email,
                'password' => $request->password,
                'password_confirmation' => $request->password_confirmation,
            ]);
            
            $data = $response->json();
            
            if ($response->successful() && ($data['success'] ?? false)) {
                $this->storeUserSession($data);
                return redirect($data['redirect_url'] ?? $this->getRedirectPathByRole($data['user']['role']));
            }
            
            return redirect()->back()
                ->with('error', $data['message'] ?? 'Registration failed')
                ->withInput($request->except(['password', 'password_confirmation']));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Server error. Please try again later.')
                ->withInput($request->except(['password', 'password_confirmation']));
        }
    }

    /**
     * Handle logout request
     */
    public function logout()
    {
        $token = session('api_token');
        
        if ($token) {
            Http::withToken($token)->post($this->apiBaseUrl . '/auth/logout');
        }
        
        session()->flush();
        
        return redirect()->route('welcome')
            ->with('success', 'Berhasil logout. Sampai jumpa kembali!');
    }
    
    /**
     * Store user session data
     */
    private function storeUserSession($data)
    {
        session([
            'api_token' => $data['api_token'],
            'user' => $data['user'],
            'expires_at' => $data['expires_at'],
        ]);
    }
    
    /**
     * Get redirect path based on role
     */
    private function getRedirectPathByRole($role)
    {
        $paths = [
            'super_admin' => '/superadmin/dashboard',
            'admin' => '/admin/dashboard',
        ];
        
        return $paths[$role] ?? '/';
    }
    
    /**
     * Redirect based on role
     */
    private function redirectBasedOnRole($user)
    {
        $routes = [
            'super_admin' => 'superadmin.dashboard',
            'admin' => 'admin.dashboard',
        ];
        
        $role = $user['role'] ?? null;
        $route = $routes[$role] ?? 'welcome';
        
        return redirect()->route($route);
    }
}