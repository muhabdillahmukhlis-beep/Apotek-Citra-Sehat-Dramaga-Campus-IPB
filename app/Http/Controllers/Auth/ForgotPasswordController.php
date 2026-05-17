<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ForgotPasswordController extends Controller
{
    // 1. Tampilkan halaman input email
    public function showLinkRequestForm() {
        return view('auth.forgot-password');
    }

    // 2. Cek apakah email terdaftar, jika ya, langsung lempar ke halaman reset (Simulasi)
    public function sendResetLinkEmail(Request $request) {
        $request->validate(['email' => 'required|email']);
        
        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return back()->withErrors(['email' => 'Email tidak ditemukan di sistem kami.']);
        }

        // Untuk kebutuhan demo tugas, kita langsung arahkan ke halaman ganti password baru
        return redirect()->route('password.reset', ['token' => md5($request->email)]);
    }

    // 3. Tampilkan halaman form password baru
    public function showResetForm($token) {
        return view('auth.reset-password', ['token' => $token]);
    }

    // 4. Proses update password di database
    public function resetPassword(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'Email tidak valid.']);
        }

        // Update password baru (di-hash agar aman)
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('login')->with('status', 'Sandi berhasil diubah! Silakan login.');
    }
}