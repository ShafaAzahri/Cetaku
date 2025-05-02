<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Superadmin | Cetaku</title>
    
    <!-- Google Font: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }
        
        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            background: #001f3f;
            color: #fff;
            z-index: 1000;
            transition: all 0.3s;
            box-shadow: 4px 0 10px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
        }
        
        .sidebar.collapsed {
            width: 70px;
        }
        
        .sidebar .brand-logo {
            display: flex;
            align-items: center;
            padding: 20px;
            font-size: 22px;
            font-weight: 700;
            color: #fff;
            background: rgba(0, 0, 0, 0.2);
        }
        
        .sidebar .brand-logo i {
            margin-right: 15px;
            font-size: 24px;
        }
        
        .sidebar .brand-text {
            transition: opacity 0.3s;
        }
        
        .sidebar.collapsed .brand-text {
            opacity: 0;
            display: none;
        }
        
        .sidebar .nav-list {
            padding: 0;
            margin: 0;
            list-style: none;
        }
        
        .sidebar .nav-item {
            position: relative;
            margin-bottom: 5px;
        }
        
        .sidebar .nav-link {
            display: flex;
            align-items: center;
            color: #fff;
            padding: 12px 20px;
            transition: 0.3s;
            border-radius: 0;
            text-decoration: none;
        }
        
        .sidebar .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        
        .sidebar .nav-link.active {
            background: #007bff;
            color: #fff;
        }
        
        .sidebar .nav-link i {
            min-width: 24px;
            margin-right: 15px;
            font-size: 16px;
        }
        
        .sidebar .nav-text {
            transition: opacity 0.3s;
        }
        
        .sidebar.collapsed .nav-text {
            opacity: 0;
            display: none;
        }
        
        .sidebar.mobile-visible {
            transform: translateX(0) !important;
        }
        /* Main Content */
        .main-content {
            margin-left: 250px;
            padding: 20px;
            transition: all 0.3s;
            min-height: 100vh;
        }
        
        .main-content.expanded {
            margin-left: 70px;
        }
        
        /* Navbar */
        .navbar {
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            padding: 10px 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .navbar .toggle-sidebar {
            cursor: pointer;
            padding: 5px;
            color: #6c757d;
            transition: 0.3s;
        }
        
        .navbar .toggle-sidebar:hover {
            color: #007bff;
        }
        
        .navbar .search-form {
            flex-grow: 1;
            max-width: 500px;
            margin: 0 20px;
        }
        
        .navbar .search-input {
            width: 100%;
            padding: 8px 15px;
            border-radius: 50px;
            border: 1px solid #e0e0e0;
            font-size: 14px;
        }
        
        .navbar .user-profile {
            display: flex;
            align-items: center;
        }
        
        .navbar .avatar {
            width: 36px;
            height: 36px;
            overflow: hidden;
            border-radius: 50%;
            margin-right: 10px;
        }
        
        .navbar .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        /* Dashboard Cards */
        .dashboard-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 20px;
            margin-bottom: 20px;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .dashboard-card .icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-size: 24px;
        }
        
        .dashboard-card .icon.bg-primary {
            background-color: #cfe2ff;
            color: #0d6efd;
        }
        
        .dashboard-card .icon.bg-success {
            background-color: #d1e7dd;
            color: #198754;
        }
        
        .dashboard-card .icon.bg-warning {
            background-color: #fff3cd;
            color: #ffc107;
        }
        
        .dashboard-card .icon.bg-danger {
            background-color: #f8d7da;
            color: #dc3545;
        }
        
        .dashboard-card h2 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .dashboard-card p {
            color: #6c757d;
            margin-bottom: 0;
        }
        
        /* Activity Feed */
        .activity-item {
            display: flex;
            align-items: flex-start;
            padding: 15px 0;
            border-bottom: 1px solid #f3f3f3;
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .activity-icon {
            margin-right: 15px;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: 16px;
        }
        
        .activity-content {
            flex-grow: 1;
        }
        
        .activity-content h6 {
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .activity-content p {
            margin-bottom: 0;
            font-size: 14px;
            color: #6c757d;
        }
        
        .activity-time {
            font-size: 12px;
            color: #9e9e9e;
        }
        
        /* Responsive */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
                box-shadow: none;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .main-content.expanded {
                margin-left: 0;
            }
            
            .overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 999;
            }
            
            .overlay.active {
                display: block;
            }
        }
    </style>
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
                <a href="#" class="nav-link active">
                    <i class="fas fa-tachometer-alt"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-user-shield"></i>
                    <span class="nav-text">Admin Management</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-users"></i>
                    <span class="nav-text">User Management</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-store"></i>
                    <span class="nav-text">Toko Management</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link logout-button">
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
                <h5 class="mb-0">Dashboard Superadmin</h5>
            </div>
            
            <form class="search-form d-none d-md-block">
                <input type="text" class="search-input" placeholder="Search...">
            </form>
            
            <div class="user-profile">
                <div class="avatar">
                    <img src="https://ui-avatars.com/api/?name=Super+Admin&background=4361ee&color=fff" alt="User Avatar" id="user-avatar">
                </div>
                <div class="dropdown">
                    <a class="dropdown-toggle text-decoration-none text-dark" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="d-none d-sm-inline-block me-1 user-name">Super Administrator</span>
                        <i class="fas fa-chevron-down fa-xs"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user-circle"></i> Profil</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-cog"></i> Pengaturan</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item logout-button" href="#"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>
        
        <!-- Page Content -->
        <div class="content">
            <!-- Welcome Section -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="dashboard-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4>Selamat Datang, <span id="welcome-name" class="user-name">Super Administrator</span>!</h4>
                                <p>Panel kontrol untuk manajemen sistem Cetaku. Anda memiliki akses penuh ke seluruh fitur.</p>
                            </div>
                            <div>
                                <button class="btn btn-primary">
                                    <i class="fas fa-download me-2"></i>Download Laporan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Stats Cards -->
            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <div class="dashboard-card">
                        <div class="icon bg-primary">
                            <i class="fas fa-users"></i>
                        </div>
                        <h2>245</h2>
                        <p>Total Users</p>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="dashboard-card">
                        <div class="icon bg-success">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <h2>12</h2>
                        <p>Total Admins</p>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="dashboard-card">
                        <div class="icon bg-warning">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <h2>543</h2>
                        <p>Total Orders</p>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="dashboard-card">
                        <div class="icon bg-danger">
                            <i class="fas fa-store"></i>
                        </div>
                        <h2>3</h2>
                        <p>Total Stores</p>
                    </div>
                </div>
            </div>
            
            <!-- Charts and Activity -->
            <div class="row">
                <div class="col-md-8">
                    <div class="dashboard-card">
                        <h5 class="mb-4">Revenue Overview</h5>
                        <div style="height: 300px; width: 100%;">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="dashboard-card">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="mb-0">Recent Activities</h5>
                            <a href="#" class="text-decoration-none">View All</a>
                        </div>
                        
                        <div class="activity-list">
                            <div class="activity-item">
                                <div class="activity-icon bg-primary text-white">
                                    <i class="fas fa-user-plus"></i>
                                </div>
                                <div class="activity-content">
                                    <h6>New Admin Added</h6>
                                    <p>Admin Dani telah ditambahkan ke sistem</p>
                                    <div class="activity-time">5 menit yang lalu</div>
                                </div>
                            </div>
                            
                            <div class="activity-item">
                                <div class="activity-icon bg-success text-white">
                                    <i class="fas fa-cog"></i>
                                </div>
                                <div class="activity-content">
                                    <h6>System Update</h6>
                                    <p>Sistem berhasil diperbarui ke v2.5.1</p>
                                    <div class="activity-time">2 jam yang lalu</div>
                                </div>
                            </div>
                            
                            <div class="activity-item">
                                <div class="activity-icon bg-warning text-white">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>
                                <div class="activity-content">
                                    <h6>Low Disk Space</h6>
                                    <p>Server disk space sudah hampir penuh</p>
                                    <div class="activity-time">1 hari yang lalu</div>
                                </div>
                            </div>
                            
                            <div class="activity-item">
                                <div class="activity-icon bg-danger text-white">
                                    <i class="fas fa-lock"></i>
                                </div>
                                <div class="activity-content">
                                    <h6>Security Alert</h6>
                                    <p>Login gagal berulang kali terdeteksi</p>
                                    <div class="activity-time">2 hari yang lalu</div>
                                </div>
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
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="{{ asset('js/auth.js') }}"></script>
    <script src="{{ asset('js/check-auth.js') }}"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            const toggleBtn = document.getElementById('toggle-sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            
            // Toggle sidebar when button is clicked
            toggleBtn.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
                
                // For mobile
                if (window.innerWidth < 992) {
                    sidebar.classList.toggle('mobile-visible');
                    overlay.classList.toggle('active');
                }
            });
            
            // Close sidebar when clicking outside on mobile
            overlay.addEventListener('click', function() {
                sidebar.classList.remove('mobile-visible');
                overlay.classList.remove('active');
            });
            
            // Adjust sidebar on window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth < 992) {
                    sidebar.classList.remove('collapsed');
                    mainContent.classList.remove('expanded');
                    sidebar.classList.remove('mobile-visible');
                    overlay.classList.remove('active');
                }
            });
            
            // Handle logout
            const logoutButtons = document.querySelectorAll('.logout-button');
            logoutButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    logout();
                });
            });
            
            // Create Revenue Chart - Completely rewritten implementation
            setTimeout(() => {
                const ctx = document.getElementById('revenueChart');
                if (ctx) {
                    const myChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                            datasets: [{
                                label: 'Revenue 2025',
                                data: [18500, 22000, 19500, 24000, 29000, 32000, 35000, 38000, 36000, 40000, 43000, 50000],
                                backgroundColor: 'rgba(67, 97, 238, 0.1)',
                                borderColor: '#4361ee',
                                borderWidth: 3,
                                tension: 0.3,
                                fill: true
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    max: 60000,
                                    grid: {
                                        drawBorder: false,
                                        borderDash: [5, 5]
                                    },
                                    ticks: {
                                        stepSize: 10000
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
                } else {
                    console.error('Revenue chart canvas not found');
                }
            }, 500); // Add a small delay to ensure the DOM is ready
        });
    </script>
</body>
</html>