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
            
            // Log token for debugging (length only for security)
            Log::debug('Using token for API request', ['token_length' => strlen($token)]);
            
            // Create unique cache key
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
                // Build query string
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
                
                try {
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
                } catch (\Exception $e) {
                    Log::error('HTTP request exception: ' . $e->getMessage(), [
                        'url' => $fullUrl,
                        'trace' => $e->getTraceAsString()
                    ]);
                    return [
                        'success' => false,
                        'message' => 'Error koneksi API: ' . $e->getMessage(),
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
                try {
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
                } catch (\Exception $e) {
                    Log::error('HTTP request exception: ' . $e->getMessage(), [
                        'id' => $id,
                        'trace' => $e->getTraceAsString()
                    ]);
                    return [
                        'success' => false,
                        'message' => 'Error koneksi API: ' . $e->getMessage(),
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
                try {
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
                } catch (\Exception $e) {
                    Log::error('HTTP request exception: ' . $e->getMessage(), [
                        'trace' => $e->getTraceAsString()
                    ]);
                    return [
                        'success' => false,
                        'message' => 'Error koneksi API: ' . $e->getMessage(),
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
    public function clearCache(Request $request)
    {
        try {
            Log::info('Clearing item cache');
            
            $patterns = [
                'view_items_*',
                'view_item_*',
                'view_items_dropdown'
            ];
            
            $clearedCount = 0;
            $errors = [];
            
            foreach ($patterns as $pattern) {
                try {
                    $count = $this->clearCacheByPattern($pattern);
                    $clearedCount += $count;
                    Log::info("Cleared cache for pattern: {$pattern}", ['count' => $count]);
                } catch (\Exception $e) {
                    Log::warning("Error clearing cache for pattern: {$pattern}", [
                        'error' => $e->getMessage()
                    ]);
                    $errors[] = $pattern . ': ' . $e->getMessage();
                }
            }
            
            // Always return success to avoid JS errors, but include any error messages
            return response()->json([
                'success' => true,
                'message' => $errors ? 'Cache dibersihkan dengan beberapa kesalahan' : 'Cache item berhasil dibersihkan',
                'count' => $clearedCount,
                'errors' => $errors
            ]);
        } catch (\Exception $e) {
            Log::error('Exception in ItemViewController@clearCache: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return success to avoid JS errors, but include error message
            return response()->json([
                'success' => true, // Important: This ensures JS doesn't throw error
                'message' => 'Terjadi kesalahan saat membersihkan cache: ' . $e->getMessage(),
                'error_details' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Clear cache by pattern - improved implementation
     * 
     * @param string $pattern Cache key pattern
     * @return int Number of cache keys cleared
     */
    protected function clearCacheByPattern($pattern)
    {
        Log::debug('Clearing cache by pattern', ['pattern' => $pattern]);
        $count = 0;
        
        try {
            if (config('cache.default') === 'file') {
                $cachePath = storage_path('framework/cache/data');
                if (!file_exists($cachePath) || !is_dir($cachePath)) {
                    Log::warning('Cache directory does not exist', ['path' => $cachePath]);
                    return 0;
                }
                
                $files = glob($cachePath . '/*');
                if ($files === false) {
                    Log::warning('Failed to read cache directory', ['path' => $cachePath]);
                    return 0;
                }
                
                foreach ($files as $file) {
                    // Skip if not a file
                    if (!is_file($file)) {
                        continue;
                    }
                    
                    // Check if file matches pattern
                    $cacheKey = basename($file);
                    if (strpos($file, $pattern) !== false) {
                        try {
                            // Use error suppression operator to ignore warnings
                            if (@unlink($file)) {
                                $count++;
                            } else {
                                Log::warning('Failed to delete cache file', ['file' => $file]);
                            }
                        } catch (\Exception $e) {
                            Log::warning('Exception deleting cache file: ' . $e->getMessage(), ['file' => $file]);
                        }
                    }
                }
                
                Log::debug('Cleared file cache', ['count' => $count, 'pattern' => $pattern]);
            } else {
                // For other cache drivers (Redis, Memcached, etc.)
                // Laravel doesn't provide a native way to clear by pattern for most drivers
                // So we'll use Cache::flush() which clears all cache
                Cache::flush();
                $count = 1; // Just to indicate something was done
                Log::debug('Flushed entire cache (non-file driver)');
            }
        } catch (\Exception $e) {
            Log::error('Error clearing cache by pattern: ' . $e->getMessage(), [
                'pattern' => $pattern,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e; // Re-throw so the parent method can handle it
        }
        
        return $count;
    }
    
    /**
     * Report cache status
     */
    public function cacheStatus()
    {
        try {
            $cacheDriver = config('cache.default');
            $cacheStorePath = config('cache.stores.file.path');
            
            $stats = [
                'driver' => $cacheDriver,
                'configured_path' => $cacheStorePath,
                'path_exists' => file_exists($cacheStorePath),
                'is_writable' => is_writable($cacheStorePath),
                'items_count' => 0,
                'estimated_size' => '0 KB'
            ];
            
            if ($cacheDriver === 'file' && file_exists($cacheStorePath)) {
                $files = glob($cacheStorePath . '/*');
                $stats['items_count'] = count($files);
                
                $totalSize = 0;
                foreach ($files as $file) {
                    if (is_file($file)) {
                        $totalSize += filesize($file);
                    }
                }
                
                $stats['estimated_size'] = $this->formatBytes($totalSize);
            }
            
            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            Log::error('Error checking cache status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error checking cache status: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2) 
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0); 
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
        $pow = min($pow, count($units) - 1); 
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow]; 
    }
}