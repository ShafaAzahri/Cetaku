<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Admin | Cetaku</title>
    
    <!-- Google Font: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="brand-logo">
            <i class="fas fa-print"></i>
            <span class="brand-text">CETAKU</span>
        </div>
        <ul class="nav-list">
            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}" class="nav-link active">
                    <i class="fas fa-tachometer-alt"></i>
                    <span class="nav-text">Beranda</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.product-manager') }}" class="nav-link">
                    <i class="fas fa-boxes"></i>
                    <span class="nav-text">Kelola Produk</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="nav-text">Pesanan</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-users"></i>
                    <span class="nav-text">Pelanggan</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-user-tie"></i>
                    <span class="nav-text">Operator</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-truck"></i>
                    <span class="nav-text">Ekspedisi</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-history"></i>
                    <span class="nav-text">Riwayat</span>
                </a>
            </li>
            <li class="nav-item">
                <form action="{{ route('logout') }}" method="POST" id="logout-form" style="display: none;">
                    @csrf
                </form>
                <a href="#" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="nav-text">Logout</span>
                </a>
            </li>
        </ul>
    </aside>
    
    <!-- Overlay for mobile sidebar -->
    <div class="overlay" id="sidebar-overlay"></div>
    
    <!-- Main Content -->
    <div class="main-content" id="main-content">
        <!-- Top Navbar -->
        <nav class="navbar">
            <div class="d-flex align-items-center">
                <div class="toggle-sidebar me-3" id="toggle-sidebar">
                    <i class="fas fa-bars"></i>
                </div>
            </div>
            
            <form class="search-form d-none d-md-block">
                <input type="text" class="search-input" placeholder="Masukkan ID Pemesanan dan ID Customer untuk mencari detail info">
            </form>
            
            <div class="user-profile">
                <div class="avatar">
                    <img src="https://ui-avatars.com/api/?name={{ session('user')['nama'] }}&background=4361ee&color=fff" alt="User Avatar" id="user-avatar">
                </div>
                <div class="dropdown">
                    <a class="dropdown-toggle text-decoration-none text-dark" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="d-none d-sm-inline-block me-1 user-name">{{ session('user')['nama'] }}</span>
                        <i class="fas fa-chevron-down fa-xs"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user-circle"></i> Profil</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-cog"></i> Pengaturan</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        
        <!-- Page Content -->
        <div class="content">
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
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar Toggle
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            const toggleBtn = document.getElementById('toggle-sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            
            if (toggleBtn) {
                toggleBtn.addEventListener('click', function() {
                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('expanded');
                    
                    if (window.innerWidth < 992) {
                        sidebar.classList.toggle('mobile-visible');
                        overlay.classList.toggle('active');
                    }
                });
            }
            
            if (overlay) {
                overlay.addEventListener('click', function() {
                    sidebar.classList.remove('mobile-visible');
                    overlay.classList.remove('active');
                });
            }
            
            // Responsive adjustments
            window.addEventListener('resize', function() {
                if (window.innerWidth < 992) {
                    sidebar.classList.remove('collapsed');
                    mainContent.classList.remove('expanded');
                    sidebar.classList.remove('mobile-visible');
                    overlay.classList.remove('active');
                }
            });
            
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
</body>
</html>