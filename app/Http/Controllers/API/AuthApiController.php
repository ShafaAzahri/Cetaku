<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class AuthApiController extends Controller
{
    /**
     * Handle user login via API
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        Log::info('API login attempt', ['email' => $request->email]);
        
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            Log::warning('API login validation failed', ['errors' => $validator->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $credentials = $request->only('email', 'password');
        
        try {
            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                
                Log::info('API login successful for user', [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);
                
                // Generate a new API token
                $token = Str::random(60);
                $expiresAt = now()->addDays(30);
                
                // Update user with new token using DB facade
                $updated = DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'api_token' => $token,
                        'token_expires_at' => $expiresAt,
                        'last_login_at' => now(),
                        'last_login_ip' => $request->ip()
                    ]);
                    
                Log::info('Token update result', ['updated' => $updated]);
                
                // Get updated user
                $updatedUser = User::with('role')->findOrFail($user->id);
                
                // Get user role
                $role = $updatedUser->role;
                $roleName = $role ? $role->nama_role : null;
                
                // Log the role data for debugging
                Log::info('User role data', [
                    'user_id' => $updatedUser->id,
                    'role_id' => $updatedUser->role_id,
                    'role_name' => $roleName
                ]);
                
                // Get the redirect URL based on role
                $redirectUrl = '/user/welcome'; // Default
                
                if ($roleName === 'super_admin') {
                    $redirectUrl = '/superadmin/dashboard';
                } elseif ($roleName === 'admin') {
                    $redirectUrl = '/admin/dashboard';
                }
                
                return response()->json([
                    'success' => true,
                    'user' => [
                        'id' => $updatedUser->id,
                        'nama' => $updatedUser->nama,
                        'email' => $updatedUser->email,
                        'role' => $roleName
                    ],
                    'api_token' => $token,
                    'expires_at' => $expiresAt,
                    'redirect_url' => $redirectUrl
                ]);
            }

            Log::warning('API login authentication failed', ['email' => $request->email]);
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah'
            ], 401);
        } catch (\Exception $e) {
            Log::error('API login exception: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada sistem'
            ], 500);
        }
    }

    /**
     * Handle user registration via API
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Get user role ID
        $userRole = Role::where('nama_role', 'user')->first();
        if (!$userRole) {
            // If user role doesn't exist, create it
            $userRole = Role::create(['nama_role' => 'user']);
        }

        // Generate token for new user
        $token = Str::random(60);
        $expiresAt = now()->addDays(30);

        // Create user
        $userId = DB::table('users')->insertGetId([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $userRole->id,
            'api_token' => $token,
            'token_expires_at' => $expiresAt,
            'created_at' => now()
        ]);
        
        // Get the newly created user
        $user = User::with('role')->find($userId);

        // Get redirect URL for user
        $redirectUrl = '/user/welcome';

        return response()->json([
            'success' => true,
            'message' => 'Pendaftaran berhasil',
            'user' => [
                'id' => $user->id,
                'nama' => $user->nama,
                'email' => $user->email,
                'role' => $userRole->nama_role
            ],
            'api_token' => $token,
            'expires_at' => $expiresAt,
            'redirect_url' => $redirectUrl
        ], 201);
    }

    /**
     * Get authenticated user information by token
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserByToken(Request $request)
    {
        // Get token from Authorization header or query parameter
        $token = $request->bearerToken() ?? $request->query('api_token');
        
        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token not provided'
            ], 401);
        }
        
        // Find user with the token
        $user = User::where('api_token', $token)->with('role')->first();
        
        if (!$user || now()->gt($user->token_expires_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired token'
            ], 401);
        }
        
        // Get role name
        $roleName = $user->role ? $user->role->nama_role : null;
        
        // Get redirect URL based on role
        $redirectUrl = $this->getRedirectUrlByRole($user);
        
        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'nama' => $user->nama,
                'email' => $user->email,
                'role' => $roleName
            ],
            'redirect_url' => $redirectUrl
        ]);
    }

    /**
     * Logout user (revoke token)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        // Get token from Authorization header or query parameter
        $token = $request->bearerToken() ?? $request->query('api_token');
        
        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token not provided'
            ], 401);
        }
        
        // Find user with the token
        $user = User::where('api_token', $token)->first();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid token'
            ], 401);
        }
        
        // Invalidate token using DB facade
        DB::table('users')
            ->where('id', $user->id)
            ->update([
                'api_token' => null,
                'token_expires_at' => null
            ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil',
            'redirect_url' => '/'
        ]);
    }
    
    /**
     * Get redirect URL based on user role
     *
     * @param User $user
     * @return string
     */
    private function getRedirectUrlByRole(User $user)
    {
        $roleName = $user->role ? $user->role->nama_role : null;
        
        if ($roleName === 'super_admin') {
            return '/superadmin/dashboard';
        } elseif ($roleName === 'admin') {
            return '/admin/dashboard';
        } else {
            return '/user/welcome';
        }
    }
}