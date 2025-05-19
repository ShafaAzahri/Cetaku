@extends('admin.layout.admin')

@section('title', 'Dashboard')

@section('content')
<!-- Row 1: Welcome & Stats -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Selamat Datang, {{ session('user')['nama'] }}!</h5>
                        <p class="card-text text-muted">Anda login sebagai Administrator</p>
                        <a href="{{ route('admin.product-manager') }}" class="btn btn-primary mt-2">
                            <i class="fas fa-boxes me-1"></i> Kelola Produk
                        </a>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fas fa-user-tie text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <div class="row row-cols-1 row-cols-md-3 g-3">
                    <div class="col">
                        <div class="card border-0 bg-light h-100">
                            <div class="card-body text-center">
                                <h3 class="text-primary mb-2">30</h3>
                                <p class="card-text mb-0">Pesanan</p>
                                <small class="text-muted">Bulan Ini</small>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card border-0 bg-light h-100">
                            <div class="card-body text-center">
                                <h3 class="text-success mb-2">12</h3>
                                <p class="card-text mb-0">Selesai</p>
                                <small class="text-muted">Bulan Ini</small>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card border-0 bg-light h-100">
                            <div class="card-body text-center">
                                <h3 class="text-warning mb-2">8</h3>
                                <p class="card-text mb-0">Pending</p>
                                <small class="text-muted">Saat Ini</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistik dan Grafik -->
<div class="row">
    <!-- Statistik Penjualan -->
    <div class="col-md-6 mb-4">
        <div class="card h-100 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center bg-white">
                <h5 class="m-0">Statistik Penjualan</h5>
                <div>
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="salesMonthDropdown" data-bs-toggle="dropdown">
                        Mei 2025
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="salesMonthDropdown">
                        <li><a class="dropdown-item" href="#">April 2025</a></li>
                        <li><a class="dropdown-item" href="#">Mei 2025</a></li>
                        <li><a class="dropdown-item" href="#">Juni 2025</a></li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <p class="mb-2">30 Pesanan</p>
                <div style="height: 250px;">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Total Penjualan -->
    <div class="col-md-6 mb-4">
        <div class="card h-100 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center bg-white">
                <h5 class="m-0">Total Penjualan</h5>
                <div>
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="totalMonthDropdown" data-bs-toggle="dropdown">
                        Mei 2025
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="totalMonthDropdown">
                        <li><a class="dropdown-item" href="#">April 2025</a></li>
                        <li><a class="dropdown-item" href="#">Mei 2025</a></li>
                        <li><a class="dropdown-item" href="#">Juni 2025</a></li>
                    </ul>
                </div>
            </div>
            <div class="card-body text-center">
                <h3 class="text-primary mb-3">2.000.000,00 IDR</h3>
                <p class="mb-3">30 Pesanan</p>
                <div style="height: 200px; max-width: 200px; margin: 0 auto;">
                    <canvas id="doughnutChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Aktivitas Terbaru dan Pesanan Terbaru -->
<div class="row">
    <!-- Aktivitas Terbaru -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Aktivitas Terbaru</h5>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex align-items-center py-3">
                        <div class="me-3">
                            <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="mb-1">Pesanan Baru #1234</h6>
                            <p class="mb-0 text-muted small">Budi telah membuat pesanan baru</p>
                            <small class="text-muted">2 jam yang lalu</small>
                        </div>
                    </li>
                    <li class="list-group-item d-flex align-items-center py-3">
                        <div class="me-3">
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-check"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="mb-1">Pesanan Selesai #1230</h6>
                            <p class="mb-0 text-muted small">Pesanan telah selesai diproses</p>
                            <small class="text-muted">4 jam yang lalu</small>
                        </div>
                    </li>
                    <li class="list-group-item d-flex align-items-center py-3">
                        <div class="me-3">
                            <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="mb-1">Pembayaran Masuk #1232</h6>
                            <p class="mb-0 text-muted small">Ani telah melakukan pembayaran</p>
                            <small class="text-muted">6 jam yang lalu</small>
                        </div>
                    </li>
                    <li class="list-group-item d-flex align-items-center py-3">
                        <div class="me-3">
                            <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="mb-1">Pesanan Dibatalkan #1228</h6>
                            <p class="mb-0 text-muted small">Deni membatalkan pesanan</p>
                            <small class="text-muted">8 jam yang lalu</small>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="card-footer bg-white text-center">
                <a href="#" class="text-decoration-none">Lihat Semua Aktivitas</a>
            </div>
        </div>
    </div>
    
    <!-- Pesanan Terbaru -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Pesanan Terbaru</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Pelanggan</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pesanans as $pesanan)
                            <tr>
                                <td>#{{ $pesanan->id }}</td>
                                <td>{{ $pesanan->user->name }}</td> <!-- Relasi dengan User untuk nama pelanggan -->
                                <td>
                                    <span class="badge
                                        @if($pesanan->status == 'Baru') bg-info
                                        @elseif($pesanan->status == 'Proses') bg-warning
                                        @elseif($pesanan->status == 'Bayar') bg-success
                                        @else bg-primary
                                        @endif">
                                        {{ $pesanan->status }}
                                    </span>
                                </td>
                                <td>Rp {{ number_format($pesanan->total, 0, ',', '.') }}</td>
                                <td><a href="{{ route('pesanan.show', $pesanan->id) }}" class="btn btn-sm btn-outline-primary">Lihat</a></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white text-center">
                <a href="{{ route('admin.pesanan.index') }}" class="text-decoration-none">Lihat Semua Pesanan</a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sales Chart
        const salesCtx = document.getElementById('salesChart');
        if (salesCtx) {
            const salesChart = new Chart(salesCtx, {
                type: 'line',
                data: {
                    labels: ['1-5', '6-10', '11-15', '16-20', '21-25', '26-31'],
                    datasets: [{
                        label: 'Pesanan',
                        data: [12, 14, 15, 17, 19, 25],
                        borderColor: '#007bff',
                        tension: 0.1,
                        fill: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 30,
                            ticks: {
                                stepSize: 5
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }
        
        // Doughnut Chart
        const doughnutCtx = document.getElementById('doughnutChart');
        if (doughnutCtx) {
            const doughnutChart = new Chart(doughnutCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Pemesanan', 'Selesai', 'Gagal'],
                    datasets: [{
                        data: [20, 8, 2],
                        backgroundColor: [
                            '#007bff',
                            '#28a745',
                            '#dc3545'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
    });
</script>
@endsection