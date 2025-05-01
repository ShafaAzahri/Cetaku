@extends('admin.layout.admin')

@section('title', 'Dashboard Admin')

@section('content')
<div class="container-fluid p-4">
    <!-- Row 1: Welcome & Stats -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Selamat Datang, <span id="admin-name">Admin</span>!</h5>
                            <p class="card-text text-muted">Anda login sebagai Administrator</p>
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
                    <div style="height: 250px; position: relative;">
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
                    <div style="height: 200px; max-width: 200px; margin: 0 auto; position: relative;">
                        <canvas id="doughnutChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Jadwal Pesanan -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center bg-white">
                    <h5 class="m-0">Jadwal Pesanan</h5>
                    <div class="d-flex align-items-center">
                        <button class="btn btn-sm btn-outline-secondary me-2" type="button" id="calendarMonthDropdown" data-bs-toggle="dropdown">
                            Mei, 2025
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="calendarMonthDropdown">
                            <li><a class="dropdown-item" href="#">April, 2025</a></li>
                            <li><a class="dropdown-item" href="#">Mei, 2025</a></li>
                            <li><a class="dropdown-item" href="#">Juni, 2025</a></li>
                        </ul>
                        <div class="btn-group ms-2">
                            <button class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead>
                                <tr class="text-center">
                                    <th width="14.28%">MINGGU</th>
                                    <th width="14.28%">SENIN</th>
                                    <th width="14.28%">SELASA</th>
                                    <th width="14.28%">RABU</th>
                                    <th width="14.28%">KAMIS</th>
                                    <th width="14.28%">JUMAT</th>
                                    <th width="14.28%">SABTU</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr style="height: 100px;">
                                    <td class="bg-light text-danger">
                                        <div class="d-flex justify-content-end p-1">
                                            <span class="fw-bold">1</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-end p-1">
                                            <span class="fw-bold">2</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-end p-1">
                                            <span class="fw-bold">3</span>
                                        </div>
                                        <div class="p-1">
                                            <small class="d-block p-1 rounded text-primary bg-light">Pesanan 1</small>
                                            <small class="d-block p-1 rounded text-primary bg-light">Pelanggan 2</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-end p-1">
                                            <span class="fw-bold">4</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-end p-1">
                                            <span class="fw-bold">5</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-end p-1">
                                            <span class="fw-bold">6</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-end p-1">
                                            <span class="fw-bold">7</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr style="height: 100px;">
                                    <td class="bg-light text-danger">
                                        <div class="d-flex justify-content-end p-1">
                                            <span class="fw-bold">8</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-end p-1">
                                            <span class="fw-bold">9</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-end p-1">
                                            <span class="fw-bold">10</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-end p-1">
                                            <span class="fw-bold">11</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-end p-1">
                                            <span class="fw-bold">12</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-end p-1">
                                            <span class="fw-bold">13</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-end p-1">
                                            <span class="fw-bold">14</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="{{ asset('js/auth.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cek autentikasi
        if (!isLoggedIn()) {
            window.location.href = '/login';
            return;
        }
        
        // Cek role
        const user = getCurrentUser();
        if (user && user.role !== 'admin' && user.role !== 'superadmin') {
            window.location.href = '/user/welcome';
            return;
        }
        
        // Tampilkan nama admin
        if (user) {
            document.getElementById('admin-name').textContent = user.nama;
        }
        
        // Line Chart untuk Statistik Penjualan
        const salesCtx = document.getElementById('salesChart').getContext('2d');
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
                        max: 30
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Doughnut Chart untuk Total Penjualan
        const doughnutCtx = document.getElementById('doughnutChart').getContext('2d');
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
    });
</script>

<script src="{{ asset('js/check-auth.js') }}"></script>
@endsection