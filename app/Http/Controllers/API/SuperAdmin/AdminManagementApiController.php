<?php

namespace App\Http\Controllers\API\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AdminManagementApiController extends Controller
{
    /**
     * Get list of all admins
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            // Get admin role ID
            $adminRole = Role::where('nama_role', 'admin')->first();
            
            if (!$adminRole) {
                return response()->json([
                    'success' => false,
                    'message' => 'Admin role not found'
                ], 404);
            }
            
            // Query for admins with search functionality
            $query = User::with('role')
                ->where('role_id', $adminRole->id);
            
            // Search by name or email
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
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
            
            // Format response with custom pagination structure
            return response()->json([
                'success' => true,
                'admins' => $paginator->items(),
                'pagination' => [
                    'current_page' => $paginator->currentPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                    'total_pages' => $paginator->lastPage()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching admins: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching admins',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Store a newly created admin
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
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            // Get admin role
            $adminRole = Role::where('nama_role', 'admin')->first();
            
            if (!$adminRole) {
                return response()->json([
                    'success' => false,
                    'message' => 'Admin role not found'
                ], 404);
            }
            
            // Create new admin
            $admin = new User();
            $admin->nama = $request->nama;
            $admin->email = $request->email;
            $admin->password = Hash::make($request->password);
            $admin->role_id = $adminRole->id;
            $admin->save();
            
            // Log the action
            Log::info('New admin created', [
                'admin_id' => $admin->id,
                'created_by' => auth()->user()->id ?? 'Unknown'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Admin berhasil ditambahkan',
                'admin' => $admin
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating admin: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error creating admin',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Display the specified admin
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $admin = User::with('role')->findOrFail($id);
            
            // Check if user is admin
            if ($admin->role->nama_role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'User is not an admin'
                ], 400);
            }
            
            return response()->json([
                'success' => true,
                'admin' => $admin
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching admin: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Admin not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }
    
    /**
     * Update the specified admin
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            // Find admin
            $admin = User::findOrFail($id);
            
            // Check if user is admin
            $adminRole = Role::where('nama_role', 'admin')->first();
            if ($admin->role_id != $adminRole->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is not an admin'
                ], 400);
            }
            
            // Validate request
            $validator = Validator::make($request->all(), [
                'nama' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $admin->id,
                'password' => 'nullable|string|min:8',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            // Update admin
            $admin->nama = $request->nama;
            $admin->email = $request->email;
            
            // Update password if provided
            if ($request->filled('password')) {
                $admin->password = Hash::make($request->password);
            }
            
            $admin->save();
            
            // Log the action
            Log::info('Admin updated', [
                'admin_id' => $admin->id,
                'updated_by' => auth()->user()->id ?? 'Unknown'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Admin berhasil diperbarui',
                'admin' => $admin
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating admin: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating admin',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Remove the specified admin
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            // Find admin
            $admin = User::findOrFail($id);
            
            // Check if user is admin
            $adminRole = Role::where('nama_role', 'admin')->first();
            if ($admin->role_id != $adminRole->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is not an admin'
                ], 400);
            }
            
            // Store admin info for logging
            $adminInfo = [
                'id' => $admin->id,
                'email' => $admin->email,
                'nama' => $admin->nama
            ];
            
            // Delete admin
            $admin->delete();
            
            // Log the action
            Log::info('Admin deleted', [
                'admin_info' => $adminInfo,
                'deleted_by' => auth()->user()->id ?? 'Unknown'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Admin berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting admin: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting admin',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Reset admin password
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword($id)
    {
        try {
            // Find admin
            $admin = User::findOrFail($id);
            
            // Check if user is admin
            $adminRole = Role::where('nama_role', 'admin')->first();
            if ($admin->role_id != $adminRole->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is not an admin'
                ], 400);
            }
            
            // Generate random password
            $newPassword = Str::random(10);
            
            // Update password
            $admin->password = Hash::make($newPassword);
            $admin->save();
            
            // Log the action
            Log::info('Admin password reset', [
                'admin_id' => $admin->id,
                'reset_by' => auth()->user()->id ?? 'Unknown'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Password admin berhasil direset',
                'new_password' => $newPassword // Only returned once
            ]);
        } catch (\Exception $e) {
            Log::error('Error resetting admin password: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error resetting admin password',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}