<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use App\Mail\ResetPasswordMail; // Tambahkan ini di atas
use App\Models\User;

class ForgotPasswordApiController extends Controller
{
    /**
     * Kirim kode OTP ke email user
     */
    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'Email tidak ditemukan'], 404);
        }

        $otp = random_int(100000, 999999);

        $user->update([
            'reset_code' => $otp,
            'reset_code_expires_at' => now()->addMinutes(5)
        ]);

        // Kirim email menggunakan Blade
        Mail::to($user->email)->send(new ResetPasswordMail($user, $otp));

        return response()->json(['message' => 'Kode OTP telah dikirim ke email']);
    }

    /**
     * Verifikasi OTP yang dimasukkan user
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'Email tidak ditemukan'], 404);
        }

        if (
            $user->reset_code !== $request->otp ||
            !$user->reset_code_expires_at ||
            now()->greaterThan($user->reset_code_expires_at)
        ) {
            return response()->json(['message' => 'Kode OTP salah atau kadaluarsa'], 422);
        }

        return response()->json(['message' => 'OTP valid, lanjutkan reset password']);
    }

    /**
     * Simpan password baru user
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required',
            'password' => 'required|confirmed|min:6'
        ]);

        $user = User::where('email', $request->email)->first();

        if (
            !$user ||
            $user->reset_code !== $request->otp ||
            now()->greaterThan($user->reset_code_expires_at)
        ) {
            return response()->json(['message' => 'OTP tidak valid atau kadaluarsa'], 422);
        }

        $user->update([
            'password' => Hash::make($request->password),
            'reset_code' => null,
            'reset_code_expires_at' => null
        ]);

        return response()->json(['message' => 'Password berhasil direset']);
    }
}

