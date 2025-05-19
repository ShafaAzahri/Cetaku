<?php

namespace App\Http\Controllers\API\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Pesanan;
use App\Models\Operator;
use App\Models\Mesin;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardApiController extends Controller
{
    /**
     * Get dashboard statistics
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStats(Request $request)
    {
        try {
            // Get counts by role
            $roleStats = $this->getRoleStats();
            
            // Get order statistics
            $orderStats = $this->getOrderStats();
            
            // Get revenue statistics
            $revenueStats = $this->getRevenueStats();
            
            // Get equipment stats
            $equipmentStats = $this->getEquipmentStats();
            
            // Get recent activities (last 10)
            $recentActivities = $this->getRecentActivities();
            
            return response()->json([
                'success' => true,
                'stats' => [
                    'users' => $roleStats,
                    'orders' => $orderStats,
                    'revenue' => $revenueStats,
                    'equipment' => $equipmentStats,
                ],
                'recent_activities' => $recentActivities
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching dashboard statistics: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching dashboard statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get role statistics
     * 
     * @return array
     */
    private function getRoleStats()
    {
        // Get user role
        $userRole = Role::where('nama_role', 'user')->first();
        $adminRole = Role::where('nama_role', 'admin')->first();
        $superAdminRole = Role::where('nama_role', 'super_admin')->first();
        
        // Get counts
        $userCount = 0;
        $adminCount = 0;
        $superAdminCount = 0;
        
        if ($userRole) {
            $userCount = User::where('role_id', $userRole->id)->count();
        }
        
        if ($adminRole) {
            $adminCount = User::where('role_id', $adminRole->id)->count();
        }
        
        if ($superAdminRole) {
            $superAdminCount = User::where('role_id', $superAdminRole->id)->count();
        }
        
        // Get new users in last 30 days
        $newUsers = User::where('created_at', '>=', now()->subDays(30))
            ->where('role_id', $userRole ? $userRole->id : 0)
            ->count();
        
        return [
            'total_users' => $userCount,
            'total_admins' => $adminCount,
            'total_super_admins' => $superAdminCount,
            'new_users_last_30_days' => $newUsers
        ];
    }
    
    /**
     * Get order statistics
     * 
     * @return array
     */
    private function getOrderStats()
    {
        // Get total orders count
        $totalOrders = Pesanan::count();
        
        // Get orders by status
        $ordersByStatus = Pesanan::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status')
            ->toArray();
        
        // Get orders in last 30 days
        $recentOrders = Pesanan::where('created_at', '>=', now()->subDays(30))
            ->count();
        
        // Format order by status with defaults for all possible statuses
        $statusCounts = [
            'Pemesanan' => $ordersByStatus['Pemesanan'] ?? 0,
            'Dikonfirmasi' => $ordersByStatus['Dikonfirmasi'] ?? 0,
            'Sedang Diproses' => $ordersByStatus['Sedang Diproses'] ?? 0,
            'Menunggu Pengambilan' => $ordersByStatus['Menunggu Pengambilan'] ?? 0,
            'Sedang Dikirim' => $ordersByStatus['Sedang Dikirim'] ?? 0,
            'Selesai' => $ordersByStatus['Selesai'] ?? 0,
            'Dibatalkan' => $ordersByStatus['Dibatalkan'] ?? 0
        ];
        
        return [
            'total_orders' => $totalOrders,
            'orders_by_status' => $statusCounts,
            'new_orders_last_30_days' => $recentOrders
        ];
    }
    
    /**
     * Get revenue statistics
     * 
     * @return array
     */
    private function getRevenueStats()
    {
        // Calculate total revenue from completed orders
        $totalRevenue = DB::table('detail_pesanans')
            ->join('pesanans', 'detail_pesanans.pesanan_id', '=', 'pesanans.id')
            ->where('pesanans.status', 'Selesai')
            ->sum('detail_pesanans.total_harga');
        
        // Calculate monthly revenue for the last 12 months
        $monthlyRevenue = DB::table('detail_pesanans')
            ->join('pesanans', 'detail_pesanans.pesanan_id', '=', 'pesanans.id')
            ->where('pesanans.status', 'Selesai')
            ->where('pesanans.created_at', '>=', now()->subMonths(12))
            ->select(
                DB::raw('MONTH(pesanans.created_at) as month'),
                DB::raw('YEAR(pesanans.created_at) as year'),
                DB::raw('SUM(detail_pesanans.total_harga) as total')
            )
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
        
        // Format monthly revenue data
        $revenueByMonth = [];
        $monthNames = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun',
            7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'
        ];
        
        foreach ($monthlyRevenue as $revenue) {
            $monthLabel = $monthNames[$revenue->month] . ' ' . $revenue->year;
            $revenueByMonth[] = [
                'month' => $monthLabel,
                'revenue' => (float) $revenue->total
            ];
        }
        
        // Get current month revenue
        $currentMonthRevenue = DB::table('detail_pesanans')
            ->join('pesanans', 'detail_pesanans.pesanan_id', '=', 'pesanans.id')
            ->where('pesanans.status', 'Selesai')
            ->whereMonth('pesanans.created_at', now()->month)
            ->whereYear('pesanans.created_at', now()->year)
            ->sum('detail_pesanans.total_harga');
        
        return [
            'total_revenue' => $totalRevenue,
            'current_month_revenue' => $currentMonthRevenue,
            'monthly_revenue' => $revenueByMonth
        ];
    }
    
    /**
     * Get equipment statistics
     * 
     * @return array
     */
    private function getEquipmentStats()
    {
        // Get operator stats
        $operatorStats = [
            'total' => Operator::count(),
            'active' => Operator::where('status', 'aktif')->count(),
            'inactive' => Operator::where('status', 'tidak_aktif')->count()
        ];
        
        // Get machine stats
        $machineStats = [
            'total' => Mesin::count(),
            'active' => Mesin::where('status', 'aktif')->count(),
            'in_use' => Mesin::where('status', 'digunakan')->count(),
            'maintenance' => Mesin::where('status', 'maintenance')->count()
        ];
        
        // Get product stats
        $productStats = [
            'total_items' => Item::count()
        ];
        
        return [
            'operators' => $operatorStats,
            'machines' => $machineStats,
            'products' => $productStats
        ];
    }
    
    /**
     * Get recent activities
     * 
     * @return array
     */
    private function getRecentActivities()
    {
        // Get recent orders (5)
        $recentOrders = Pesanan::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($order) {
                return [
                    'type' => 'order',
                    'id' => $order->id,
                    'description' => 'Pesanan baru dari ' . ($order->user ? $order->user->nama : 'Pengguna'),
                    'status' => $order->status,
                    'time' => $order->created_at->diffForHumans(),
                    'timestamp' => $order->created_at
                ];
            });
        
        // Get recent status changes (5)
        $recentStatusChanges = Pesanan::with('user')
            ->whereIn('status', ['Selesai', 'Sedang Diproses', 'Dibatalkan'])
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($order) {
                $description = '';
                switch ($order->status) {
                    case 'Selesai':
                        $description = 'Pesanan #' . $order->id . ' telah selesai';
                        break;
                    case 'Sedang Diproses':
                        $description = 'Pesanan #' . $order->id . ' sedang diproses';
                        break;
                    case 'Dibatalkan':
                        $description = 'Pesanan #' . $order->id . ' dibatalkan';
                        break;
                }
                
                return [
                    'type' => 'status_change',
                    'id' => $order->id,
                    'description' => $description,
                    'status' => $order->status,
                    'time' => $order->updated_at->diffForHumans(),
                    'timestamp' => $order->updated_at
                ];
            });
        
        // Combine and sort by timestamp
        $allActivities = $recentOrders->concat($recentStatusChanges)
            ->sortByDesc('timestamp')
            ->values()
            ->take(10)
            ->toArray();
        
        return $allActivities;
    }
}