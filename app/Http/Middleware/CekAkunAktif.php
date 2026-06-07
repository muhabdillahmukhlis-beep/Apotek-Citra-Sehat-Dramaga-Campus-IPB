<?php

// ================================================================
//  app/Http/Middleware/CekAkunAktif.php
//
//  Middleware ini berjalan di setiap request yang membutuhkan auth.
//  Fungsinya: jika akun dinonaktifkan saat sedang login,
//  user akan otomatis di-logout dan diarahkan ke halaman login.
// ================================================================

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route; 
use Symfony\Component\HttpFoundation\Response;

class CekAkunAktif
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Hanya jalankan pengecekan jika user benar-benar sedang login
        if (auth()->check()) {
            $user = auth()->user();

            // 🟢 PERBAIKAN MUTLAK: Ambil nilai mentah langsung dari engine Eloquent
            $dbIsAktif = $user->getAttribute('is_aktif');
            $dbStatus  = $user->getAttribute('status');

            // Set default ke true (dianggap aktif jika kolom tidak ditemukan)
            $isAktif = true;

            // Pengecekan Kondisi Kolom 1: Jika menggunakan 'is_aktif'
            if ($dbIsAktif !== null) {
                // Konversi string '0', angka 0, atau false menjadi boolean false secara tegas
                $isAktif = ($dbIsAktif == 1 || $dbIsAktif === true || $dbIsAktif == '1');
            } 
            // Pengecekan Kondisi Kolom 2: Jika menggunakan 'status'
            elseif ($dbStatus !== null) {
                $statusText = strtolower(trim($dbStatus));
                $isAktif = ($statusText === 'aktif' || $statusText === 'active' || $dbStatus == 1 || $dbStatus === true);
            }

            // 2. Jika status akun adalah tidak aktif (dinonaktifkan oleh sistem)
            if (!$isAktif) {
                
                // Hancurkan sesi login saat ini secara bersih dan menyeluruh
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                // Cari rute login secara aman
                $redirectUrl = Route::has('login') ? redirect()->route('login') : redirect('/login');

                return $redirectUrl->with('error', 'Akun Anda mendeteksi perubahan status keaktifan. Silakan hubungi admin apotek.');
            }
        }

        return $next($request);
    }
}