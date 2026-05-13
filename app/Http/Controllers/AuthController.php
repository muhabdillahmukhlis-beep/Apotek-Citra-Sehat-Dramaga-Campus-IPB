<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('login');
    }

    public function login(Request $request)
{
    // 1. Validasi input (hapus aturan 'email' agar teks biasa bisa masuk)
    $request->validate([
        'email' => 'required', // Kita tetap pakai nama 'email' sesuai name di form
        'password' => 'required',
    ]);

    // 2. Cek apakah yang diinput itu format email atau bukan
    // Jika input mengandung '@', kita anggap itu email. Jika tidak, itu username.
    $loginField = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

    // 3. Susun data kredensial untuk dicek ke database
    $credentials = [
        $loginField => $request->email,
        'password'  => $request->password,
    ];

    // 4. Proses Otentikasi
    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended('dashboard');
    }

    // 5. Jika gagal, kirim pesan error
    return back()->withErrors([
        'email' => 'Username/Email atau Password salah.',
    ])->onlyInput('email');
}

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
