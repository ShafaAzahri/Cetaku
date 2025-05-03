<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Polines-print | Daftar</title>
    
    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f5f7fb;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 0;
        }
        .register-container {
            display: flex;
            max-width: 1000px;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        .illustration-side {
            flex: 1;
            background-color: #f0f4f9;
            padding: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .register-form-side {
            flex: 1;
            padding: 40px;
        }
        .logo-container {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
        }
        .logo-container img {
            height: 50px;
            margin-right: 10px;
        }
        .logo-container h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #213B70;
            margin: 0;
        }
        .welcome-text {
            font-size: 1.8rem;
            font-weight: 700;
            color: #213B70;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-label {
            font-weight: 500;
            margin-bottom: 8px;
            display: block;
        }
        .form-control {
            padding: 12px 16px;
            border-radius: 8px;
            border: 1px solid #dde2e8;
            width: 100%;
            font-size: 15px;
        }
        .form-control:focus {
            border-color: #4285F4;
            box-shadow: 0 0 0 3px rgba(66, 133, 244, 0.2);
        }
        .password-field {
            position: relative;
        }
        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
        }
        .btn {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
            margin-bottom: 15px;
        }
        .btn-register {
            background-color: #213B70;
            border: none;
            color: white;
        }
        .btn-register:hover {
            background-color: #152a4f;
        }
        .btn-google {
            background-color: white;
            border: 1px solid #dde2e8;
            color: #333;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .btn-google img {
            margin-right: 8px;
        }
        .login-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #6c757d;
        }
        .login-link a {
            color: #4285F4;
            text-decoration: none;
            font-weight: 500;
        }
        @media (max-width: 768px) {
            .register-container {
                flex-direction: column;
                max-width: 450px;
                margin: 20px;
            }
            .illustration-side {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="illustration-side">
            <img src="{{ asset('images/cover.png') }}" alt="Office illustration" style="max-width: 100%;">
        </div>
        
        <div class="register-form-side">
            <div class="logo-container">
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
                           placeholder="Masukkan nama lengkap anda" value="{{ old('nama') }}" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control" 
                           placeholder="Masukkan email anda" value="{{ old('email') }}" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="password-field">
                        <input type="password" name="password" id="password" class="form-control" 
                               placeholder="Masukkan password" required>
                        <span class="password-toggle" onclick="togglePassword()">
                            <i class="fas fa-eye" id="toggleIconPassword"></i>
                        </span>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Konfirmasi Password</label>
                    <div class="password-field">
                        <input type="password" name="password_confirmation" id="password_confirmation" 
                               class="form-control" placeholder="Konfirmasi password" required>
                        <span class="password-toggle" onclick="togglePasswordConfirm()">
                            <i class="fas fa-eye" id="toggleIconConfirm"></i>
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
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle password visibility for password field
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIconPassword');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
        
        // Toggle password visibility for confirm password field  
        function togglePasswordConfirm() {
            const passwordInput = document.getElementById('password_confirmation');
            const toggleIcon = document.getElementById('toggleIconConfirm');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>