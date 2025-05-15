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
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/product/procuct.css') }}">
    @yield('styles')
</head>
<body>
    <!-- Sidebar Component -->
    @include('admin.components.sidebar')
    
    <!-- Overlay for mobile sidebar -->
    <div class="overlay" id="sidebar-overlay"></div>
    
    <!-- Main Content -->
    <div class="main-content" id="main-content">
        <!-- Navbar Component -->
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
            
            // Cek sesi saat halaman dimuat
            fetch('/debug/session')
                .then(response => response.json())
                .then(data => {
                    if (!data.api_token) {
                        console.error('Session token not found');
                        // Redirect ke login jika tidak ada token
                        window.location.href = '/login?session_expired=true';
                    } else {
                        console.log('Session validation OK');
                    }
                })
                .catch(error => {
                    console.error('Session check error:', error);
                });
            
            // Fungsi sidebar toggle
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
        });
    </script>
    
    
    @yield('scripts')
</body>
</html>