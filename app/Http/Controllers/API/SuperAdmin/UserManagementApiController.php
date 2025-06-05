<?php

namespace App\Http\Controllers\API\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserManagementApiController extends Controller
{
    /**
     * Get list of all users
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            // Get user role ID
            $userRole = Role::where('nama_role', 'user')->first();
            
            if (!$userRole) {
                return response()->json([
                    'success' => false,
                    'message' => 'User role not found'
                ], 404);
            }
            
            // Query for users with search functionality
            $query = User::with('role')
                ->where('role_id', $userRole->id);
            
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
            
            // Format response
            return response()->json([
                'success' => true,
                'users' => $paginator->items(),
                'pagination' => [
                    'current_page' => $paginator->currentPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                    'total_pages' => $paginator->lastPage()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching users: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching users',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Display the specified user
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $user = User::with(['role', 'alamats'])->findOrFail($id);
            
            // Check if user is regular user
            if ($user->role->nama_role !== 'user') {
                return response()->json([
                    'success' => false,
                    'message' => 'This endpoint is only for regular users'
                ], 400);
            }
            
            return response()->json([
                'success' => true,
                'user' => $user
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching user: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'User not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }
    
    /**
     * Update the specified user
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */

    public function update(Request $request, $id)
    {
        try {
            // Find user
            $user = User::findOrFail($id);
            
            // Check if user is regular user
            $userRole = Role::where('nama_role', 'user')->first();
            if ($user->role_id != $userRole->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'This endpoint is only for regular users'
                ], 400);
            }
            
            // Validate request
            $validator = Validator::make($request->all(), [
                'nama' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'password' => 'nullable|string|min:8',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            // Update user
            $user->nama = $request->nama;
            $user->email = $request->email;
            
            // Update password if provided
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
            
            $user->save();
            
            // Log the action
            Log::info('User updated by super admin', [
                'user_id' => $user->id,
                'updated_by' => auth()->user()->id ?? 'Unknown'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'User berhasil diperbarui',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating user: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating user',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Remove the specified user
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            // Find user
            $user = User::findOrFail($id);
            
            // Check if user is regular user
            $userRole = Role::where('nama_role', 'user')->first();
            if ($user->role_id != $userRole->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'This endpoint is only for regular users'
                ], 400);
            }
            
            // Check if user has orders
            if ($user->hasOrderHistory()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User has order history and cannot be deleted'
                ], 400);
            }
            
            // Store user info for logging
            $userInfo = [
                'id' => $user->id,
                'email' => $user->email,
                'nama' => $user->nama
            ];
            
            // Delete user's addresses if any
            $user->alamats()->delete();
            
            // Delete user
            $user->delete();
            
            // Log the action
            Log::info('User deleted by super admin', [
                'user_info' => $userInfo,
                'deleted_by' => auth()->user()->id ?? 'Unknown'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'User berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting user: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting user',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Reset user password
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword($id)
    {
        try {
            // Find user
            $user = User::findOrFail($id);
            
            // Check if user is regular user
            $userRole = Role::where('nama_role', 'user')->first();
            if ($user->role_id != $userRole->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'This endpoint is only for regular users'
                ], 400);
            }
            
            // Generate random password
            $newPassword = Str::random(10);
            
            // Update password
            $user->password = Hash::make($newPassword);
            $user->save();
            
            // Log the action
            Log::info('User password reset by super admin', [
                'user_id' => $user->id,
                'reset_by' => auth()->user()->id ?? 'Unknown'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Password user berhasil direset',
                'new_password' => $newPassword // Only returned once
            ]);
        } catch (\Exception $e) {
            Log::error('Error resetting user password: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error resetting user password',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get user order history
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function orderHistory($id)
    {
        try {
            // Find user
            $user = User::findOrFail($id);
            
            // Check if user is regular user
            if ($user->role->nama_role !== 'user') {
                return response()->json([
                    'success' => false,
                    'message' => 'This endpoint is only for regular users'
                ], 400);
            }
            
            // Get user's orders with relationships
            $orders = $user->pesanans()
                ->with(['detailPesanans.custom.item', 'ekspedisi', 'pembayaran'])
                ->orderBy('created_at', 'desc')
                ->get();
            
            return response()->json([
                'success' => true,
                'user_id' => $user->id,
                'user_name' => $user->nama,
                'orders' => $orders
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching user order history: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching user order history',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}