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
            
            <div id="error-message" class="alert alert-danger d-none" role="alert"></div>
            
            <form id="register-form">
                @csrf
                
                <div class="form-group">
                    <label class="form-label">Nama Panjang</label>
                    <input type="text" name="nama" id="nama" class="form-control" placeholder="Your full name" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Email</label>
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
                
                <div class="form-group">
                    <label class="form-label">Konfirmasi Password</label>
                    <div class="password-field">
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Konfirmasi password" required>
                        <span class="password-toggle-confirm">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-register" id="register-button">
                    <span class="button-text">Sign Up</span>
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                </button>
                
                <button type="button" class="btn btn-google">
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
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="{{ asset('js/auth.js') }}"></script>
    <script>
        // Setup CSRF token untuk semua request API
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Toggle password visibility for password field
        document.querySelector('.password-toggle').addEventListener('click', function() {
            const passwordInput = this.parentElement.querySelector('input[name="password"]');
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
        
        // Toggle password visibility for confirm password field
        document.querySelector('.password-toggle-confirm').addEventListener('click', function() {
            const passwordInput = this.parentElement.querySelector('input[name="password_confirmation"]');
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
        
        // Handle register form submission
        document.getElementById('register-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const buttonText = document.querySelector('.button-text');
            const spinner = document.querySelector('.spinner-border');
            const errorMessage = document.getElementById('error-message');
            
            // Tampilkan loading
            buttonText.classList.add('d-none');
            spinner.classList.remove('d-none');
            errorMessage.classList.add('d-none');
            
            const nama = document.getElementById('nama').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const passwordConfirmation = document.getElementById('password_confirmation').value;
            
            // Validasi sederhana
            if (password !== passwordConfirmation) {
                errorMessage.textContent = 'Password dan konfirmasi password tidak cocok.';
                errorMessage.classList.remove('d-none');
                buttonText.classList.remove('d-none');
                spinner.classList.add('d-none');
                return;
            }
            
            try {
                const success = await register(nama, email, password, passwordConfirmation);
                
                if (!success) {
                    errorMessage.textContent = 'Gagal mendaftar. Silakan coba lagi.';
                    errorMessage.classList.remove('d-none');
                }
            } catch (error) {
                console.error('Registration error:', error);
                
                if (error.response && error.response.data && error.response.data.errors) {
                    // Tampilkan pesan error dari validasi API
                    const errors = error.response.data.errors;
                    let errorHtml = '<ul class="mb-0">';
                    
                    for (const field in errors) {
                        errors[field].forEach(message => {
                            errorHtml += `<li>${message}</li>`;
                        });
                    }
                    
                    errorHtml += '</ul>';
                    errorMessage.innerHTML = errorHtml;
                } else {
                    errorMessage.textContent = 'Terjadi kesalahan saat mendaftar. Silakan coba lagi.';
                }
                
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