<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    protected $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = rtrim(env('API_URL', config('app.url')), '/') . '/superadmin';
    }
    
    /**
     * Display admin list
     */
    public function index(Request $request)
    {
        $params = [
            'search' => $request->get('search', ''),
            'page' => $request->get('page', 1),
            'per_page' => 10
        ];
        
        $response = $this->sendApiRequest('get', '/admins', $params);
        
        if (!($response['success'] ?? false)) {
            return view('superadmin.admin.index')->with('error', $response['message'] ?? 'Failed to fetch admins');
        }
        
        return view('superadmin.admin.index', [
            'admins' => $response['admins'] ?? [],
            'pagination' => $response['pagination'] ?? null,
            'search' => $params['search']
        ]);
    }
    
    /**
     * Show admin creation form
     */
    public function create()
    {
        return view('superadmin.admin.create');
    }
    
    /**
     * Store new admin
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|min:8'
        ]);
        
        $response = $this->sendApiRequest('post', '/admins', $request->all());
        
        if (!($response['success'] ?? false)) {
            return redirect()->back()
                ->withInput($request->except('password'))
                ->with('error', $response['message'] ?? 'Failed to create admin');
        }
        
        return redirect()->route('superadmin.admin.index')
            ->with('success', 'Admin created successfully');
    }
    
    /**
     * Show single admin
     */
    public function show($id)
    {
        $response = $this->sendApiRequest('get', "/admins/{$id}");
        
        if (!($response['success'] ?? false)) {
            return redirect()->route('superadmin.admin.index')
                ->with('error', $response['message'] ?? 'Admin not found');
        }
        
        return view('superadmin.admin.show', [
            'admin' => $response['admin'] ?? null
        ]);
    }
    
    /**
     * Show admin edit form
     */
    public function edit($id)
    {
        $response = $this->sendApiRequest('get', "/admins/{$id}");
        
        if (!($response['success'] ?? false)) {
            return redirect()->route('superadmin.admin.index')
                ->with('error', $response['message'] ?? 'Admin not found');
        }
        
        return view('superadmin.admin.edit', [
            'admin' => $response['admin'] ?? null
        ]);
    }
    
    /**
     * Update existing admin
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'nullable|min:8'
        ]);
        
        $response = $this->sendApiRequest('put', "/admins/{$id}", $request->all());
        
        if (!($response['success'] ?? false)) {
            return redirect()->back()
                ->withInput($request->except('password'))
                ->with('error', $response['message'] ?? 'Failed to update admin');
        }
        
        return redirect()->route('superadmin.admin.index')
            ->with('success', 'Admin updated successfully');
    }
    
    /**
     * Delete admin
     */
    public function destroy($id)
    {
        $response = $this->sendApiRequest('delete', "/admins/{$id}");
        
        if (!($response['success'] ?? false)) {
            return redirect()->back()
                ->with('error', $response['message'] ?? 'Failed to delete admin');
        }
        
        return redirect()->route('superadmin.admin.index')
            ->with('success', 'Admin deleted successfully');
    }
    
    /**
     * Reset admin password
     */
    public function resetPassword($id)
    {
        $response = $this->sendApiRequest('post', "/admins/{$id}/reset-password");
        
        if (!($response['success'] ?? false)) {
            return redirect()->back()
                ->with('error', $response['message'] ?? 'Failed to reset password');
        }
        
        return redirect()->back()
            ->with('success', 'Password reset successfully')
            ->with('new_password', $response['new_password'] ?? null);
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