<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="auth-required" content="true">
    <meta name="required-role" content="admin">
    <title>@yield('title', 'Dashboard Admin') | Cetaku</title>
    
    <!-- Google Font: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
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
    @yield('styles')
</head>
<body>
    <!-- Sidebar -->
    @include('admin.components.sidebar')
    
    <!-- Overlay for mobile sidebar -->
    <div class="overlay" id="sidebar-overlay"></div>
    
    <!-- Main Content -->
    <div class="main-content" id="main-content">
        <!-- Top Navbar -->
        @include('admin.components.navbar')
        
        <!-- Page Content -->
        <div class="content">
            @yield('content')
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Setup CSRF token untuk semua request API
            axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            console.log("DOM loaded in admin.blade.php");
            
            // Tampilkan nama user 
            const userNameElements = document.querySelectorAll('.user-name');
            userNameElements.forEach(el => {
                if (el && typeof el.textContent !== 'undefined') {
                    // User name is set by the server-side in the blade template
                    console.log("User name displayed: " + el.textContent);
                }
            });
            
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            const toggleBtn = document.getElementById('toggle-sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            
            // Toggle sidebar when button is clicked
            if (toggleBtn) {
                toggleBtn.addEventListener('click', function() {
                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('expanded');
                    
                    // For mobile
                    if (window.innerWidth < 992) {
                        sidebar.classList.toggle('mobile-visible');
                        overlay.classList.toggle('active');
                    }
                });
            }
            
            // Close sidebar when clicking outside on mobile
            if (overlay) {
                overlay.addEventListener('click', function() {
                    sidebar.classList.remove('mobile-visible');
                    overlay.classList.remove('active');
                });
            }
            
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
                    console.log("Logout button clicked");
                    // Logout is handled by the onclick attribute in the HTML
                });
            });
        });
    </script>
    
    @yield('scripts')
</body>
</html>