<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    protected $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = rtrim(env('API_URL', config('app.url')), '/') . '/superadmin';
    }
    
    /**
     * Display dashboard
     */
    public function index()
    {
        $response = $this->sendApiRequest('get', '/dashboard/stats');
        
        if (!($response['success'] ?? false)) {
            return view('superadmin.dashboard')->with('error', $response['message'] ?? 'Failed to fetch dashboard data');
        }
        
        return view('superadmin.dashboard', [
            'stats' => $response['stats'] ?? null,
            'recent_activities' => $response['recent_activities'] ?? []
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