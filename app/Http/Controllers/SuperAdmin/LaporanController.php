<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class LaporanController extends Controller
{
    protected $apiBaseUrl;
    // LaporanController.php
    public function __construct()
    {
        $this->apiBaseUrl = rtrim(env('API_URL', config('app.url')), '/') . '/superadmin';
    }

// LaporanController.php

public function index(Request $request)
{
    // Set default start and end date if not provided
    $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());  // Default to the start of the current month
    $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());  // Default to the end of the current month

    // Get sales data with status "Selesai" within the date range
    $response = $this->sendApiRequest('get', '/superadmin/sales', [
        'start_date' => $startDate,
        'end_date' => $endDate,
    ]);

    // Log the response for debugging purposes
    Log::debug('Sales data response:', $response);

    // Handle if the response doesn't contain the expected data
    if (!isset($response['success']) || !$response['success']) {
        return view('superadmin.laporan.index')->with('error', $response['message'] ?? 'Failed to fetch sales data');
    }

    // Calculate the sum of total prices
    $totalPrice = 0;
    foreach ($response['sales_data'] as $sale) {
        $totalPrice += $sale['total_harga'] ?? 0;
    }

    // Pass the sales data and total price to the view
    return view('superadmin.laporan.index', [
        'salesData' => $response['sales_data'], // Assuming the data comes in this key
        'startDate' => $startDate,
        'endDate' => $endDate,
        'totalPrice' => $totalPrice, // Pass total price
    ]);
}



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
