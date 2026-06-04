<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi; // Impor model Notifikasi agar log aktivitas tercatat
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
     * Memproses otentikasi masuk pengguna (Email atau Username).
     */
    public function login(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'email' => 'required', // Tetap menggunakan 'email' sesuai nama komponen di view HTML
            'password' => 'required',
        ]);

        // 2. Cek format input (Email atau Username)
        $loginField = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // 3. Susun data kredensial
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

    /**
     * Memproses pembaruan data PROFIL sekaligus PASSWORD pengguna (Terintegrasi).
     */
    public function updateProfilDanPassword(Request $request)
    {
        $user = Auth::user();

        // 1. Validasi Input Terintegrasi (Bahasa Indonesia kustom)
        $request->validate([
            'nama'              => 'required|string|max:255',
            // Pengecualian unique dilakukan dengan menambahkan ID user saat ini (,'.$user->id) agar tidak bentrok dengan datanya sendiri
            'username'          => 'required|string|alpha_dash|max:50|unique:users,username,' . $user->id,
            'email'             => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'no_telepon'        => 'nullable|string|max:15',
            'password_sekarang' => 'required', // Wajib diisi sebagai konfirmasi PIN keamanan utama
            'password_baru'     => 'nullable|string|min:6|confirmed', // 'nullable' karena boleh kosong jika profil saja yang diubah
        ], [
            'nama.required'              => 'Nama lengkap wajib diisi.',
            'username.required'          => 'Username wajib diisi.',
            'username.unique'            => 'Username ini sudah digunakan oleh staf lain.',
            'username.alpha_dash'        => 'Username hanya boleh berisi huruf, angka, strip, dan garis bawah.',
            'email.required'             => 'Alamat email wajib diisi.',
            'email.email'                => 'Format alamat email tidak valid.',
            'email.unique'               => 'Alamat email ini sudah terdaftar di sistem.',
            'password_sekarang.required' => 'Masukkan kata sandi saat ini untuk memverifikasi segala bentuk perubahan data.',
            'password_baru.min'          => 'Password baru minimal harus 6 karakter.',
            'password_baru.confirmed'    => 'Konfirmasi password baru tidak cocok.',
        ]);

        // 2. Verifikasi Keamanan: Apakah password lama yang dimasukkan benar?
        if (!Hash::check($request->password_sekarang, $user->password)) {
            return redirect()->back()
                ->withErrors(['password_sekarang' => 'Kata sandi saat ini yang Anda masukkan salah.'])
                ->withInput(); // Menjaga agar isian form yang sudah diketik tidak hilang
        }

        // 3. Antisipasi Skema Database (Deteksi dinamis kolom 'nama' atau 'name')
        $kolomNama = Schema::hasColumn('users', 'nama') ? 'nama' : 'name';

        // 4. Susun Dataset Perubahan Profil
        $updateData = [
            $kolomNama    => $request->nama,
            'username'    => strtolower($request->username),
            'email'       => $request->email,
            'no_telepon'  => $request->no_telepon,
        ];

        // 5. Logika Percabangan: Deteksi apakah pengguna juga berniat mengganti password?
        $isGantiPassword = false;
        if ($request->filled('password_baru')) {
            $updateData['password'] = Hash::make($request->password_baru);
            $isGantiPassword = true;
        }

        // 6. Eksekusi Perubahan Data ke Database
        $user->update($updateData);

        // 7. Automasi Log Trigger: Kirim Notifikasi Rekaman Jejak Aktivitas ke Sistem
        $namaUser = $request->nama;
        $roleUser = $user->role ?? 'staf';
        
        $pesanNotifikasi = 'Profil komponen akun untuk "' . $namaUser . '" (' . ucfirst($roleUser) . ') baru saja diperbarui melalui Pengaturan Sistem.';
        if ($isGantiPassword) {
            $pesanNotifikasi = 'Profil dan kata sandi baru untuk akun "' . $namaUser . '" (' . ucfirst($roleUser) . ') berhasil diperbarui melalui Pengaturan Sistem.';
        }

        Notifikasi::create([
            'kategori'  => 'sistem',
            'judul'     => $isGantiPassword ? 'Pembaruan keamanan & profil' : 'Profil akun diperbarui',
            'pesan'     => $pesanNotifikasi,
            'is_dibaca' => false
        ]);

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
        return redirect('/login');
    }
}