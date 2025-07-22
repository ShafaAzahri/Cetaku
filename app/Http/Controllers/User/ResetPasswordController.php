<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class ResetPasswordController extends Controller
{
    /**
     * Tampilkan form input email untuk lupa password
     */
    public function showEmailForm()
    {
        return view('auth.reset-password');
    }

    /**
     * Kirim OTP ke email user lewat API
     */
    public function submitEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $response = Http::post(url('/api/forgot-password'), [
            'email' => $request->email
        ]);

        if ($response->failed()) {
            return back()->withErrors(['email' => $response->json('message') ?? 'Gagal mengirim OTP']);
        }

        session([
            'reset_email' => $request->email
        ]);

        return redirect()->route('password.otp.form')->with('success', 'Kode OTP telah dikirim ke email Anda.');
    }

    /**
     * Tampilkan form verifikasi OTP
     */
    public function showOtpForm()
    {
        return view('auth.verify-otp');
    }

    /**
     * Verifikasi OTP lewat API
     */
    public function submitOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6'
        ]);

        $email = session('reset_email');

        $response = Http::post(url('/api/verify-otp'), [
            'email' => $email,
            'otp' => $request->otp
        ]);

        if ($response->failed()) {
            return back()->withErrors(['otp' => $response->json('message') ?? 'OTP salah atau kadaluarsa']);
        }

        session([
            'otp_verified' => true,
            'otp' => $request->otp
        ]);

        return redirect()->route('password.reset.form');
    }

    /**
     * Tampilkan form input password baru
     */
    public function showResetForm()
    {
        if (!session('otp_verified')) {
            return redirect()->route('password.email.form');
        }

        return view('auth.reset-password');
    }

    /**
     * Submit password baru ke API
     */
    public function submitReset(Request $request)
    {
        $request->validate([
            'password' => 'required|confirmed|min:6'
        ]);

        $email = session('reset_email');
        $otp = session('otp');

        if (!$email || !$otp) {
            return redirect()->route('password.email.form')->withErrors(['error' => 'Sesi OTP tidak valid.']);
        }

        $response = Http::post(url('/api/reset-password'), [
            'email' => $email,
            'otp' => $otp,
            'password' => $request->password,
            'password_confirmation' => $request->password_confirmation
        ]);

        if ($response->failed()) {
            return back()->withErrors(['password' => $response->json('message') ?? 'Gagal reset password']);
        }

        session()->forget(['reset_email', 'otp_verified', 'otp']);

        return redirect()->route('login')->with('success', 'Password berhasil diubah.');
    }
}
