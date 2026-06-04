<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CekRole
{
    /**
     * Jalankan middleware keamanan tingkatan Level (Role).
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // 1. Pastikan user sudah login
        if (!auth()->check()) {
            return redirect()
                ->route('login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = auth()->user();

        // 2. Normalisasi teks role (Ubah ke huruf kecil & buang spasi kosong)
        // Mencegah error akibat perbedaan penulisan seperti 'Admin', 'ADMIN', atau 'admin '
        $userRole = strtolower(trim($user->role));
        $allowedRoles = array_map(function ($role) {
            return strtolower(trim($role));
        }, $roles);

        // Cek apakah role user ada di daftar role yang diizinkan
        if (in_array($userRole, $allowedRoles)) {
            return $next($request); // Izinkan akses ke halaman
        }

        // 3. Role tidak cocok — Tolak akses
        // Jika request berupa AJAX / API / Axios, kembalikan response JSON
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status'  => 'error',
                'pesan'   => 'Anda tidak memiliki izin untuk mengakses fitur ini.',
                'current_role' => $user->role,
            ], 403);
        }

        // Jika request halaman biasa, kembalikan ke dashboard dengan pesan error
        return redirect()
            ->route('dashboard')
            ->with('error', "Akses ditolak. Fitur ini hanya dapat diakses oleh: " . implode(', ', $roles) . ".");
    }
}