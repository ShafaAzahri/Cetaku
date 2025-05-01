<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cetaku | Login</title>
    
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body>
    <div class="login-container">
        <div class="illustration-side">
            <!-- Anda bisa mengganti dengan gambar ilustrasi yang sesuai -->
            <img src="{{ asset('images/cover.png') }}" alt="Office illustration" style="max-width: 100%;">
        </div>
        
        <div class="login-form-side">
            <div class="logo-container">
                <!-- Logo Polines - ganti dengan path logo yang sesuai -->
                <img src="{{ asset('images/poliness.png') }}" alt="Polines Logo">
                <h2>Polines-print</h2>
            </div>
            
            <h1 class="welcome-text">Selamat datang kembali</h1>
            
            <div id="error-message" class="alert alert-danger d-none" role="alert"></div>
            
            <form id="login-form">
                @csrf
                
                <div class="form-group">
                    <label class="form-label">Login</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="Enter your email" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="password-field">
                        <input type="password" name="password" id="password" class="form-control" placeholder="Enter password" required>
                        <span class="password-toggle">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                </div>
                
                <div class="remember-forgot">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Ingat saya</label>
                    </div>
                    <a href="{{ route('password.request') }}" class="forgot-link">Lupa password?</a>
                </div>
                
                <button type="submit" class="btn btn-login" id="login-button">
                    <span class="button-text">Login</span>
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                </button>
                
                <button type="button" class="btn btn-google">
                    <img src="{{ asset('images/google.png') }}" alt="Google logo" style="width: 20px; height: 20px;">
                    Or sign in with Google
                </button>
                
                <div class="register-link">
                    Tidak punya akun? <a href="{{ route('register') }}">Daftar sekarang</a>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="{{ asset('js/auth.js') }}"></script>
    <script>
        // Setup CSRF token untuk semua request API
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Toggle password visibility
        document.querySelector('.password-toggle').addEventListener('click', function() {
            const passwordInput = document.querySelector('input[name="password"]');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
        
        // Handle login form submission
        document.getElementById('login-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const buttonText = document.querySelector('.button-text');
            const spinner = document.querySelector('.spinner-border');
            const errorMessage = document.getElementById('error-message');
            
            // Tampilkan loading
            buttonText.classList.add('d-none');
            spinner.classList.remove('d-none');
            errorMessage.classList.add('d-none');
            
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            try {
                const success = await login(email, password);
                
                if (!success) {
                    errorMessage.textContent = 'Email atau password salah.';
                    errorMessage.classList.remove('d-none');
                }
            } catch (error) {
                console.error('Login error:', error);
                errorMessage.textContent = 'Terjadi kesalahan saat login. Silakan coba lagi.';
                errorMessage.classList.remove('d-none');
            } finally {
                // Kembalikan tombol ke kondisi awal
                buttonText.classList.remove('d-none');
                spinner.classList.add('d-none');
            }
        });
        
        // Cek apakah sudah login saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            if (isLoggedIn()) {
                const user = getCurrentUser();
                if (user.role === 'superadmin') {
                    window.location.href = '/superadmin/dashboard';
                } else if (user.role === 'admin') {
                    window.location.href = '/admin/dashboard';
                } else {
                    window.location.href = '/user/welcome';
                }
            }
        });
    </script>
</body>
</html>