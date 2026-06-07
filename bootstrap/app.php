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
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // 🌟 ALIAS MIDDLEWARE
        $middleware->alias([
            'role'           => CekRole::class,
            'cek.akun.aktif' => CekAkunAktif::class, 
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

        // =====================================================================
        // PENANGANAN EXCEPTION (ERROR HANDLING) GLOBAL
        // =====================================================================

        // 1. Handle HTTP 403 — Akses ditolak / Hak Akses Tidak Sesuai (Authorization Error)
        $exceptions->render(function (AuthorizationException $e, $request) {
            
            // 🌟 MASTER OVERRIDE PRIVILEGE UNTUK PEMILIK:
            // Jika yang mengakses adalah 'pemilik', bypass proteksi Form Request / Policy 
            // dan izinkan dia melanjutkan request ke Controller secara paksa.
            if (auth()->check() && strtolower(trim(auth()->user()->role)) === 'pemilik') {
                return null; 
            }

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

        // 🌟 HANDLE JUGA ACCESS DENIED EXCEPTION UMUM
        $exceptions->render(function (AccessDeniedHttpException $e, $request) {
            if (auth()->check() && strtolower(trim(auth()->user()->role)) === 'pemilik') {
                return null; 
            }
        });

        // 2. Handle HTTP 404 — Halaman atau Data Model tidak ditemukan di database
        $exceptions->render(function (NotFoundHttpException $e, $request) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'error', 
                    'pesan'  => 'Data atau halaman yang Anda cari tidak ditemukan.'
                ], 404);
            }
            
            if (view()->exists('errors.404')) {
                return response()->view('errors.404', [], 404);
            }
            
            return null;
        });
    })
    ->create();