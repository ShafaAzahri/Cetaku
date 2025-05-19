<?php

namespace App\Http\Controllers\API\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Operator;
use App\Models\ProsesPesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class OperatorManagementApiController extends Controller
{
    /**
     * Get list of all operators
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $query = Operator::query();
            
            // Filter by status if provided
            if ($request->has('status') && in_array($request->status, ['aktif', 'tidak_aktif'])) {
                $query->where('status', $request->status);
            }
            
            // Filter by position if provided
            if ($request->has('posisi') && !empty($request->posisi)) {
                $query->where('posisi', 'like', "%{$request->posisi}%");
            }
            
            // Search by name
            if ($request->has('search') && !empty($request->search)) {
                $query->where('nama', 'like', "%{$request->search}%");
            }
            
            // Order by
            $sortField = $request->get('sort_field', 'created_at');
            $sortDirection = $request->get('sort_direction', 'desc');
            $query->orderBy($sortField, $sortDirection);
            
            // Pagination
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 10);
            
            // Get paginated results
            $paginator = $query->paginate($perPage, ['*'], 'page', $page);
            
            // Get current assignments for each operator
            $operators = $paginator->items();
            foreach ($operators as $operator) {
                $currentAssignment = ProsesPesanan::with(['detailPesanan.custom.item', 'mesin'])
                    ->where('operator_id', $operator->id)
                    ->whereNull('waktu_selesai')
                    ->where('status_proses', '!=', 'Selesai')
                    ->orderBy('waktu_mulai', 'desc')
                    ->first();
                
                $operator->current_assignment = $currentAssignment;
            }
            
            // Format response
            return response()->json([
                'success' => true,
                'operators' => $operators,
                'pagination' => [
                    'current_page' => $paginator->currentPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                    'total_pages' => $paginator->lastPage()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching operators: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching operators',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Store a newly created operator
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'nama' => 'required|string|max:255',
                'posisi' => 'required|string|max:100',
                'kontak' => 'required|string|max:50',
                'status' => 'required|in:aktif,tidak_aktif'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            // Create new operator
            $operator = new Operator();
            $operator->nama = $request->nama;
            $operator->posisi = $request->posisi;
            $operator->kontak = $request->kontak;
            $operator->status = $request->status;
            $operator->save();
            
            // Log the action
            Log::info('New operator created', [
                'operator_id' => $operator->id,
                'created_by' => auth()->user()->id ?? 'Unknown'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Operator berhasil ditambahkan',
                'operator' => $operator
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating operator: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error creating operator',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Display the specified operator
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $operator = Operator::findOrFail($id);
            
            // Get current assignment
            $currentAssignment = ProsesPesanan::with(['detailPesanan.custom.item', 'detailPesanan.pesanan', 'mesin'])
                ->where('operator_id', $id)
                ->whereNull('waktu_selesai')
                ->where('status_proses', '!=', 'Selesai')
                ->orderBy('waktu_mulai', 'desc')
                ->first();
            
            $operator->current_assignment = $currentAssignment;
            
            // Get completed assignments count
            $completedCount = ProsesPesanan::where('operator_id', $id)
                ->where('status_proses', 'Selesai')
                ->count();
            
            return response()->json([
                'success' => true,
                'operator' => $operator,
                'stats' => [
                    'completed_assignments' => $completedCount
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching operator: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Operator not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }
    
    /**
     * Update the specified operator
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            // Find operator
            $operator = Operator::findOrFail($id);
            
            // Validate request
            $validator = Validator::make($request->all(), [
                'nama' => 'required|string|max:255',
                'posisi' => 'required|string|max:100',
                'kontak' => 'required|string|max:50',
                'status' => 'required|in:aktif,tidak_aktif'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            // If trying to set status to tidak_aktif, check if operator is currently assigned
            if ($request->status == 'tidak_aktif' && $operator->status == 'aktif') {
                $currentAssignment = ProsesPesanan::where('operator_id', $id)
                    ->whereNull('waktu_selesai')
                    ->where('status_proses', '!=', 'Selesai')
                    ->first();
                
                if ($currentAssignment) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Operator tidak dapat dinonaktifkan karena sedang bekerja pada pesanan'
                    ], 400);
                }
            }
            
            // Update operator
            $operator->nama = $request->nama;
            $operator->posisi = $request->posisi;
            $operator->kontak = $request->kontak;
            $operator->status = $request->status;
            $operator->save();
            
            // Log the action
            Log::info('Operator updated', [
                'operator_id' => $operator->id,
                'updated_by' => auth()->user()->id ?? 'Unknown'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Operator berhasil diperbarui',
                'operator' => $operator
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating operator: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating operator',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Remove the specified operator
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            // Find operator
            $operator = Operator::findOrFail($id);
            
            // Check if operator has current or historical assignments
            $assignmentCount = ProsesPesanan::where('operator_id', $id)->count();
            
            if ($assignmentCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Operator tidak dapat dihapus karena memiliki riwayat tugas'
                ], 400);
            }
            
            // Store operator info for logging
            $operatorInfo = [
                'id' => $operator->id,
                'nama' => $operator->nama,
                'posisi' => $operator->posisi
            ];
            
            // Use transaction to ensure data integrity
            DB::beginTransaction();
            
            // Delete operator
            $operator->delete();
            
            DB::commit();
            
            // Log the action
            Log::info('Operator deleted', [
                'operator_info' => $operatorInfo,
                'deleted_by' => auth()->user()->id ?? 'Unknown'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Operator berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting operator: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting operator',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get the operator's work history
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function workHistory(Request $request, $id)
    {
        try {
            // Find operator
            $operator = Operator::findOrFail($id);
            
            // Query for completed assignments
            $query = ProsesPesanan::with(['detailPesanan.custom.item', 'mesin'])
                ->where('operator_id', $id)
                ->where('status_proses', 'Selesai')
                ->whereNotNull('waktu_selesai');
            
            // Filter by date range if provided
            if ($request->has('start_date') && !empty($request->start_date)) {
                $query->whereDate('waktu_mulai', '>=', $request->start_date);
            }
            
            if ($request->has('end_date') && !empty($request->end_date)) {
                $query->whereDate('waktu_selesai', '<=', $request->end_date);
            }
            
            // Order by
            $sortField = $request->get('sort_field', 'waktu_selesai');
            $sortDirection = $request->get('sort_direction', 'desc');
            $query->orderBy($sortField, $sortDirection);
            
            // Pagination
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 10);
            
            // Get paginated results
            $paginator = $query->paginate($perPage, ['*'], 'page', $page);
            
            // Calculate work durations
            $history = $paginator->items();
            foreach ($history as $assignment) {
                if ($assignment->waktu_mulai && $assignment->waktu_selesai) {
                    $start = new \DateTime($assignment->waktu_mulai);
                    $end = new \DateTime($assignment->waktu_selesai);
                    $interval = $start->diff($end);
                    
                    $assignment->durasi_pengerjaan = '';
                    
                    if ($interval->d > 0) {
                        $assignment->durasi_pengerjaan .= $interval->d . ' hari ';
                    }
                    
                    if ($interval->h > 0) {
                        $assignment->durasi_pengerjaan .= $interval->h . ' jam ';
                    }
                    
                    if ($interval->i > 0) {
                        $assignment->durasi_pengerjaan .= $interval->i . ' menit';
                    }
                    
                    $assignment->durasi_pengerjaan = trim($assignment->durasi_pengerjaan);
                }
            }
            
            // Get summary statistics
            $totalCompleted = ProsesPesanan::where('operator_id', $id)
                ->where('status_proses', 'Selesai')
                ->count();
            
            $thisMonth = ProsesPesanan::where('operator_id', $id)
                ->where('status_proses', 'Selesai')
                ->whereMonth('waktu_selesai', now()->month)
                ->whereYear('waktu_selesai', now()->year)
                ->count();
            
            $thisWeek = ProsesPesanan::where('operator_id', $id)
                ->where('status_proses', 'Selesai')
                ->whereBetween('waktu_selesai', [now()->startOfWeek(), now()->endOfWeek()])
                ->count();
            
            return response()->json([
                'success' => true,
                'operator' => [
                    'id' => $operator->id,
                    'nama' => $operator->nama,
                    'posisi' => $operator->posisi
                ],
                'work_history' => $history,
                'pagination' => [
                    'current_page' => $paginator->currentPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                    'total_pages' => $paginator->lastPage()
                ],
                'summary' => [
                    'total_completed' => $totalCompleted,
                    'this_month' => $thisMonth,
                    'this_week' => $thisWeek
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching operator work history: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching operator work history',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Update operator status
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:aktif,tidak_aktif'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            // Find operator
            $operator = Operator::findOrFail($id);
            
            // If trying to set status to tidak_aktif, check if operator is currently assigned
            if ($request->status == 'tidak_aktif' && $operator->status == 'aktif') {
                $currentAssignment = ProsesPesanan::where('operator_id', $id)
                    ->whereNull('waktu_selesai')
                    ->where('status_proses', '!=', 'Selesai')
                    ->first();
                
                if ($currentAssignment) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Operator tidak dapat dinonaktifkan karena sedang bekerja pada pesanan'
                    ], 400);
                }
            }
            
            // Update status
            $operator->status = $request->status;
            $operator->save();
            
            // Log the action
            Log::info('Operator status updated', [
                'operator_id' => $operator->id,
                'old_status' => $operator->getOriginal('status'),
                'new_status' => $request->status,
                'updated_by' => auth()->user()->id ?? 'Unknown'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Status operator berhasil diperbarui',
                'operator' => $operator
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating operator status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating operator status',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}