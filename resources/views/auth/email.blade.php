<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f9f9f9; color: #333; }
        .container { max-width: 600px; margin: 30px auto; background: white; padding: 20px; border-radius: 8px; }
        .code { font-size: 32px; font-weight: bold; color: #0057ff; margin: 20px 0; text-align: center; }
        .footer { font-size: 12px; color: #888; text-align: center; margin-top: 20px; }
    </style>
</head>
<body>
<div class="container">
    <h2>Reset Password Akun Anda</h2>
    <p>Halo, {{ $user->nama ?? $user->name ?? 'Pengguna' }},</p>
    <p>Kami menerima permintaan untuk mereset password akun Anda. Silakan gunakan kode OTP berikut untuk melanjutkan proses reset password:</p>
    <div class="code">{{ $otp }}</div>
    <p><b>Catatan:</b> Kode ini hanya berlaku selama 5 menit.</p>
    <p>Jika Anda tidak merasa meminta reset password, Anda dapat mengabaikan email ini.</p>
    <p>Terima kasih,<br>Tim Cetaku</p>
    <div class="footer">
        &copy; {{ date('Y') }} Cetaku. All rights reserved.
    </div>
</div>
</body>
</html>
