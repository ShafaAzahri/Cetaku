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
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4cc9f0;
            --success-color: #4caf50;
            --sidebar-width: 250px;
            --sidebar-collapsed-width: 70px;
            --transition-speed: 0.3s;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }
        
        /* Sidebar Styles */
        .sidebar {
            background-color: #1a3353;
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            color: white;
            z-index: 1000;
            transition: width var(--transition-speed) ease;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,0.2) transparent;
        }
        
        .sidebar::-webkit-scrollbar {
            width: 5px;
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background-color: rgba(255,255,255,0.2);
            border-radius: 10px;
        }
        
        .sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }
        
        .main-content {
            margin-left: var(--sidebar-width);
            transition: margin-left var(--transition-speed) ease;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .main-content.expanded {
            margin-left: var(--sidebar-collapsed-width);
        }
        
        .brand-logo {
            padding: 1.2rem;
            font-size: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            height: 70px;
            white-space: nowrap;
            overflow: hidden;
        }
        
        .brand-logo i {
            font-size: 1.5rem;
            margin-right: 1rem;
        }
        
        .brand-text {
            transition: opacity var(--transition-speed);
        }
        
        .sidebar.collapsed .brand-text {
            opacity: 0;
        }
        
        .nav-list {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }
        
        .nav-item {
            margin: 5px 10px;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 15px;
            display: flex;
            align-items: center;
            transition: all var(--transition-speed);
            border-radius: 8px;
            text-decoration: none;
            white-space: nowrap;
        }
        
        .nav-link:hover {
            color: white;
            background-color: rgba(255,255,255,0.1);
        }
        
        .nav-link.active {
            color: white;
            background-color: var(--primary-color);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .nav-link i {
            font-size: 1.2rem;
            min-width: 30px;
            display: flex;
            justify-content: center;
            margin-right: 15px;
        }
        
        .sidebar.collapsed .nav-text {
            opacity: 0;
            display: none;
        }
        
        /* Navbar Styles */
        .navbar {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 0.7rem 1.5rem;
            height: 70px;
            z-index: 900;
        }
        
        .toggle-sidebar {
            font-size: 1.3rem;
            cursor: pointer;
            color: #555;
            transition: color 0.2s;
        }
        
        .toggle-sidebar:hover {
            color: var(--primary-color);
        }
        
        .search-form {
            flex: 1;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .search-input {
            border-radius: 50px;
            padding: 0.6rem 1.5rem;
            width: 100%;
            border: 1px solid #eaeaea;
            background-color: #f8f9fa;
            transition: all 0.3s;
            font-size: 0.9rem;
        }
        
        .search-input:focus {
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.15);
            border-color: var(--primary-color);
        }
        
        .user-profile {
            display: flex;
            align-items: center;
        }
        
        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            overflow: hidden;
            margin-right: 0.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .dropdown-toggle {
            display: flex;
            align-items: center;
            font-weight: 500;
        }
        
        .dropdown-toggle::after {
            display: none;
        }
        
        .dropdown-menu {
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            border-radius: 12px;
            padding: 0.7rem 0;
            min-width: 200px;
        }
        
        .dropdown-item {
            padding: 0.5rem 1.2rem;
            transition: all 0.2s;
        }
        
        .dropdown-item:hover {
            background-color: #f3f4f6;
            color: var(--primary-color);
        }
        
        .dropdown-item i {
            width: 20px;
            margin-right: 10px;
            text-align: center;
        }
        
        /* Content area */
        .content {
            flex: 1;
            padding: 1.5rem;
            transition: all var(--transition-speed);
        }
        
        /* Card Styles */
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .card-header {
            background-color: white;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 1.2rem 1.5rem;
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        /* Button Styles */
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        /* Table Styles */
        .table {
            width: 100%;
            margin-bottom: 0;
        }
        
        .table th {
            font-weight: 600;
            border-top: none;
            background-color: #f9fafc;
        }
        
        .table td {
            vertical-align: middle;
        }
        
        /* Responsive adjustments */
        @media (max-width: 992px) {
            .sidebar {
                width: var(--sidebar-collapsed-width);
                transform: translateX(-100%);
            }
            
            .sidebar.mobile-visible {
                transform: translateX(0);
                width: var(--sidebar-width);
            }
            
            .sidebar.mobile-visible .brand-text,
            .sidebar.mobile-visible .nav-text {
                opacity: 1;
                display: inline;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: rgba(0,0,0,0.5);
                z-index: 999;
            }
            
            .overlay.active {
                display: block;
            }
        }
    </style>
    
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
                <a href="#" class="nav-link">
                    <i class="fas fa-box"></i>
                    <span class="nav-text">Produk</span>
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