<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard Admin') | Cetaku</title>
    
    <!-- Google Font: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    @yield('styles')
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
                <a href="{{ route('dashboard') }}" class="nav-link active">
                    <i class="fas fa-tachometer-alt"></i>
                    <span class="nav-text">Beranda</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.product-manager') }}" class="nav-link {{ request()->routeIs('admin.product-manager') ? 'active' : '' }}">
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
                    <img src="https://ui-avatars.com/api/?name={{ Auth::user()->nama }}&background=4361ee&color=fff" alt="User Avatar">
                </div>
                <div class="dropdown">
                    <a class="dropdown-toggle text-decoration-none text-dark" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="d-none d-sm-inline-block me-1">{{ Auth::user()->nama }}</span>
                        <i class="fas fa-chevron-down fa-xs"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user-circle"></i> Profil</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-cog"></i> Pengaturan</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt"></i> Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        
        <!-- Page Content -->
        <div class="content">
            @yield('content')
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
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
            
            // Add active class to current page nav link
            const currentPath = window.location.pathname;
            const navLinks = document.querySelectorAll('.nav-link');
            
            navLinks.forEach(link => {
                const href = link.getAttribute('href');
                if (href === currentPath || currentPath.includes(href) && href !== '#') {
                    link.classList.add('active');
                }
            });
        });
    </script>
    
    @yield('scripts')
</body>
</html>