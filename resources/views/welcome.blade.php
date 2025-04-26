<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cetaku Print</title>
    
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .main-content {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        .welcome-box {
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            max-width: 500px;
            width: 100%;
        }
        .btn-login, .btn-register {
            width: 100%;
            margin-bottom: 10px;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 1rem 0;
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <div class="welcome-box mx-auto">
                <h1 class="mb-4">Cetaku Print</h1>
                
                @guest
                    <p class="mb-4">Silahkan login atau daftar untuk menggunakan layanan kami.</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('login') }}" class="btn btn-primary btn-login">Login</a>
                        <a href="{{ route('register') }}" class="btn btn-outline-primary btn-register">Daftar</a>
                    </div>
                @else
                    <div class="alert alert-success mb-4">
                        <h4 class="alert-heading">Halo, {{ Auth::user()->nama }}!</h4>
                        <p>Selamat datang di Cetaku Print.</p>
                        
                        @if(Auth::user()->role && Auth::user()->role->nama_role === 'admin')
                            <div class="mt-3">
                                <a href="{{ route('dashboard') }}" class="btn btn-primary">Dashboard Admin</a>
                            </div>
                        @endif
                    </div>
                    
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger">Logout</button>
                    </form>
                @endguest
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p class="mb-0">&copy; 2025 Cetaku Print. All rights reserved.</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>