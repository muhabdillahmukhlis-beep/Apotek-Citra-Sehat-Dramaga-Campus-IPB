<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman login.
     */
    public function showLogin()
    {
        return view('login');
    }

    /**
     * Memproses otentikasi masuk pengguna (Email atau Username secara hibrid).
     */
    public function login(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'email'    => 'required|string', // Menggunakan 'email' sesuai dengan name atribut di view HTML yang baru
            'password' => 'required|string',
        ], [
            'email.required'    => 'Kolom ID / Username / Email wajib diisi.',
            'password.required' => 'Kolom password wajib diisi.',
        ]);

        // 2. Cek format input (Otomatis mendeteksi Email atau Username)
        $loginField = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // 3. Susun data kredensial (Jika username, paksa ke lowercase agar serasi)
        $credentials = [
            $loginField => ($loginField === 'username') ? strtolower($request->email) : $request->email,
            'password'  => $request->password,
        ];

        // 4. Proses Otentikasi
        if (Auth::attempt($credentials, $request->has('remember'))) {
            
            // 🛡️ SECURITY FIX: Cegah pengguna yang berstatus NONAKTIF untuk masuk ke sistem
            if (!Auth::user()->is_aktif) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Akses ditolak: Akun Anda telah dinonaktifkan oleh Admin.',
                ])->onlyInput('email');
            }

            // Catat waktu login terakhir jika metodenya tersedia di model User
            if (method_exists(Auth::user(), 'catatLoginTerakhir')) {
                Auth::user()->catatLoginTerakhir();
            }

            $request->session()->regenerate();
            
            return redirect()->intended('dashboard');
        }

        // 5. Jika gagal, kirim pesan error
        return back()->withErrors([
            'email' => 'ID / Username / Email atau Password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    /**
     * Memproses pembaruan data PROFIL sekaligus PASSWORD pengguna (Terintegrasi).
     */
    public function updateProfilDanPassword(Request $request)
    {
        $user = Auth::user();

        // 🔥 PERBAIKAN SINKRONISASI: Ubah username ke lowercase sebelum validasi unique dijalankan
        if ($request->has('username')) {
            $request->merge(['username' => strtolower($request->username)]);
        }

        // 1. Validasi Input Terintegrasi
        $request->validate([
            'nama'              => 'required|string|max:255',
            'username'          => 'required|string|alpha_dash|max:50|unique:users,username,' . $user->id,
            'email'             => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'no_telepon'        => 'nullable|string|max:15',
            'password_sekarang' => 'required', 
            'password_baru'     => 'nullable|string|min:6|confirmed', 
        ], [
            'nama.required'              => 'Nama lengkap wajib diisi.',
            'username.required'          => 'Username wajib diisi.',
            'username.unique'            => 'Username ini sudah digunakan oleh staf lain.',
            'username.alpha_dash'        => 'Username hanya boleh berisi huruf, angka, strip, dan garis bawah.',
            'email.required'             => 'Alamat email wajib diisi.',
            'email.email'                => 'Format alamat email tidak valid.',
            'email.unique'               => 'Alamat email ini sudah terdaftar di sistem.',
            'password_sekarang.required' => 'Masukkan kata sandi saat ini untuk memverifikasi perubahan data.',
            'password_baru.min'          => 'Password baru minimal harus 6 karakter.',
            'password_baru.confirmed'    => 'Konfirmasi password baru tidak cocok.',
        ]);

        // 2. Verifikasi Keamanan: Apakah password lama benar?
        if (!Hash::check($request->password_sekarang, $user->password)) {
            return redirect()->back()
                ->withErrors(['password_sekarang' => 'Kata sandi saat ini yang Anda masukkan salah.'])
                ->withInput();
        }

        // 3. Susun Dataset Perubahan Profil (FIX: Dikunci langsung ke kolom 'nama')
        $updateData = [
            'nama'       => $request->nama,
            'username'   => $request->username,
            'email'      => $request->email,
            'no_telepon' => $request->no_telepon,
        ];

        // 4. Logika Percabangan: Cek apakah berniat mengganti password?
        $isGantiPassword = false;
        if ($request->filled('password_baru')) {
            $updateData['password'] = Hash::make($request->password_baru);
            $isGantiPassword = true;
        }

        // 5. Eksekusi Perubahan Data ke Database
        $user->update($updateData);

        // 6. Automasi Log Trigger: Kirim Notifikasi Rekaman Jejak Aktivitas ke Sistem
        $namaUser = $request->nama;
        $roleUser = $user->role ?? 'staf';
        
        $pesanNotifikasi = 'Profil komponen akun untuk "' . $namaUser . '" (' . ucfirst($roleUser) . ') baru saja diperbarui melalui Pengaturan Sistem.';
        if ($isGantiPassword) {
            $pesanNotifikasi = 'Profil dan kata sandi baru untuk akun "' . $namaUser . '" (' . ucfirst($roleUser) . ') berhasil diperbarui melalui Pengaturan Sistem.';
        }

        // 7. Input data ke tabel notifikasi
        if (Schema::hasTable('notifikasi')) {
            Notifikasi::create([
                'jenis'     => 'sistem', 
                'judul'     => $isGantiPassword ? 'Pembaruan keamanan & profil' : 'Profil akun diperbarui',
                'pesan'     => $pesanNotifikasi,
                'is_dibaca' => false
            ]);
        }

        // 8. Kembali ke halaman form pengaturan dengan membawa pesan sukses session
        return redirect()->back()->with('success', 'Profil dan pengaturan akun Anda berhasil diperbarui!');
    }

    /**
     * Memproses keluar dari sistem (Log out).
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Menggunakan route() dinamis agar diakui oleh Guest Middleware rute /login
        return redirect()->route('login')->with('status', 'Anda telah berhasil keluar dari sistem.');
    }
}