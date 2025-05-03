<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ItemViewController extends Controller
{
    /**
     * Cache TTL in seconds (15 minutes)
     */
    protected $cacheTTL = 900;
    
    /**
     * Base API URL
     */
    protected $baseUrl;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->baseUrl = config('app.url');
        Log::debug('ItemViewController initialized with base URL', ['url' => $this->baseUrl]);
    }
    
    /**
     * Get paginated items dari API
     */
    public function getItems(Request $request)
    {
        try {
            Log::debug('getItems called with request', [
                'page' => $request->input('page', 1),
                'per_page' => $request->input('per_page', 10),
                'search' => $request->input('search', '')
            ]);
            
            $token = session('api_token');
            if (!$token) {
                Log::error('Token tidak ditemukan di session');
                return [
                    'success' => false,
                    'message' => 'Token tidak ditemukan',
                    'data' => []
                ];
            }
            
            // Log token untuk debugging (hanya panjangnya untuk keamanan)
            Log::debug('Using token for API request', ['token_length' => strlen($token)]);
            
            // Buat cache key yang unik
            $page = $request->input('page', 1);
            $perPage = $request->input('per_page', 10);
            $search = $request->input('search', '');
            $sortBy = $request->input('sort_by', 'id');
            $sortDirection = $request->input('sort_direction', 'desc');
            
            $cacheKey = "view_items_page{$page}_perPage{$perPage}_search" . md5($search) . "_sort_{$sortBy}_{$sortDirection}";
            Log::debug('Cache key generated', ['key' => $cacheKey]);
            
            // Force skip cache for debugging if needed
            $skipCache = $request->input('skip_cache', false);
            if ($skipCache) {
                Log::debug('Skipping cache as requested');
                Cache::forget($cacheKey);
            }
            
            $result = Cache::remember($cacheKey, $this->cacheTTL, function() use ($token, $page, $perPage, $search, $sortBy, $sortDirection) {
                // Buat query string
                $queryParams = [
                    'page' => $page,
                    'per_page' => $perPage,
                    'sort_by' => $sortBy,
                    'sort_direction' => $sortDirection
                ];
                
                if (!empty($search)) {
                    $queryParams['search'] = $search;
                }
                
                $fullUrl = $this->baseUrl . '/api/admin/items?' . http_build_query($queryParams);
                Log::debug('Requesting API', ['url' => $fullUrl]);
                
                // Increase timeout to 60 seconds
                $response = Http::timeout(60)->withToken($token)->get($fullUrl);
                
                Log::debug('API Response received', [
                    'status' => $response->status(),
                    'body_length' => strlen($response->body()),
                    'sample' => substr($response->body(), 0, 100) . '...' // Sample of response for debugging
                ]);
                
                if ($response->successful()) {
                    $jsonResponse = $response->json();
                    Log::debug('API Request successful', [
                        'success' => $jsonResponse['success'] ?? 'undefined',
                        'total_records' => $jsonResponse['data']['total'] ?? 'undefined',
                        'record_count' => isset($jsonResponse['data']['data']) ? count($jsonResponse['data']['data']) : 'undefined'
                    ]);
                    return $jsonResponse;
                } else {
                    Log::error('Error fetching items from API: ' . $response->body(), [
                        'status' => $response->status(),
                        'url' => $fullUrl
                    ]);
                    return [
                        'success' => false,
                        'message' => 'Gagal mengambil data dari API: ' . $response->status(),
                        'data' => []
                    ];
                }
            });
            
            Log::debug('getItems returning result', [
                'success' => $result['success'] ?? false,
                'data_type' => isset($result['data']) ? gettype($result['data']) : 'undefined',
                'data_count' => isset($result['data']['data']) ? count($result['data']['data']) : 0
            ]);
            
            return $result;
            
        } catch (\Exception $e) {
            Log::error('Exception in ItemViewController@getItems: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data produk: ' . $e->getMessage(),
                'data' => []
            ];
        }
    }
    
    /**
     * Get single item from API
     */
    public function getItem($id)
    {
        try {
            Log::debug('getItem called with ID: ' . $id);
            
            $token = session('api_token');
            if (!$token) {
                Log::error('Token tidak ditemukan di session');
                return [
                    'success' => false,
                    'message' => 'Token tidak ditemukan',
                    'data' => null
                ];
            }
            
            $cacheKey = "view_item_{$id}";
            Log::debug('Cache key for item', ['key' => $cacheKey]);
            
            $result = Cache::remember($cacheKey, $this->cacheTTL, function() use ($token, $id) {
                Log::debug('Making API request for single item', ['id' => $id]);
                $response = Http::timeout(60)->withToken($token)
                    ->get($this->baseUrl . '/api/admin/items/' . $id);
                
                Log::debug('API Response for item received', [
                    'status' => $response->status(),
                    'body_length' => strlen($response->body())
                ]);
                
                if ($response->successful()) {
                    return $response->json();
                } else {
                    Log::error('Error fetching item from API: ' . $response->body());
                    return [
                        'success' => false,
                        'message' => 'Gagal mengambil data produk',
                        'data' => null
                    ];
                }
            });
            
            return $result;
            
        } catch (\Exception $e) {
            Log::error('Exception in ItemViewController@getItem: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data produk: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }
    
    /**
     * Get items dropdown (for select options) dari API
     */
    public function getItemsDropdown()
    {
        try {
            Log::debug('getItemsDropdown called');
            
            $token = session('api_token');
            if (!$token) {
                Log::error('Token tidak ditemukan di session');
                return [
                    'success' => false,
                    'message' => 'Token tidak ditemukan',
                    'data' => []
                ];
            }
            
            $cacheKey = "view_items_dropdown";
            Log::debug('Cache key for dropdown', ['key' => $cacheKey]);
            
            $result = Cache::remember($cacheKey, $this->cacheTTL, function() use ($token) {
                Log::debug('Making API request for items dropdown');
                $response = Http::timeout(60)->withToken($token)
                    ->get($this->baseUrl . '/api/admin/items/all');
                
                Log::debug('API Response for dropdown received', [
                    'status' => $response->status(),
                    'body_length' => strlen($response->body())
                ]);
                
                if ($response->successful()) {
                    $jsonResponse = $response->json();
                    Log::debug('Dropdown items count', ['count' => isset($jsonResponse['data']) ? count($jsonResponse['data']) : 0]);
                    return $jsonResponse;
                } else {
                    Log::error('Error fetching items dropdown from API: ' . $response->body());
                    return [
                        'success' => false,
                        'message' => 'Gagal mengambil data dropdown produk',
                        'data' => []
                    ];
                }
            });
            
            return $result;
            
        } catch (\Exception $e) {
            Log::error('Exception in ItemViewController@getItemsDropdown: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data dropdown produk: ' . $e->getMessage(),
                'data' => []
            ];
        }
    }
    
    /**
     * Clear item cache
     */
    public function clearCache()
    {
        try {
            Log::info('Clearing item cache');
            
            $patterns = [
                'view_items_*',
                'view_item_*',
                'view_items_dropdown'
            ];
            
            foreach ($patterns as $pattern) {
                $this->clearCacheByPattern($pattern);
            }
            
            return [
                'success' => true,
                'message' => 'Cache item berhasil dibersihkan'
            ];
        } catch (\Exception $e) {
            Log::error('Exception in ItemViewController@clearCache: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat membersihkan cache: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Clear cache berdasarkan pattern
     */
    protected function clearCacheByPattern($pattern)
    {
        Log::debug('Clearing cache by pattern', ['pattern' => $pattern]);
        
        if (config('cache.default') === 'file') {
            $cachePath = storage_path('framework/cache/data');
            $files = glob($cachePath . '/*');
            $count = 0;
            
            foreach ($files as $file) {
                $cacheKey = basename($file);
                if (strpos($file, $pattern) !== false) {
                    @unlink($file);
                    $count++;
                }
            }
            
            Log::debug('Cleared file cache', ['count' => $count, 'pattern' => $pattern]);
        } else {
            // Untuk driver cache selain file
            Cache::flush();
            Log::debug('Flushed entire cache (non-file driver)');
        }
    }
}