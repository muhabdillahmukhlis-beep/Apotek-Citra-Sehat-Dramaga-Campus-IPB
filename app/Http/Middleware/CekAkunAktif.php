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
use Symfony\Component\HttpFoundation\Response;

class CekAkunAktif
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Hanya jalankan pengecekan jika user benar-benar sedang login
        if (auth()->check()) {
            $user = auth()->user();

            // 🛡️ FIX SECURITY: Gunakan array_key_exists atau pastikan field tidak null. 
            // Jangan gunakan isset() langsung pada model Eloquent karena nilai 0/false akan dianggap tidak diset.
            
            // Ambil status keaktifan dengan toleransi nama properti (is_aktif atau status)
            // Jika kolom tidak ditemukan sama sekali di database, kita default ke true agar tidak mengunci user.
            $isAktif = true;

            if (isset($user->getAttributes()['is_aktif'])) {
                $isAktif = (bool) $user->is_aktif;
            } elseif (isset($user->getAttributes()['status'])) {
                // Antisipasi jika nama kolom di database Anda menggunakan kata 'status'
                $isAktif = $user->status === 'aktif' || $user->status === 1 || $user->status === true;
            }

            // 2. Jika status akun adalah false (dinonaktifkan)
            if (!$isAktif) {
                
                // Hancurkan sesi login saat ini secara bersih
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                // Kembalikan ke halaman login dengan pesan peringatan error session
                // Menggunakan route('login') atau fallback URL jika name route tidak didefinisikan
                $redirectUrl = route()->has('login') ? redirect()->route('login') : redirect('/login');

                return $redirectUrl->with('error', 'Akun Anda telah dinonaktifkan oleh administrator. Silakan hubungi admin apotek.');
            }
        }

        return $next($request);
    }
}