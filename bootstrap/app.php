<?php

// =============================================================================
//  bootstrap/app.php
//
//  Ini adalah konfigurasi utama arsitektur aplikasi Laravel 11 / Laravel 12.
//  Tempat mendaftarkan rute, middleware global/alias, dan penanganan error.
// =============================================================================

use App\Http\Middleware\CekRole;
use App\Http\Middleware\CekAkunAktif;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // 🌟 PERBAIKAN & OPTIMALISASI ALIAS MIDDLEWARE
        // Mendaftarkan alias agar bisa dipanggil di routes/web.php secara fleksibel.
        // Contoh penggunaan: ->middleware(['auth', 'cek.akun.aktif', 'role:admin'])
        $middleware->alias([
            'role'           => CekRole::class,
            'cek.akun.aktif' => CekAkunAktif::class, 
        ]);

        // 💡 CATATAN ARSITEKTUR:
        // Baris $middleware->appendToGroup('web', CekAkunAktif::class); DIHAPUS.
        // Alasan: Agar pemeriksaan status akun dilakukan secara selektif pada rute 
        // yang membutuhkan autentikasi saja lewat route group di file web.php.
    })
    ->withExceptions(function (Exceptions $exceptions) {

        // =====================================================================
        // PENANGANAN EXCEPTION (ERROR HANDLING) GLOBAL
        // =====================================================================

        // 1. Handle HTTP 403 — Akses ditolak / Hak Akses Tidak Sesuai (Authorization Error)
        $exceptions->render(function (AuthorizationException $e, $request) {
            // Jika request meminta format JSON atau merupakan AJAX Call (misal: API/Datatables)
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'error', 
                    'pesan'  => 'Akses ditolak. Anda tidak memiliki wewenang mengakses data ini.'
                ], 403);
            }
            
            // Jika request web biasa, arahkan kembali ke dashboard dengan pesan peringatan
            return redirect()->route('dashboard')
                             ->with('error', 'Anda tidak memiliki izin mengakses halaman tersebut.');
        });

        // 2. Handle HTTP 404 — Halaman atau Data Model tidak ditemukan di database
        $exceptions->render(function (NotFoundHttpException $e, $request) {
            // Jika request meminta format JSON atau merupakan AJAX Call
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'error', 
                    'pesan'  => 'Data atau halaman yang Anda cari tidak ditemukan.'
                ], 404);
            }
            
            // Mengarahkan ke file kustom resources/views/errors/404.blade.php jika file tersebut ada
            if (view()->exists('errors.404')) {
                return response()->view('errors.404', [], 404);
            }
            
            return null; // Menggunakan halaman default 404 bawaan Laravel jika kostumisasi view tidak ditemukan
        });
    })
    ->create();