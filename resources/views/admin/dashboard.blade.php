@extends('admin.layout.admin')

@section('title', 'Dashboard')

@section('content')
<!-- Row 1: Welcome & Stats -->
<div class="row mb-4">
    <div class="col-md-5">
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
    <div class="col-md-7">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <div class="row row-cols-1 row-cols-md-4 g-3">
                    <div class="col">
                        <div class="card border-0 bg-light h-100">
                            <div class="card-body text-center d-flex flex-column justify-content-center">
                                <h3 class="text-primary mb-2">{{ $pesananBulanIni }}</h3>
                                <p class="card-text mb-0">Pesanan</p>
                                <small class="text-muted">Bulan Ini</small>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card border-0 bg-light h-100">
                            <div class="card-body text-center d-flex flex-column justify-content-center">
                                <h3 class="text-success mb-2">{{ $pesananSelesaiBulanIni }}</h3>
                                <p class="card-text mb-0">Selesai</p>
                                <small class="text-muted">Bulan Ini</small>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card border-0 bg-light h-100">
                            <div class="card-body text-center d-flex flex-column justify-content-center">
                                <h3 class="text-warning mb-2">{{ $pesananBerjalan }}</h3>
                                <p class="card-text mb-0">Berjalan</p>
                                <small class="text-muted">Saat Ini</small>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card border-0 bg-light h-100">
                            <div class="card-body text-center d-flex flex-column justify-content-center">
                                <h3 class="text-danger mb-2">{{ $pesananDibatalkan }}</h3>
                                <p class="card-text mb-0">Dibatalkan</p>
                                <small class="text-muted">Bulan Ini</small>
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
    <div class="col-md-8 mb-4">
        <div class="card h-100 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center bg-white">
                <h5 class="m-0">Statistik Penjualan</h5>
                <div>
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="salesMonthDropdown" data-bs-toggle="dropdown">
                        {{ \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->format('F Y') }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="salesMonthDropdown">
                        @foreach($months as $month)
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.dashboard', ['month' => $month->year.'-'.$month->month]) }}">
                                    {{ \Carbon\Carbon::createFromFormat('Y-m', $month->year.'-'.$month->month)->format('F Y') }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="text-center">
                            <h6 class="text-muted mb-1">Total Pesanan</h6>
                            <h4 class="text-primary mb-0">{{ $pesananBulanIni }}</h4>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h6 class="text-muted mb-1">Rata-rata Harian</h6>
                            <h4 class="text-info mb-0">{{ round($pesananBulanIni / $jumlahHari, 1) }}</h4>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h6 class="text-muted mb-1">Tingkat Keberhasilan</h6>
                            <h4 class="text-success mb-0">{{ $pesananBulanIni > 0 ? round(($pesananSelesaiBulanIni / $pesananBulanIni) * 100, 1) : 0 }}%</h4>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h6 class="text-muted mb-1">Pesanan Hari Ini</h6>
                            <h4 class="text-warning mb-0">{{ $pesananPerTanggal[date('j')] ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
                <div style="height: 350px;">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Penjualan & Status Distribution -->
    <div class="col-md-4 mb-4">
        <div class="card h-100 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="m-0">Total Penjualan</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <h2 class="text-primary mb-2">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</h2>
                    <small class="text-muted">
                        Total pemasukan di bulan
                        {{ \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->format('F Y') }}
                    </small>
                </div>
                
                <h6 class="mb-3">Distribusi Status Pesanan</h6>
                <div style="height: 200px; margin-bottom: 20px;">
                    <canvas id="statusChart"></canvas>
                </div>
                
                <div class="mt-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <small class="text-muted">Selesai</small>
                        <span class="badge bg-success">{{ $pesananSelesaiBulanIni }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <small class="text-muted">Berjalan</small>
                        <span class="badge bg-warning">{{ $pesananBerjalan }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <small class="text-muted">Dibatalkan</small>
                        <span class="badge bg-danger">{{ $pesananDibatalkan }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Trend Analysis -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="m-0">Analisis Trend Penjualan</h5>
            </div>
            <div class="card-body">
                <div style="height: 300px;">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pesanan Terbaru dan Riwayat Pesanan -->
<div class="row">
    <!-- Pesanan Terbaru -->
    <div class="col-md-12 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Pesanan Terbaru</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">ID</th>
                                <th class="text-center">Pelanggan</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Total</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pesananTerbaru as $pesanan)
                            <tr>
                                <td class="text-center">#{{ $pesanan->pesanan_id }}</td>
                                <td class="text-center">{{ $pesanan->pelanggan }}</td>
                                <td class="text-center">
                                    <span class="badge 
                                    @if($pesanan->status == 'Pemesanan') bg-secondary
                                    @elseif($pesanan->status == 'Dikonfirmasi') bg-info
                                    @elseif($pesanan->status == 'Sedang Diproses') bg-warning
                                    @elseif($pesanan->status == 'Menunggu Pengambilan') bg-warning
                                    @elseif($pesanan->status == 'Sedang Dikirim') bg-primary
                                    @elseif($pesanan->status == 'Selesai') bg-success
                                    @elseif($pesanan->status == 'Dibatalkan') bg-danger
                                    @endif">{{ $pesanan->status }}</span>
                                </td>
                                <td class="text-center">Rp {{ number_format($pesanan->total, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <a href="{{ route('admin.pesanan.show', $pesanan->pesanan_id) }}" class="btn btn-sm btn-outline-primary">Lihat</a>
                                </td>
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

    <!-- Riwayat Pesanan -->
    <div class="col-md-12 mb-0">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Riwayat Pesanan</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">No</th>
                                <th class="text-center">Tanggal</th>
                                <th class="text-center">Pesanan ID</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Info</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($riwayatPesanan as $key => $pesanan)
                            <tr>
                                <td class="text-center">{{ $key + 1 }}</td>
                                <td class="text-center">{{ $pesanan->created_at->format('Y-m-d') }}</td>
                                <td class="text-center">{{ str_pad($pesanan->id, 4, '0', STR_PAD_LEFT) }}</td>
                                <td class="text-center">
                                    <span class="badge 
                                        @if($pesanan->status == 'Selesai') bg-success
                                        @elseif($pesanan->status == 'Dibatalkan') bg-danger
                                        @endif">{{ $pesanan->status }}</span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.pesanan.show', $pesanan->id) }}" class="btn btn-sm btn-outline-primary">Lihat Riwayat</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Configure Chart.js defaults
        Chart.defaults.font.family = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif";
        Chart.defaults.color = '#6c757d';
        
        // Sales Chart - Enhanced Line Chart
        const salesCtx = document.getElementById('salesChart');
        const pesananPerTanggal = @json($pesananPerTanggal);
        const daysInMonth = {{ $jumlahHari }};
        
        const labels = Array.from({ length: daysInMonth }, (_, i) => i + 1);
        const data = labels.map(tgl => pesananPerTanggal[tgl] ?? 0);
        
        // Calculate moving average
        const movingAverage = data.map((_, index, array) => {
            const start = Math.max(0, index - 3);
            const end = Math.min(array.length, index + 4);
            const subset = array.slice(start, end);
            return subset.reduce((sum, val) => sum + val, 0) / subset.length;
        });

        if (salesCtx) {
            const salesChart = new Chart(salesCtx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Pesanan Harian',
                        data: data,
                        borderColor: '#007bff',
                        backgroundColor: 'rgba(0, 123, 255, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#007bff',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 20
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#ffffff',
                            bodyColor: '#ffffff',
                            borderColor: '#007bff',
                            borderWidth: 1,
                            cornerRadius: 8,
                            displayColors: true,
                            callbacks: {
                                title: function(context) {
                                    return 'Tanggal ' + context[0].label;
                                },
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.parsed.y + ' pesanan';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                callback: function(value) {
                                    return value + ' pesanan';
                                }
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            },
                            title: {
                                display: true,
                                text: 'Jumlah Pesanan'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            title: {
                                display: true,
                                text: 'Tanggal'
                            }
                        }
                    }
                }
            });
        }

        // Status Distribution Chart
        const statusCtx = document.getElementById('statusChart');
        if (statusCtx) {
            const statusChart = new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Selesai', 'Berjalan', 'Dibatalkan'],
                    datasets: [{
                        data: [{{ $pesananSelesaiBulanIni }}, {{ $pesananBerjalan }}, {{ $pesananDibatalkan }}],
                        backgroundColor: [
                            '#28a745',
                            '#ffc107',
                            '#dc3545'
                        ],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#ffffff',
                            bodyColor: '#ffffff',
                            borderColor: '#007bff',
                            borderWidth: 1,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((sum, val) => sum + val, 0);
                                    const percentage = total > 0 ? Math.round((context.parsed / total) * 100) : 0;
                                    return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                                }
                            }
                        }
                    },
                    cutout: '60%'
                }
            });
        }

        // Trend Analysis Chart
        const trendCtx = document.getElementById('trendChart');
        if (trendCtx) {
            // Create weekly data from daily data
            const weeklyData = [];
            const weekLabels = [];
            
            for (let i = 0; i < daysInMonth; i += 7) {
                const weekEnd = Math.min(i + 6, daysInMonth - 1);
                const weekStart = i;
                let weekSum = 0;
                
                for (let j = weekStart; j <= weekEnd; j++) {
                    weekSum += data[j] || 0;
                }
                
                weeklyData.push(weekSum);
                weekLabels.push(`Minggu ${Math.floor(i / 7) + 1}`);
            }

            const trendChart = new Chart(trendCtx, {
                type: 'bar',
                data: {
                    labels: weekLabels,
                    datasets: [{
                        label: 'Pesanan per Minggu',
                        data: weeklyData,
                        backgroundColor: 'rgba(0, 123, 255, 0.6)',
                        borderColor: '#007bff',
                        borderWidth: 1,
                        borderRadius: 4,
                        borderSkipped: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#ffffff',
                            bodyColor: '#ffffff',
                            borderColor: '#007bff',
                            borderWidth: 1,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(context) {
                                    return 'Total: ' + context.parsed.y + ' pesanan';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                callback: function(value) {
                                    return value + ' pesanan';
                                }
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endsection