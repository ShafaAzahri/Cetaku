<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Get user role ID
        $userRole = Role::where('nama_role', 'user')->first();
        if (!$userRole) {
            // If user role doesn't exist, create it
            $userRole = Role::create(['nama_role' => 'user']);
        }

        // Create user
        $user = User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $userRole->id,
            'api_token' => Str::random(60),
            'token_expires_at' => now()->addDays(30), // Token expires in 30 days
        ]);

        // Redirect to register page with success message
        return redirect()->back()->with('success', 'Pendaftaran berhasil! Apakah Anda ingin login sekarang?');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $credentials = $request->only('email', 'password');
        
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            // Update user login information
            User::where('id', $user->id)->update([
                'api_token' => Str::random(60),
                'token_expires_at' => now()->addDays(30),
                'last_login_at' => now(),
                'last_login_ip' => $request->ip()
            ]);
            
            // Reload user with updated data
            $user = User::find($user->id);

            // Check role and redirect accordingly
            $userRole = $user->role ? $user->role->nama_role : null;
            if ($userRole === 'admin') {
                return redirect()->route('dashboard');
            } else {
                return redirect()->route('welcome');
            }
        }

        return redirect()->back()
            ->withErrors(['email' => 'Email atau password salah'])
            ->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }

    // API endpoints
    public function apiLogin(Request $request)
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
            
            // Update user login information
            User::where('id', $user->id)->update([
                'api_token' => Str::random(60),
                'token_expires_at' => now()->addDays(30),
                'last_login_at' => now(),
                'last_login_ip' => $request->ip()
            ]);
            
            // Reload user with updated data
            $user = User::find($user->id);
            
            // Check role for redirection
            $userRole = $user->role ? $user->role->nama_role : null;
            $redirectTo = ($userRole === 'admin') ? '/dashboard' : '/';

            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'nama' => $user->nama,
                    'email' => $user->email,
                    'role' => $userRole
                ],
                'api_token' => $user->api_token,
                'expires_at' => $user->token_expires_at,
                'redirect_to' => $redirectTo
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Email atau password salah'
        ], 401);
    }

    public function apiRegister(Request $request)
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

        // Get user role
        $userRole = Role::where('nama_role', 'user')->first();
        if (!$userRole) {
            // If user role doesn't exist, create it
            $userRole = Role::create(['nama_role' => 'user']);
        }

        // Create user
        $user = User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $userRole->id,
            'api_token' => Str::random(60),
            'token_expires_at' => now()->addDays(30), // Token expires in 30 days
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil didaftarkan',
            'user' => [
                'id' => $user->id,
                'nama' => $user->nama,
                'email' => $user->email,
                'role' => $userRole->nama_role
            ],
            'api_token' => $user->api_token,
            'expires_at' => $user->token_expires_at
        ]);
    }
}