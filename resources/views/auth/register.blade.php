<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cetaku | Daftar</title>
    
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
</head>
<body>
    <div class="register-container">
        <div class="illustration-side">
            <!-- Anda bisa mengganti dengan gambar ilustrasi yang sesuai -->
            <img src="{{ asset('images/cover.png') }}" alt="Office illustration" style="max-width: 100%;">
        </div>
        
        <div class="register-form-side">
            <div class="logo-container">
                <!-- Logo Polines - ganti dengan path logo yang sesuai -->
                <img src="{{ asset('images/polines.png') }}" alt="Polines Logo">
                <h2>Polines-print</h2>
            </div>
            
            <h1 class="welcome-text">Selamat datang</h1>
            
            @if(session('error'))
                <div class="alert alert-danger" role="alert">
                    {{ session('error') }}
                </div>
            @endif
            
            @if($errors->any())
                <div class="alert alert-danger" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form action="{{ route('register.submit') }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label class="form-label">Nama Panjang</label>
                    <input type="text" name="nama" id="nama" class="form-control" 
                           placeholder="Your full name" value="{{ old('nama') }}" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control" 
                           placeholder="Enter your email" value="{{ old('email') }}" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="password-field">
                        <input type="password" name="password" id="password" class="form-control" 
                               placeholder="Enter password" required>
                        <span class="password-toggle" onclick="togglePassword()">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Konfirmasi Password</label>
                    <div class="password-field">
                        <input type="password" name="password_confirmation" id="password_confirmation" 
                               class="form-control" placeholder="Konfirmasi password" required>
                        <span class="password-toggle-confirm" onclick="togglePasswordConfirm()">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-register">
                    <span class="button-text">Sign Up</span>
                </button>
                
                <button type="button" class="btn btn-google" disabled>
                    <img src="{{ asset('images/google.png') }}" alt="Google logo" style="width: 20px; height: 20px;">
                    Or sign up with Google
                </button>
                
                <div class="login-link">
                    Sudah punya akun? <a href="{{ route('login') }}">Masuk sekarang</a>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle password visibility for password field
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const icon = passwordInput.parentElement.querySelector('.password-toggle i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
        
        // Toggle password visibility for confirm password field  
        function togglePasswordConfirm() {
            const passwordInput = document.getElementById('password_confirmation');
            const icon = passwordInput.parentElement.querySelector('.password-toggle-confirm i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>