@extends('superadmin.layout.superadmin')

@section('title', 'Dashboard')

@section('styles')
<style>
    .stat-card {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        padding: 20px;
        margin-bottom: 20px;
        transition: all 0.3s;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .stat-card .icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-right: 15px;
    }
    
    .stat-card .count {
        font-size: 28px;
        font-weight: 600;
        margin-bottom: 5px;
    }
    
    .stat-card .label {
        color: #6c757d;
        font-size: 14px;
        margin-bottom: 0;
    }
    
    .stat-card .text-blue {
        color: #4361ee;
    }
    
    .stat-card .text-green {
        color: #10b981;
    }
    
    .stat-card .text-orange {
        color: #f59e0b;
    }
    
    .stat-card .text-red {
        color: #ef4444;
    }
    
    .stat-card .bg-blue-light {
        background-color: rgba(67, 97, 238, 0.1);
    }
    
    .stat-card .bg-green-light {
        background-color: rgba(16, 185, 129, 0.1);
    }
    
    .stat-card .bg-orange-light {
        background-color: rgba(245, 158, 11, 0.1);
    }
    
    .stat-card .bg-red-light {
        background-color: rgba(239, 68, 68, 0.1);
    }
    
    .activity-card {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
    
    .activity-card .card-header {
        padding: 15px 20px;
        border-bottom: 1px solid #eee;
    }
    
    .activity-card .card-title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 0;
    }
    
    .activity-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .activity-item {
        padding: 15px 20px;
        border-bottom: 1px solid #eee;
        display: flex;
        align-items: center;
    }
    
    .activity-item:last-child {
        border-bottom: none;
    }
    
    .activity-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
    }
    
    .activity-content {
        flex: 1;
    }
    
    .activity-title {
        font-weight: 500;
        margin-bottom: 2px;
    }
    
    .activity-time {
        font-size: 12px;
        color: #6c757d;
    }
    
    .chart-card {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        padding: 20px;
        margin-bottom: 20px;
    }
    
    .chart-card .card-title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 20px;
    }
    
    .chart-container {
        height: 300px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Dashboard Super Admin</h4>
    </div>
    
    <!-- Stat Cards -->
    <div class="row">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex align-items-center">
                    <div class="icon bg-blue-light text-blue">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <div class="count">{{ $stats['users']['total_users'] ?? 0 }}</div>
                        <p class="label">Total User</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex align-items-center">
                    <div class="icon bg-green-light text-green">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div>
                        <div class="count">{{ $stats['users']['total_admins'] ?? 0 }}</div>
                        <p class="label">Total Admin</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex align-items-center">
                    <div class="icon bg-orange-light text-orange">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div>
                        <div class="count">{{ $stats['orders']['total_orders'] ?? 0 }}</div>
                        <p class="label">Total Pesanan</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex align-items-center">
                    <div class="icon bg-red-light text-red">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div>
                        <div class="count">Rp {{ number_format($stats['revenue']['total_revenue'] ?? 0, 0, ',', '.') }}</div>
                        <p class="label">Total Revenue</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Chart -->
        <div class="col-md-8">
            <div class="chart-card">
                <h5 class="card-title">Pendapatan Bulanan</h5>
                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Activity -->
        <div class="col-md-4">
            <div class="activity-card">
                <div class="card-header">
                    <h5 class="card-title">Aktivitas Terbaru</h5>
                </div>
                <ul class="activity-list">
                    @forelse($recent_activities ?? [] as $activity)
                    <li class="activity-item">
                        <div class="activity-icon bg-{{ $activity['type'] == 'order' ? 'blue' : 'green' }}-light text-{{ $activity['type'] == 'order' ? 'blue' : 'green' }}">
                            <i class="fas fa-{{ $activity['type'] == 'order' ? 'shopping-cart' : 'exchange-alt' }}"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">{{ $activity['description'] }}</div>
                            <div class="activity-time">{{ $activity['time'] }}</div>
                        </div>
                    </li>
                    @empty
                    <li class="activity-item">
                        <div class="activity-content">
                            <div class="activity-title">Tidak ada aktivitas terbaru</div>
                        </div>
                    </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Setup Revenue Chart
        const revenueCtx = document.getElementById('revenueChart');
        
        if (revenueCtx) {
            const revenueData = @json($stats['revenue']['monthly_revenue'] ?? []);
            
            const labels = revenueData.map(item => item.month);
            const values = revenueData.map(item => item.revenue);
            
            new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Pendapatan Bulanan',
                        data: values,
                        backgroundColor: 'rgba(67, 97, 238, 0.1)',
                        borderColor: '#4361ee',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Pendapatan: Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endsection