<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\BiayaDesain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BiayaDesainController extends Controller
{
    /**
 * Display a listing of the design costs.
 *
 * @return \Illuminate\Http\JsonResponse
 */
public function index(Request $request)
    {
        try {
            $query = BiayaDesain::query();
            
            // Search by description
            if ($request->has('search')) {
                $search = $request->input('search');
                $query->where('deskripsi', 'LIKE', "%{$search}%");
            }
            
            // Sort
            $sortField = $request->input('sort_by', 'id');
            $sortDirection = $request->input('sort_direction', 'desc');
            $query->orderBy($sortField, $sortDirection);
            
            // Pagination
            $perPage = $request->input('per_page', 10);
            $biayaDesains = $query->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'data' => $biayaDesains
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching design costs: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created design cost.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'biaya' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $biayaDesain = BiayaDesain::create([
            'biaya' => $request->biaya,
            'deskripsi' => $request->deskripsi,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Design cost created successfully',
            'data' => $biayaDesain
        ], 201);
    }

    /**
     * Display the specified design cost.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $biayaDesain = BiayaDesain::find($id);
        
        if (!$biayaDesain) {
            return response()->json([
                'success' => false,
                'message' => 'Design cost not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $biayaDesain
        ]);
    }

    /**
     * Update the specified design cost.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $biayaDesain = BiayaDesain::find($id);
        
        if (!$biayaDesain) {
            return response()->json([
                'success' => false,
                'message' => 'Design cost not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'biaya' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $biayaDesain->biaya = $request->biaya;
        $biayaDesain->deskripsi = $request->deskripsi;
        $biayaDesain->save();

        return response()->json([
            'success' => true,
            'message' => 'Design cost updated successfully',
            'data' => $biayaDesain
        ]);
    }

    /**
     * Remove the specified design cost.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $biayaDesain = BiayaDesain::find($id);
        
        if (!$biayaDesain) {
            return response()->json([
                'success' => false,
                'message' => 'Design cost not found'
            ], 404);
        }

        $biayaDesain->delete();

        return response()->json([
            'success' => true,
            'message' => 'Design cost deleted successfully'
        ]);
    }

    /**
     * Get all design costs (no pagination, for dropdowns)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll()
    {
        $biayaDesains = BiayaDesain::orderBy('biaya')->get();
        
        return response()->json([
            'success' => true,
            'data' => $biayaDesains
        ]);
    }
}