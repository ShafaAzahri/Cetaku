<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
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
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $credentials = $request->only('email', 'password');
        
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            // Generate a new API token
            $token = Str::random(60);
            $expiresAt = now()->addDays(30);
            
            // Update user with new token using DB facade
            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'api_token' => $token,
                    'token_expires_at' => $expiresAt,
                    'last_login_at' => now(),
                    'last_login_ip' => $request->ip()
                ]);
            
            // Get updated user
            $updatedUser = User::find($user->id);
            
            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $updatedUser->id,
                    'nama' => $updatedUser->nama,
                    'email' => $updatedUser->email,
                    'role' => $updatedUser->role->nama_role ?? null
                ],
                'api_token' => $token,
                'expires_at' => $expiresAt
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Email atau password salah'
        ], 401);
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
        $user = User::find($userId);

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
            'expires_at' => $expiresAt
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
        $user = User::where('api_token', $token)->first();
        
        if (!$user || now()->gt($user->token_expires_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired token'
            ], 401);
        }
        
        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'nama' => $user->nama,
                'email' => $user->email,
                'role' => $user->role->nama_role ?? null
            ]
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
            'message' => 'Logout berhasil'
        ]);
    }
}