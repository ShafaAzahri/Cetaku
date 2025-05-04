<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\API\AuthApiController;

class AuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        // Check if already logged in
        if (session()->has('api_token')) {
            $user = session('user');
            if (isset($user['role'])) {
                return $this->redirectBasedOnRole($user);
            }
        }

        return view('auth.login');
    }

    /**
     * Show the registration form
     */
    public function showRegistrationForm()
    {
        // Check if already logged in
        if (session()->has('api_token')) {
            return $this->redirectBasedOnRole(session('user'));
        }

        return view('auth.register');
    }

    /**
     * Handle login request using direct API controller
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
            Log::debug('Attempting to login via API controller', [
                'email' => $request->email
            ]);
            
            // Create instance of API controller and call login method
            $apiController = new AuthApiController();
            $response = $apiController->login($request);
            
            // Get data from JSON response
            $data = $response->getData(true);
            
            if ($data['success'] ?? false) {
                // Store token and user data in session
                session([
                    'api_token' => $data['api_token'],
                    'user' => $data['user'],
                    'expires_at' => $data['expires_at'],
                ]);
                
                Log::info('User successfully logged in via API controller', [
                    'user_id' => $data['user']['id'],
                    'email' => $data['user']['email']
                ]);
                
                // Redirect based on role
                return redirect($data['redirect_url'] ?? $this->getRedirectPathByRole($data['user']['role']));
            } else {
                throw new \Exception('API error: ' . ($data['message'] ?? 'Unknown error'));
            }
        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            // If error, try fallback
            if ($this->loginFallback($request)) {
                Log::info('Login fallback successful after exception');
                return $this->redirectBasedOnRole(session('user'));
            }
            
            return redirect()->back()
                ->with('error', 'Error: ' . $e->getMessage())
                ->withInput($request->except('password'));
        }
    }

    /**
     * Handle registration request using direct API controller
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
            Log::debug('Attempting to register via API controller', [
                'email' => $request->email
            ]);
            
            // Create instance of API controller and call register method
            $apiController = new AuthApiController();
            $response = $apiController->register($request);
            
            // Get data from JSON response
            $data = $response->getData(true);
            
            if (($data['success'] ?? false)) {
                // Store token and user data in session
                session([
                    'api_token' => $data['api_token'],
                    'user' => $data['user'],
                    'expires_at' => $data['expires_at'],
                ]);
                
                Log::info('User successfully registered via API controller', [
                    'user_id' => $data['user']['id'],
                    'email' => $data['user']['email']
                ]);
                
                // Redirect based on role
                return redirect($data['redirect_url'] ?? $this->getRedirectPathByRole($data['user']['role']));
            } else {
                throw new \Exception('API error: ' . ($data['message'] ?? 'Unknown error'));
            }
        } catch (\Exception $e) {
            Log::error('Register error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            // If error, try fallback
            if ($this->registerFallback($request)) {
                Log::info('Register fallback successful after exception');
                return $this->redirectBasedOnRole(session('user'));
            }
            
            return redirect()->back()
                ->with('error', 'Error: ' . $e->getMessage())
                ->withInput($request->except(['password', 'password_confirmation']));
        }
    }

    /**
     * Handle logout request using direct API controller
     */
    /**
     * Handle logout request using direct API controller
     */
    public function logout(Request $request)
    {
        try {
            // Get token from session
            $token = session('api_token');
            
            if ($token) {
                // Setup request with token
                $request->headers->set('Authorization', 'Bearer ' . $token);
                
                // Create instance of API controller and call logout method
                $apiController = new AuthApiController();
                $response = $apiController->logout($request);
            }
            
            // Clear session regardless of API response
            session()->flush();
            
            return redirect()->route('welcome')
                ->with('success', 'Berhasil logout. Sampai jumpa kembali!');
                
        } catch (\Exception $e) {
            Log::error('Logout error: ' . $e->getMessage());
            
            // Still clear session even if there's an error
            session()->flush();
            
            return redirect()->route('welcome')
                ->with('success', 'Berhasil Logout jir');
        }
    }
    
    // Keep fallback methods as they are - they're still useful for backup
    
    /**
     * Fallback method if API is unavailable or connection issues
     * This is direct login from database (without API)
     */
    private function loginFallback(Request $request)
    {
        // Existing implementation
    }
    
    /**
     * Fallback method if API is unavailable or connection issues
     * This is direct registration to database (without API)
     */
    private function registerFallback(Request $request)
    {
        // Existing implementation
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