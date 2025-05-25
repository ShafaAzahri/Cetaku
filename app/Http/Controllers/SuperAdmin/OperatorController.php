<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OperatorController extends Controller
{
    protected $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = rtrim(env('API_URL', config('app.url')), '/') . '/superadmin';
    }

    /**
     * Display operator list
     */
    public function index(Request $request)
    {
        $params = [
            'search' => $request->get('search', ''),
            'status' => $request->get('status', ''),
            'posisi' => $request->get('posisi', ''),
            'page' => $request->get('page', 1),
            'per_page' => 10
        ];

        $response = $this->sendApiRequest('get', '/operators', $params);

        if (!($response['success'] ?? false)) {
            return view('superadmin.operator.index')->with('error', $response['message'] ?? 'Failed to fetch operators');
        }

        return view('superadmin.operator.index', [
            'operators' => $response['operators'] ?? [],
            'pagination' => $response['pagination'] ?? null,
            'search' => $params['search'],
            'status' => $params['status'],
            'posisi' => $params['posisi']
        ]);
    }

    /**
     * Show operator creation form
     */
    public function create()
    {
        return view('superadmin.operator.create');
    }

    /**
     * Store new operator
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'posisi' => 'required|string|max:100',
            'kontak' => 'required|string|max:50',
            'status' => 'required|in:aktif,tidak_aktif'
        ]);

        $response = $this->sendApiRequest('post', '/operators', $request->all());

        if (!($response['success'] ?? false)) {
            return redirect()->back()
                ->withInput()
                ->with('error', $response['message'] ?? 'Failed to create operator');
        }

        return redirect()->route('superadmin.operator.index')
            ->with('success', 'Operator created successfully');
    }

    /**
     * Show single operator
     */
    public function show($id)
    {
        $response = $this->sendApiRequest('get', "/operators/{$id}");

        if (!($response['success'] ?? false)) {
            return redirect()->route('superadmin.operator.index')
                ->with('error', $response['message'] ?? 'Operator not found');
        }

        return view('superadmin.operator.show', [
            'operator' => $response['operator'] ?? null,
            'stats' => $response['stats'] ?? null
        ]);
    }

    /**
     * Show operator edit form
     */
    public function edit($id)
    {
        // Ambil data operator berdasarkan ID
        $response = $this->sendApiRequest('get', "/operators/{$id}");

        if (!($response['success'] ?? false)) {
            return redirect()->route('superadmin.operator.index')
                ->with('error', $response['message'] ?? 'Operator not found');
        }

        // Pass data operator yang ditemukan ke view edit
        return view('superadmin.operator.edit', [
            'operator' => $response['operator'] ?? null
        ]);
    }


    /**
     * Update existing operator
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'posisi' => 'required|string|max:100',
            'kontak' => 'required|string|max:50',
            'status' => 'required|in:aktif,tidak_aktif'
        ]);

        $response = $this->sendApiRequest('put', "/operators/{$id}", $request->all());

        if (!($response['success'] ?? false)) {
            return redirect()->back()
                ->withInput()
                ->with('error', $response['message'] ?? 'Failed to update operator');
        }

        return redirect()->route('superadmin.operator.index')
            ->with('success', 'Operator updated successfully');
    }

    /**
     * Delete operator
     */
    public function destroy($id)
    {
        $response = $this->sendApiRequest('delete', "/operators/{$id}");

        if (!($response['success'] ?? false)) {
            return redirect()->back()
                ->with('error', $response['message'] ?? 'Failed to delete operator');
        }

        return redirect()->route('superadmin.operator.index')
            ->with('success', 'Operator deleted successfully');
    }

    /**
     * Update operator status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:aktif,tidak_aktif'
        ]);

        $response = $this->sendApiRequest('put', "/operators/{$id}/status", $request->all());

        if (!($response['success'] ?? false)) {
            return redirect()->back()
                ->with('error', $response['message'] ?? 'Failed to update operator status');
        }

        return redirect()->back()
            ->with('success', 'Operator status updated successfully');
    }

    /**
     * View operator work history
     */
    public function workHistory(Request $request, $id)
    {
        $params = [
            'page' => $request->get('page', 1),
            'per_page' => 10,
            'start_date' => $request->get('start_date', ''),
            'end_date' => $request->get('end_date', '')
        ];

        $response = $this->sendApiRequest('get', "/operators/{$id}/work-history", $params);

        if (!($response['success'] ?? false)) {
            return redirect()->back()
                ->with('error', $response['message'] ?? 'Failed to fetch work history');
        }

        return view('superadmin.operator.work-history', [
            'operator' => $response['operator'] ?? null,
            'work_history' => $response['work_history'] ?? [],
            'pagination' => $response['pagination'] ?? null,
            'summary' => $response['summary'] ?? null,
            'start_date' => $params['start_date'],
            'end_date' => $params['end_date']
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
