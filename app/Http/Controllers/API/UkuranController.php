<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Ukuran;
use App\Models\ItemUkuran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class UkuranController extends Controller
{
    /**
     * Display a listing of the sizes with optimized query.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            // Start with a base query
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
            
            // Use a more efficient eager loading approach with select
            $ukurans = $query->with(['items' => function($q) {
                $q->select('items.id', 'nama_item');
            }])
            ->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'data' => $ukurans
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching sizes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created size.
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

        // Use DB transaction for data integrity
        return DB::transaction(function() use ($request) {
            $ukuran = Ukuran::create([
                'size' => $request->size,
                'faktor_harga' => $request->faktor_harga,
            ]);
    
            // Create association with item
            ItemUkuran::create([
                'item_id' => $request->item_id,
                'ukuran_id' => $ukuran->id
            ]);
    
            // Clear cache
            Cache::forget('ukurans_all');
            
            return response()->json([
                'success' => true,
                'message' => 'Size created successfully',
                'data' => $ukuran->load('items')
            ], 201);
        });
    }

    // Other methods...

    /**
     * Get all sizes (no pagination, for dropdowns) - with caching
     */
    public function getAll()
    {
        // This is a perfect candidate for caching
        $cacheKey = 'ukurans_all';
        $ukurans = Cache::remember($cacheKey, 3600, function() {
            return Ukuran::orderBy('size')->get();
        });
        
        return response()->json([
            'success' => true,
            'data' => $ukurans
        ]);
    }
    
    /**
     * Get sizes by item ID (optimized)
     */
    public function getByItem($itemId)
    {
        // More efficient query with joins
        $ukurans = Ukuran::select('ukurans.*')
            ->join('item_ukurans', 'ukurans.id', '=', 'item_ukurans.ukuran_id')
            ->where('item_ukurans.item_id', $itemId)
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $ukurans
        ]);
    }
}