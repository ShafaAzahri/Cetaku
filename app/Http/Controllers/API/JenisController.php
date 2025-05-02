<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Jenis;
use App\Models\ItemJenis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JenisController extends Controller
{
    /**
 * Display a listing of the categories.
 *
 * @return \Illuminate\Http\JsonResponse
 */
public function index(Request $request)
    {
        try {
            $query = Jenis::query();
            
            // Search by category
            if ($request->has('search')) {
                $search = $request->input('search');
                $query->where('kategori', 'LIKE', "%{$search}%");
            }
            
            // Sort
            $sortField = $request->input('sort_by', 'id');
            $sortDirection = $request->input('sort_direction', 'desc');
            $query->orderBy($sortField, $sortDirection);
            
            // Pagination
            $perPage = $request->input('per_page', 10);
            $jenis = $query->paginate($perPage);
            
            // Load associated items
            $jenis->each(function ($jenisItem) {
                $jenisItem->load('items');
            });
            
            return response()->json([
                'success' => true,
                'data' => $jenis
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching categories: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created category.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kategori' => 'required|string|max:255',
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

        $jenis = Jenis::create([
            'kategori' => $request->kategori,
            'biaya_tambahan' => $request->biaya_tambahan,
        ]);

        // Create association with item
        ItemJenis::create([
            'item_id' => $request->item_id,
            'jenis_id' => $jenis->id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully',
            'data' => $jenis->load('items')
        ], 201);
    }

    /**
     * Display the specified category.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $jenis = Jenis::with('items')->find($id);
        
        if (!$jenis) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $jenis
        ]);
    }

    /**
     * Update the specified category.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $jenis = Jenis::find($id);
        
        if (!$jenis) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'kategori' => 'required|string|max:255',
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

        $jenis->kategori = $request->kategori;
        $jenis->biaya_tambahan = $request->biaya_tambahan;
        $jenis->save();

        // Update item association
        // First remove all existing associations
        ItemJenis::where('jenis_id', $jenis->id)->delete();
        
        // Create new association
        ItemJenis::create([
            'item_id' => $request->item_id,
            'jenis_id' => $jenis->id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully',
            'data' => $jenis->load('items')
        ]);
    }

    /**
     * Remove the specified category.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $jenis = Jenis::find($id);
        
        if (!$jenis) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }

        // Delete associations in pivot table
        ItemJenis::where('jenis_id', $id)->delete();

        $jenis->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully'
        ]);
    }

    /**
     * Get all categories (no pagination, for dropdowns)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll()
    {
        $jenis = Jenis::orderBy('kategori')->get();
        
        return response()->json([
            'success' => true,
            'data' => $jenis
        ]);
    }
    
    /**
     * Get categories by item ID
     * 
     * @param int $itemId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByItem($itemId)
    {
        $jenis = Jenis::whereHas('items', function ($query) use ($itemId) {
            $query->where('items.id', $itemId);
        })->get();
        
        return response()->json([
            'success' => true,
            'data' => $jenis
        ]);
    }
}