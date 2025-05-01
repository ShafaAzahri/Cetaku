<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Bahan;
use App\Models\ItemBahan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BahanController extends Controller
{
    /**
     * Display a listing of the materials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = Bahan::query();
        
        // Search by name
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('nama_bahan', 'LIKE', "%{$search}%");
        }
        
        // Sort
        $sortField = $request->input('sort_by', 'id');
        $sortDirection = $request->input('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);
        
        // Pagination
        $perPage = $request->input('per_page', 10);
        $bahans = $query->paginate($perPage);
        
        // Load associated items
        $bahans->each(function ($bahan) {
            $bahan->load('items');
        });
        
        return response()->json([
            'success' => true,
            'data' => $bahans
        ]);
    }

    /**
     * Store a newly created material.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_bahan' => 'required|string|max:255',
            'biaya_tambahan' => 'required|numeric|min:0',
            'item_id' => 'required|exists:items,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $bahan = Bahan::create([
            'nama_bahan' => $request->nama_bahan,
            'biaya_tambahan' => $request->biaya_tambahan,
            'is_available' => $request->has('is_available') ? $request->is_available : true,
        ]);

        // Create association with item
        ItemBahan::create([
            'item_id' => $request->item_id,
            'bahan_id' => $bahan->id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Material created successfully',
            'data' => $bahan->load('items')
        ], 201);
    }

    /**
     * Display the specified material.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $bahan = Bahan::with('items')->find($id);
        
        if (!$bahan) {
            return response()->json([
                'success' => false,
                'message' => 'Material not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $bahan
        ]);
    }

    /**
     * Update the specified material.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $bahan = Bahan::find($id);
        
        if (!$bahan) {
            return response()->json([
                'success' => false,
                'message' => 'Material not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama_bahan' => 'required|string|max:255',
            'biaya_tambahan' => 'required|numeric|min:0',
            'item_id' => 'required|exists:items,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $bahan->nama_bahan = $request->nama_bahan;
        $bahan->biaya_tambahan = $request->biaya_tambahan;
        
        if ($request->has('is_available')) {
            $bahan->is_available = $request->is_available;
        }
        
        $bahan->save();

        // Update item association
        // First remove all existing associations
        ItemBahan::where('bahan_id', $bahan->id)->delete();
        
        // Create new association
        ItemBahan::create([
            'item_id' => $request->item_id,
            'bahan_id' => $bahan->id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Material updated successfully',
            'data' => $bahan->load('items')
        ]);
    }

    /**
     * Remove the specified material.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $bahan = Bahan::find($id);
        
        if (!$bahan) {
            return response()->json([
                'success' => false,
                'message' => 'Material not found'
            ], 404);
        }

        // Delete associations in pivot table
        ItemBahan::where('bahan_id', $id)->delete();

        $bahan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Material deleted successfully'
        ]);
    }

    /**
     * Get all materials (no pagination, for dropdowns)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll()
    {
        $bahans = Bahan::orderBy('nama_bahan')->get();
        
        return response()->json([
            'success' => true,
            'data' => $bahans
        ]);
    }
    
    /**
     * Get materials by item ID
     * 
     * @param int $itemId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByItem($itemId)
    {
        $bahans = Bahan::whereHas('items', function ($query) use ($itemId) {
            $query->where('items.id', $itemId);
        })->get();
        
        return response()->json([
            'success' => true,
            'data' => $bahans
        ]);
    }
}