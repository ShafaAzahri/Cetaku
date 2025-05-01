<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Ukuran;
use App\Models\ItemUkuran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UkuranController extends Controller
{
    /**
     * Display a listing of the sizes.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = Ukuran::query();
        
        // Search by size
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('size', 'LIKE', "%{$search}%");
        }
        
        // Sort
        $sortField = $request->input('sort_by', 'id');
        $sortDirection = $request->input('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);
        
        // Pagination
        $perPage = $request->input('per_page', 10);
        $ukurans = $query->paginate($perPage);
        
        // Load associated items
        $ukurans->each(function ($ukuran) {
            $ukuran->load('items');
        });
        
        return response()->json([
            'success' => true,
            'data' => $ukurans
        ]);
    }

    /**
     * Store a newly created size.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'size' => 'required|string|max:100',
            'faktor_harga' => 'required|numeric|min:0.1',
            'item_id' => 'required|exists:items,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $ukuran = Ukuran::create([
            'size' => $request->size,
            'faktor_harga' => $request->faktor_harga,
        ]);

        // Create association with item
        ItemUkuran::create([
            'item_id' => $request->item_id,
            'ukuran_id' => $ukuran->id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Size created successfully',
            'data' => $ukuran->load('items')
        ], 201);
    }

    /**
     * Display the specified size.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $ukuran = Ukuran::with('items')->find($id);
        
        if (!$ukuran) {
            return response()->json([
                'success' => false,
                'message' => 'Size not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $ukuran
        ]);
    }

    /**
     * Update the specified size.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $ukuran = Ukuran::find($id);
        
        if (!$ukuran) {
            return response()->json([
                'success' => false,
                'message' => 'Size not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'size' => 'required|string|max:100',
            'faktor_harga' => 'required|numeric|min:0.1',
            'item_id' => 'required|exists:items,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $ukuran->size = $request->size;
        $ukuran->faktor_harga = $request->faktor_harga;
        $ukuran->save();

        // Update item association
        // First remove all existing associations
        ItemUkuran::where('ukuran_id', $ukuran->id)->delete();
        
        // Create new association
        ItemUkuran::create([
            'item_id' => $request->item_id,
            'ukuran_id' => $ukuran->id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Size updated successfully',
            'data' => $ukuran->load('items')
        ]);
    }

    /**
     * Remove the specified size.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $ukuran = Ukuran::find($id);
        
        if (!$ukuran) {
            return response()->json([
                'success' => false,
                'message' => 'Size not found'
            ], 404);
        }

        // Delete associations in pivot table
        ItemUkuran::where('ukuran_id', $id)->delete();

        $ukuran->delete();

        return response()->json([
            'success' => true,
            'message' => 'Size deleted successfully'
        ]);
    }

    /**
     * Get all sizes (no pagination, for dropdowns)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll()
    {
        $ukurans = Ukuran::orderBy('size')->get();
        
        return response()->json([
            'success' => true,
            'data' => $ukurans
        ]);
    }
    
    /**
     * Get sizes by item ID
     * 
     * @param int $itemId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByItem($itemId)
    {
        $ukurans = Ukuran::whereHas('items', function ($query) use ($itemId) {
            $query->where('items.id', $itemId);
        })->get();
        
        return response()->json([
            'success' => true,
            'data' => $ukurans
        ]);
    }
}