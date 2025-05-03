<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ItemBahan;
use App\Models\ItemUkuran;
use App\Models\ItemJenis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ItemController extends Controller
{
    /**
     * Display a listing of the items.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $query = Item::query();
            
            // Search by name
            if ($request->has('search')) {
                $search = $request->input('search');
                $query->where('nama_item', 'LIKE', "%{$search}%");
            }
            
            // Sort
            $sortField = $request->input('sort_by', 'id');
            $sortDirection = $request->input('sort_direction', 'desc');
            $query->orderBy($sortField, $sortDirection);
            
            // Get all items
            $items = $query->get();
            
            Log::debug('API Items fetched', [
                'count' => $items->count()
            ]);
            
            return response()->json([
                'success' => true,
                'data' => $items
            ]);
        } catch (\Exception $e) {
            Log::error('Error in ItemController@index: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error fetching items: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all items (for dropdowns and simple lists)
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll(Request $request)
    {
        try {
            $query = Item::query();
            
            // Search by name
            if ($request->has('search')) {
                $search = $request->input('search');
                $query->where('nama_item', 'LIKE', "%{$search}%");
            }
            
            // Sort by name by default
            $query->orderBy('nama_item', 'asc');
            
            // Get all items
            $items = $query->get();
            
            Log::debug('All items fetched', [
                'count' => $items->count()
            ]);
            
            return response()->json([
                'success' => true,
                'data' => $items
            ]);
        } catch (\Exception $e) {
            Log::error('Error in ItemController@getAll: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error fetching all items: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created item.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_item' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga_dasar' => 'required|numeric|min:0',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $itemData = [
                'nama_item' => $request->nama_item,
                'deskripsi' => $request->deskripsi,
                'harga_dasar' => $request->harga_dasar,
            ];

            // Upload image if provided
            if ($request->hasFile('gambar')) {
                $gambar = $request->file('gambar');
                $path = $gambar->store('product-images', 'public');
                $itemData['gambar'] = $path;
            }

            $item = Item::create($itemData);

            return response()->json([
                'success' => true,
                'message' => 'Item created successfully',
                'data' => $item
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error in ItemController@store: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error creating item: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified item.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $item = Item::with(['bahans', 'ukurans', 'jenis'])->find($id);
            
            if (!$item) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $item
            ]);
        } catch (\Exception $e) {
            Log::error('Error in ItemController@show: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'id' => $id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error fetching item: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified item.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $item = Item::find($id);
            
            if (!$item) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'nama_item' => 'required|string|max:255',
                'deskripsi' => 'nullable|string',
                'harga_dasar' => 'required|numeric|min:0',
                'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $item->nama_item = $request->nama_item;
            $item->deskripsi = $request->deskripsi;
            $item->harga_dasar = $request->harga_dasar;

            // Upload image if provided
            if ($request->hasFile('gambar')) {
                // Delete old image if exists
                if ($item->gambar) {
                    Storage::disk('public')->delete($item->gambar);
                }
                
                $gambar = $request->file('gambar');
                $path = $gambar->store('product-images', 'public');
                $item->gambar = $path;
            }

            $item->save();

            return response()->json([
                'success' => true,
                'message' => 'Item updated successfully',
                'data' => $item
            ]);
        } catch (\Exception $e) {
            Log::error('Error in ItemController@update: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'id' => $id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating item: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified item.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $item = Item::find($id);
            
            if (!$item) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item not found'
                ], 404);
            }

            // Delete related records in pivot tables
            ItemBahan::where('item_id', $id)->delete();
            ItemUkuran::where('item_id', $id)->delete();
            ItemJenis::where('item_id', $id)->delete();

            // Delete image if exists
            if ($item->gambar) {
                Storage::disk('public')->delete($item->gambar);
            }

            $item->delete();

            return response()->json([
                'success' => true,
                'message' => 'Item deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error in ItemController@destroy: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'id' => $id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error deleting item: ' . $e->getMessage()
            ], 500);
        }
    }
}