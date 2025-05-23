<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class UserController extends Controller
{
    protected $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = rtrim(env('API_URL', config('app.url')), '/') . '/superadmin';
    }
    
    /**
     * Display user list
     */
    public function index(Request $request)
    {
        $params = [
            'search' => $request->get('search', ''),
            'page' => $request->get('page', 1),
            'per_page' => 10
        ];
        
        $response = $this->sendApiRequest('get', '/users', $params);
        
        if (!($response['success'] ?? false)) {
            return view('superadmin.user.index')->with('error', $response['message'] ?? 'Failed to fetch users');
        }
        
        return view('superadmin.user.index', [
            'users' => $response['users'] ?? [],
            'pagination' => $response['pagination'] ?? null,
            'search' => $params['search']
        ]);
    }

    public function create()
    {
        return view('superadmin.user.create');
    }

    // Menyimpan user baru
    public function store(Request $request)
    {
        // Validasi data
        $validatedData = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|in:user,admin',
        ]);

        // Simpan user ke database
        $user = User::create([
            'nama' => $validatedData['nama'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password']),
            'role' => $validatedData['role'],
        ]);

        return redirect()->route('superadmin.user.index')->with('success', 'User berhasil ditambahkan.');
    }
    
    /**
     * Show single user
     */
    public function show($id)
    {
        $response = $this->sendApiRequest('get', "/users/{$id}");
        
        if (!($response['success'] ?? false)) {
            return redirect()->route('superadmin.user.index')
                ->with('error', $response['message'] ?? 'User not found');
        }
        
        return view('superadmin.user.show', [
            'user' => $response['user'] ?? null
        ]);
    }
    
    /**
     * Show user edit form
     */
    public function edit($id)
    {
        $response = $this->sendApiRequest('get', "/users/{$id}");
        
        if (!($response['success'] ?? false)) {
            return redirect()->route('superadmin.user.index')
                ->with('error', $response['message'] ?? 'User not found');
        }
        
        return view('superadmin.user.edit', [
            'user' => $response['user'] ?? null
        ]);
    }
    
    /**
     * Update existing user
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'nullable|min:8'
        ]);
        
        $response = $this->sendApiRequest('put', "/users/{$id}", $request->all());
        
        if (!($response['success'] ?? false)) {
            return redirect()->back()
                ->withInput($request->except('password'))
                ->with('error', $response['message'] ?? 'Failed to update user');
        }
        
        return redirect()->route('superadmin.user.index')
            ->with('success', 'User updated successfully');
    }
    
    /**
     * Delete user
     */
    public function destroy($id)
    {
        $response = $this->sendApiRequest('delete', "/users/{$id}");
        
        if (!($response['success'] ?? false)) {
            return redirect()->back()
                ->with('error', $response['message'] ?? 'Failed to delete user');
        }
        
        return redirect()->route('superadmin.user.index')
            ->with('success', 'User deleted successfully');
    }
    
    /**
     * Reset user password
     */
    public function resetPassword($id)
    {
        $response = $this->sendApiRequest('post', "/users/{$id}/reset-password");
        
        if (!($response['success'] ?? false)) {
            return redirect()->back()
                ->with('error', $response['message'] ?? 'Failed to reset password');
        }
        
        return redirect()->back()
            ->with('success', 'Password reset successfully')
            ->with('new_password', $response['new_password'] ?? null);
    }
    
    /**
     * View user order history
     */
    public function orderHistory($id)
    {
        $response = $this->sendApiRequest('get', "/users/{$id}/order-history");
        
        if (!($response['success'] ?? false)) {
            return redirect()->back()
                ->with('error', $response['message'] ?? 'Failed to fetch order history');
        }
        
        return view('superadmin.user.order-history', [
            'user_id' => $response['user_id'] ?? $id,
            'user_name' => $response['user_name'] ?? 'User',
            'orders' => $response['orders'] ?? []
        ]);
    }
    
    /**
     * Helper: Send API request with authentication
     */
    protected function sendApiRequest($method, $endpoint, $data = [])
    {
        try {
            $token = session('api_token');
            
            $response = Http::withToken($token)
                ->accept('application/json')
                ->$method($this->apiBaseUrl . $endpoint, $data);
            
            return $response->json();
        } catch (\Exception $e) {
            Log::error('API request failed: ' . $e->getMessage(), [
                'method' => $method,
                'endpoint' => $endpoint
            ]);
            
            return [
                'success' => false,
                'message' => 'Failed to communicate with server'
            ];
        }
    }
}