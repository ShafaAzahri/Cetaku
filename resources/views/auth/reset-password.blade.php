<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CETAKU | Reset Password</title>
    
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
        .reset-container {
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
        .reset-form-side {
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
            margin-bottom: 10px;
        }
        .subtitle-text {
            font-size: 14px;
            color: #6c757d;
            margin-bottom: 30px;
            line-height: 1.5;
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
            position: relative;
        }
        .btn-primary {
            background-color: #213B70;
            border: none;
            color: white;
        }
        .btn-primary:hover {
            background-color: #152a4f;
        }
        .btn-secondary {
            background-color: white;
            border: 1px solid #dde2e8;
            color: #333;
        }
        .btn-secondary:hover {
            background-color: #f8f9fa;
        }
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        .btn-loading {
            color: transparent;
        }
        .btn-loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            top: 50%;
            left: 50%;
            margin-left: -10px;
            margin-top: -10px;
            border: 2px solid #ffffff;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .back-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #6c757d;
        }
        .back-link a {
            color: #4285F4;
            text-decoration: none;
            font-weight: 500;
        }
        .phase-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }
        .phase-step {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: #e9ecef;
            color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
            margin: 0 10px;
            position: relative;
        }
        .phase-step.active {
            background-color: #213B70;
            color: white;
        }
        .phase-step.completed {
            background-color: #28a745;
            color: white;
        }
        .phase-step::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 100%;
            width: 20px;
            height: 2px;
            background-color: #e9ecef;
            transform: translateY(-50%);
        }
        .phase-step:last-child::after {
            display: none;
        }
        .phase-step.completed::after {
            background-color: #28a745;
        }
        .otp-input-group {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-bottom: 20px;
        }
        .otp-input {
            width: 50px;
            height: 50px;
            text-align: center;
            border: 2px solid #dde2e8;
            border-radius: 8px;
            font-size: 18px;
            font-weight: 600;
        }
        .otp-input:focus {
            border-color: #4285F4;
            box-shadow: 0 0 0 3px rgba(66, 133, 244, 0.2);
        }
        .otp-input.success {
            border-color: #28a745;
            background-color: #d4edda;
        }
        .resend-otp {
            text-align: center;
            margin-bottom: 20px;
        }
        .resend-otp button {
            background: none;
            border: none;
            color: #4285F4;
            font-size: 14px;
            cursor: pointer;
            text-decoration: underline;
        }
        .resend-otp button:disabled {
            color: #6c757d;
            cursor: not-allowed;
            text-decoration: none;
        }
        .timer {
            color: #6c757d;
            font-size: 14px;
        }
        .alert {
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .hidden {
            display: none;
        }
        @media (max-width: 768px) {
            .reset-container {
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
    <div class="reset-container">
        <div class="illustration-side">
            <img src="images/cover.png" alt="Office illustration" style="max-width: 100%;">
        </div>
        
        <div class="reset-form-side">
            <div class="logo-container">
                <img src="images/poliness.png" alt="Polines Logo">
                <h2><span class="logo-text">CETAKU</span></h2>
            </div>
            
            <!-- Phase Indicator -->
            <div class="phase-indicator">
                <div class="phase-step active" id="step1">1</div>
                <div class="phase-step" id="step2">2</div>
                <div class="phase-step" id="step3">3</div>
            </div>
            
            <!-- Alert Messages -->
            <div id="alert-container"></div>
            
            <!-- Phase 1: Email Input -->
            <div id="phase1" class="phase-content">
                <h1 class="welcome-text">Reset Password</h1>
                <p class="subtitle-text">Masukkan email Anda untuk menerima kode verifikasi</p>
                
                <form id="emailForm">
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control" 
                               placeholder="Masukkan email Anda" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" id="sendOtpBtn">
                        Kirim Kode Verifikasi
                    </button>
                </form>
                
                <div class="back-link">
                    <a href="login.html">Kembali ke Login</a>
                </div>
            </div>
            
            <!-- Phase 2: OTP Input -->
            <div id="phase2" class="phase-content hidden">
                <h1 class="welcome-text">Verifikasi OTP</h1>
                <p class="subtitle-text">Masukkan 6 digit kode verifikasi yang telah dikirim ke email Anda</p>
                
                <div class="otp-input-group">
                    <input type="text" class="otp-input" maxlength="1" id="otp1">
                    <input type="text" class="otp-input" maxlength="1" id="otp2">
                    <input type="text" class="otp-input" maxlength="1" id="otp3">
                    <input type="text" class="otp-input" maxlength="1" id="otp4">
                    <input type="text" class="otp-input" maxlength="1" id="otp5">
                    <input type="text" class="otp-input" maxlength="1" id="otp6">
                </div>
                
                <div class="resend-otp">
                    <span class="timer" id="timer">Kirim ulang dalam 60 detik</span>
                    <button type="button" id="resendBtn" disabled>Kirim Ulang Kode</button>
                </div>
                
                <button type="button" class="btn btn-secondary" onclick="goToPhase(1)">
                    Kembali
                </button>
            </div>
            
            <!-- Phase 3: New Password -->
            <div id="phase3" class="phase-content hidden">
                <h1 class="welcome-text">Password Baru</h1>
                <p class="subtitle-text">Masukkan password baru Anda</p>
                
                <form id="passwordForm">
                    <div class="form-group">
                        <label class="form-label">Password Baru</label>
                        <div class="password-field">
                            <input type="password" name="password" id="newPassword" class="form-control" 
                                   placeholder="Masukkan password baru" required>
                            <span class="password-toggle" onclick="togglePassword('newPassword', 'toggleIcon1')">
                                <i class="fas fa-eye" id="toggleIcon1"></i>
                            </span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Konfirmasi Password</label>
                        <div class="password-field">
                            <input type="password" name="password_confirmation" id="confirmPassword" class="form-control" 
                                   placeholder="Konfirmasi password baru" required>
                            <span class="password-toggle" onclick="togglePassword('confirmPassword', 'toggleIcon2')">
                                <i class="fas fa-eye" id="toggleIcon2"></i>
                            </span>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" id="resetPasswordBtn">
                        Reset Password
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Global variables
        let currentPhase = 1;
        let userEmail = '';
        let countdown = 60;
        let countdownInterval;
        
        // Utility functions
        function togglePassword(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const toggleIcon = document.getElementById(iconId);
            
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
        
        function showAlert(message, type = 'danger') {
            const alertContainer = document.getElementById('alert-container');
            alertContainer.innerHTML = `
                <div class="alert alert-${type}" role="alert">
                    ${message}
                </div>
            `;
            
            setTimeout(() => {
                alertContainer.innerHTML = '';
            }, 5000);
        }
        
        function showLoading(buttonId) {
            const button = document.getElementById(buttonId);
            button.disabled = true;
            button.classList.add('btn-loading');
        }
        
        function hideLoading(buttonId) {
            const button = document.getElementById(buttonId);
            button.disabled = false;
            button.classList.remove('btn-loading');
        }
        
        function goToPhase(phase) {
            // Hide all phases
            for (let i = 1; i <= 3; i++) {
                document.getElementById(`phase${i}`).classList.add('hidden');
                document.getElementById(`step${i}`).classList.remove('active', 'completed');
            }
            
            // Show current phase
            document.getElementById(`phase${phase}`).classList.remove('hidden');
            document.getElementById(`step${phase}`).classList.add('active');
            
            // Mark previous phases as completed
            for (let i = 1; i < phase; i++) {
                document.getElementById(`step${i}`).classList.add('completed');
            }
            
            currentPhase = phase;
            document.getElementById('alert-container').innerHTML = '';
        }
        
        function startCountdown() {
            countdown = 60;
            const timerElement = document.getElementById('timer');
            const resendBtn = document.getElementById('resendBtn');
            
            resendBtn.disabled = true;
            
            countdownInterval = setInterval(() => {
                countdown--;
                timerElement.textContent = `Kirim ulang dalam ${countdown} detik`;
                
                if (countdown <= 0) {
                    clearInterval(countdownInterval);
                    timerElement.textContent = '';
                    resendBtn.disabled = false;
                }
            }, 1000);
        }
        
        // Phase 1: Email form
        document.getElementById('emailForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const email = document.getElementById('email').value;
            
            showLoading('sendOtpBtn');
            
            fetch('/api/forgot-password', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email })
            })
            .then(res => res.json().then(data => ({ status: res.status, body: data })))
            .then(({ status, body }) => {
                hideLoading('sendOtpBtn');
                
                if (status === 200) {
                    userEmail = email;
                    showAlert('Kode verifikasi telah dikirim ke email Anda', 'success');
                    goToPhase(2);
                    startCountdown();
                } else {
                    showAlert(body.message || 'Email tidak ditemukan dalam sistem kami');
                }
            })
            .catch(err => {
                hideLoading('sendOtpBtn');
                showAlert('Terjadi kesalahan saat mengirim OTP');
            });
        });
        
        // Phase 2: OTP Auto-verify
        const otpInputs = document.querySelectorAll('.otp-input');
        
        function setupOtpInputs() {
            otpInputs.forEach((input, index) => {
                input.addEventListener('input', function(e) {
                    const value = e.target.value;
                    
                    // Move to next input
                    if (value.length === 1 && index < otpInputs.length - 1) {
                        otpInputs[index + 1].focus();
                    }
                    
                    // Auto-verify when all inputs are filled
                    const otp = Array.from(otpInputs).map(input => input.value).join('');
                    if (otp.length === 6) {
                        verifyOtp(otp);
                    }
                });
                
                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Backspace' && e.target.value === '' && index > 0) {
                        otpInputs[index - 1].focus();
                    }
                });
            });
        }
        
        function verifyOtp(otp) {
            // Disable all inputs during verification
            otpInputs.forEach(input => input.disabled = true);
            
            fetch('/api/verify-otp', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email: userEmail, otp })
            })
            .then(res => res.json().then(data => ({ status: res.status, body: data })))
            .then(({ status, body }) => {
                if (status === 200) {
                    // Success animation
                    otpInputs.forEach(input => input.classList.add('success'));
                    showAlert('Kode verifikasi berhasil diverifikasi', 'success');
                    sessionStorage.setItem('verifiedOtp', otp);
                    clearInterval(countdownInterval);
                    
                    setTimeout(() => {
                        goToPhase(3);
                    }, 1000);
                } else {
                    // Reset inputs on error
                    otpInputs.forEach(input => {
                        input.value = '';
                        input.disabled = false;
                        input.classList.remove('success');
                    });
                    showAlert(body.message || 'Kode verifikasi tidak valid');
                    otpInputs[0].focus();
                }
            })
            .catch(() => {
                otpInputs.forEach(input => {
                    input.value = '';
                    input.disabled = false;
                    input.classList.remove('success');
                });
                showAlert('Terjadi kesalahan saat verifikasi OTP');
                otpInputs[0].focus();
            });
        }
        
        // Resend OTP
        document.getElementById('resendBtn').addEventListener('click', function() {
            showAlert('Kode verifikasi baru telah dikirim', 'success');
            startCountdown();
        });
        
        // Phase 3: Password form
        document.getElementById('passwordForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const otp = sessionStorage.getItem('verifiedOtp');
            
            if (newPassword.length < 8) {
                showAlert('Password minimal 8 karakter');
                return;
            }
            
            if (newPassword !== confirmPassword) {
                showAlert('Password dan konfirmasi password tidak cocok');
                return;
            }
            
            showLoading('resetPasswordBtn');
            
            fetch('/api/reset-password', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    email: userEmail,
                    otp,
                    password: newPassword,
                    password_confirmation: confirmPassword
                })
            })
            .then(res => res.json().then(data => ({ status: res.status, body: data })))
            .then(({ status, body }) => {
                hideLoading('resetPasswordBtn');
                
                if (status === 200) {
                    showAlert('Password berhasil direset! Silakan login.', 'success');
                    setTimeout(() => {
                        window.location.href = '/login';
                    }, 3000);
                } else {
                    showAlert(body.message || 'Gagal mereset password');
                }
            })
            .catch(() => {
                hideLoading('resetPasswordBtn');
                showAlert('Terjadi kesalahan saat reset password');
            });
        });
        
        // Initialize
        setupOtpInputs();
        goToPhase(1);
    </script>
</body>
</html>