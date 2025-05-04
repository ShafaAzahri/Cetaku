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
        .hero-section {
            background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('https://via.placeholder.com/1200x600');
            background-size: cover;
            background-position: center;
            color: white;
            text-align: center;
            padding: 100px 0;
            margin-bottom: 3rem;
        }
        .hero-section h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        .hero-section p {
            font-size: 1.25rem;
            margin-bottom: 2rem;
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
        .auth-buttons .btn {
            margin-left: 10px;
        }
        .user-welcome {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 2rem;
            padding: 2rem;
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        .user-welcome-info {
            text-align: center;
        }
        .user-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background-color: #007bff;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            margin: 0 auto 1rem;
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
                        <a class="nav-link" href="#">Layanan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Tentang Kami</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Kontak</a>
                    </li>
                </ul>
                
                <!-- Cek apakah user sudah login -->
                @if(session()->has('api_token') && session()->has('user'))
                    <!-- User sudah login - tampilkan nama dan dropdown logout -->
                    <div class="d-flex align-items-center">
                        <div class="dropdown">
                            <a class="text-white dropdown-toggle text-decoration-none" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle me-1"></i>
                                <span id="username-display">{{ session('user')['nama'] }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <!-- Link ke dashboard sesuai role -->
                                @if(session('user')['role'] == 'super_admin')
                                    <li><a class="dropdown-item" href="{{ route('superadmin.dashboard') }}"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
                                @elseif(session('user')['role'] == 'admin')
                                    <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
                                @endif
                                
                                <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profil</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt me-2"></i>Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                @else
                    <!-- User belum login - tampilkan tombol login saja -->
                    <div class="auth-buttons">
                        <a href="{{ route('login') }}" class="btn btn-outline-light">Login</a>
                    </div>
                @endif
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container">
            <h1>Solusi Cetak Digital Terbaik</h1>
            <p class="lead">Layanan percetakan berkualitas tinggi untuk semua kebutuhan Anda</p>
            <a href="#layanan" class="btn btn-primary btn-lg">Lihat Layanan Kami</a>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>