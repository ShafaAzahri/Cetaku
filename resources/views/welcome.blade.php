<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Selamat Datang | Cetaku Print</title>
    
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            font-family: 'Source Sans Pro', sans-serif;
        }
        .navbar {
            background-color: #007bff;
        }
        .navbar-brand {
            font-weight: bold;
        }
        .main-content {
            flex: 1;
            padding: 2rem 0;
        }
        .welcome-header {
            background-color: #f8f9fa;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-bottom: 1px solid #e9ecef;
        }
        .card {
            transition: transform 0.3s;
            margin-bottom: 1.5rem;
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        .card-img-top {
            height: 200px;
            object-fit: cover;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 1.5rem 0;
            border-top: 1px solid #e9ecef;
        }
        #user-name {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-print me-2"></i>CETAKU
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Produk</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Pesanan Saya</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Bantuan</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center">
                    <div class="dropdown">
                        <a class="text-white dropdown-toggle text-decoration-none" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-1"></i>
                            <span id="user-name">User</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profil</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-shopping-bag me-2"></i>Pesanan</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><button class="dropdown-item" id="logout-button"><i class="fas fa-sign-out-alt me-2"></i>Logout</button></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Welcome Header -->
    <header class="welcome-header">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <h1>Selamat Datang di Cetaku Print</h1>
                    <p class="lead">Platform cetak online terpercaya untuk semua kebutuhan pencetakan Anda.</p>
                </div>
                <div class="col-md-4 d-flex justify-content-end align-items-center">
                    <button class="btn btn-primary btn-lg">
                        <i class="fas fa-plus-circle me-2"></i>Buat Pesanan Baru
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <h2 class="mb-4">Produk Populer</h2>
            
            <div class="row">
                <!-- Product Card 1 -->
                <div class="col-md-4">
                    <div class="card">
                        <img src="https://via.placeholder.com/300x200" class="card-img-top" alt="Banner">
                        <div class="card-body">
                            <h5 class="card-title">Banner</h5>
                            <p class="card-text">Banner berkualitas tinggi dengan berbagai ukuran dan bahan.</p>
                            <a href="#" class="btn btn-primary">Pesan Sekarang</a>
                        </div>
                    </div>
                </div>
                
                <!-- Product Card 2 -->
                <div class="col-md-4">
                    <div class="card">
                        <img src="https://via.placeholder.com/300x200" class="card-img-top" alt="Kaos">
                        <div class="card-body">
                            <h5 class="card-title">Kaos</h5>
                            <p class="card-text">Cetak kaos custom dengan desain sesuai keinginan Anda.</p>
                            <a href="#" class="btn btn-primary">Pesan Sekarang</a>
                        </div>
                    </div>
                </div>
                
                <!-- Product Card 3 -->
                <div class="col-md-4">
                    <div class="card">
                        <img src="https://via.placeholder.com/300x200" class="card-img-top" alt="Brosur">
                        <div class="card-body">
                            <h5 class="card-title">Brosur</h5>
                            <p class="card-text">Brosur profesional untuk keperluan promosi dan marketing.</p>
                            <a href="#" class="btn btn-primary">Pesan Sekarang</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>Cetaku Print</h5>
                    <p>Jasa percetakan online terbaik yang menyediakan berbagai layanan percetakan berkualitas.</p>
                </div>
                <div class="col-md-3">
                    <h5>Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-decoration-none">Tentang Kami</a></li>
                        <li><a href="#" class="text-decoration-none">Produk</a></li>
                        <li><a href="#" class="text-decoration-none">Layanan</a></li>
                        <li><a href="#" class="text-decoration-none">Kontak</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Hubungi Kami</h5>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-map-marker-alt me-2"></i>Jl. Prof. Sudarto, Tembalang</li>
                        <li><i class="fas fa-phone me-2"></i>(024) 7473417</li>
                        <li><i class="fas fa-envelope me-2"></i>info@cetaku.com</li>
                    </ul>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-12 text-center">
                    <p class="mb-0">&copy; 2025 Cetaku Print. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="{{ asset('js/auth.js') }}"></script>
    <script>
        // Setup CSRF token untuk semua request API
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        document.addEventListener('DOMContentLoaded', function() {
            // Cek autentikasi
            if (!isLoggedIn()) {
                window.location.href = '/login';
                return;
            }
            
            // Tampilkan nama user
            const user = getCurrentUser();
            if (user) {
                document.getElementById('user-name').textContent = user.nama;
            }
            
            // Handle logout
            document.getElementById('logout-button').addEventListener('click', function() {
                logout();
            });
        });
    </script>
</body>
</html>