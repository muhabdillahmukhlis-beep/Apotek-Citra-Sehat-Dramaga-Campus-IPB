<?php

// ================================================================
//  app/Http/Middleware/CekRole.php
//
//  Middleware ini memastikan user yang login memiliki role
//  yang sesuai sebelum bisa mengakses suatu halaman.
//
//  CARA PAKAI DI ROUTES:
//
//  1. Satu role:
//     Route::get('/pengguna', ...)->middleware('role:admin');
//
//  2. Beberapa role (salah satu boleh):
//     Route::get('/laporan', ...)->middleware('role:admin,pemilik');
//
//  3. Di grup route:
//     Route::middleware(['auth', 'role:kasir,admin'])->group(function () {
//         Route::get('/transaksi/baru', ...);
//     });
//
//  DAFTARKAN DI: bootstrap/app.php
//  ->withMiddleware(function (Middleware $middleware) {
//      $middleware->alias(['role' => CekRole::class]);
//  })
// ================================================================

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CekRole
{
    /**
     * Jalankan middleware.
     *
     * @param Request $request  — request yang masuk
     * @param Closure $next     — lanjutkan ke handler berikutnya
     * @param string  ...$roles — role yang diizinkan akses
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Pastikan user sudah login
        if (!auth()->check()) {
            return redirect()
                ->route('login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = auth()->user();

        // Pastikan akun user aktif
        if (!$user->is_aktif) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->with('error', 'Akun Anda telah dinonaktifkan. Hubungi administrator.');
        }

        // Cek apakah role user ada di daftar role yang diizinkan
        if (in_array($user->role, $roles)) {
            return $next($request); // izinkan akses
        }

        // Role tidak cocok — tolak akses
        // Jika request AJAX/API, kembalikan JSON
        if ($request->expectsJson()) {
            return response()->json([
                'pesan' => 'Anda tidak memiliki izin untuk mengakses fitur ini.',
                'role'  => $user->role,
            ], 403);
        }

        // Jika request biasa, redirect ke halaman yang sesuai role-nya
        return redirect()
            ->route('dashboard')
            ->with('error', "Akses ditolak. Fitur ini hanya untuk: " . implode(', ', $roles) . ".");
    }
}
