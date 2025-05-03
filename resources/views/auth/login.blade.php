<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Polines-print | Login</title>
    
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
        .login-container {
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
        .login-form-side {
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
        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }
        .form-check {
            display: flex;
            align-items: center;
        }
        .form-check-input {
            margin-right: 8px;
        }
        .form-check-label {
            font-size: 14px;
            color: #6c757d;
        }
        .forgot-link {
            font-size: 14px;
            color: #4285F4;
            text-decoration: none;
        }
        .btn {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
            margin-bottom: 15px;
        }
        .btn-login {
            background-color: #213B70;
            border: none;
            color: white;
        }
        .btn-login:hover {
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
        .register-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #6c757d;
        }
        .register-link a {
            color: #4285F4;
            text-decoration: none;
            font-weight: 500;
        }
        @media (max-width: 768px) {
            .login-container {
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
    <div class="login-container">
        <div class="illustration-side">
            <img src="{{ asset('images/cover.png') }}" alt="Office illustration" style="max-width: 100%;">
        </div>
        
        <div class="login-form-side">
            <div class="logo-container">
                <img src="{{ asset('images/polines.png') }}" alt="Polines Logo">
                <h2>Polines-print</h2>
            </div>
            
            <h1 class="welcome-text">Selamat datang kembali</h1>
            
            @if(session('error'))
                <div class="alert alert-danger" role="alert">
                    {{ session('error') }}
                </div>
            @endif
            
            @if(session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
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
            
            <form action="{{ route('login.submit') }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label class="form-label">Login</label>
                    <input type="email" name="email" id="email" class="form-control" 
                           placeholder="Enter your email" value="{{ old('email') }}" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="password-field">
                        <input type="password" name="password" id="password" class="form-control" 
                               placeholder="Enter password" required>
                        <span class="password-toggle" onclick="togglePassword()">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </span>
                    </div>
                </div>
                
                <div class="remember-forgot">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Ingat saya</label>
                    </div>
                    <a href="#" class="forgot-link">Lupa password?</a>
                </div>
                
                <button type="submit" class="btn btn-login">
                    <span class="button-text">Login</span>
                </button>
                
                <button type="button" class="btn btn-google" disabled>
                    <img src="{{ asset('images/google.png') }}" alt="Google logo" style="width: 20px; height: 20px;">
                    Or sign in with Google
                </button>
                
                <div class="register-link">
                    Tidak punya akun? <a href="{{ route('register') }}">Daftar sekarang</a>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
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