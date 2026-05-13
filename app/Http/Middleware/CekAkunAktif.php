<?php

// ================================================================
//  app/Http/Middleware/CekAkunAktif.php
//
//  Middleware ini berjalan di setiap request yang membutuhkan auth.
//  Fungsinya: jika akun dinonaktifkan saat sedang login,
//  user akan otomatis di-logout dan diarahkan ke halaman login.
//
//  DAFTARKAN DI: bootstrap/app.php
//  $middleware->appendToGroup('web', CekAkunAktif::class);
// ================================================================

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CekAkunAktif
{
    public function handle(Request $request, Closure $next): Response
    {
        // Hanya jalankan jika user sedang login
        if (auth()->check() && !auth()->user()->is_aktif) {

            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->with('error', 'Akun Anda telah dinonaktifkan oleh administrator. Silakan hubungi admin apotek.');
        }

        return $next($request);
    }
}


// ================================================================
//  bootstrap/app.php
//
//  SALIN KE FILE: bootstrap/app.php
//  Ini adalah konfigurasi utama Laravel 11
// ================================================================
/*
<?php

use App\Http\Middleware\CekRole;
use App\Http\Middleware\CekAkunAktif;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // Daftarkan middleware CekRole dengan alias 'role'
        // Sehingga bisa dipakai di route: ->middleware('role:admin')
        $middleware->alias([
            'role' => CekRole::class,
        ]);

        // Jalankan CekAkunAktif di setiap request web yang login
        $middleware->appendToGroup('web', CekAkunAktif::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {

        // Handle 403 — akses ditolak
        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['pesan' => 'Akses ditolak.'], 403);
            }
            return redirect()->route('dashboard')
                             ->with('error', 'Anda tidak memiliki izin mengakses halaman tersebut.');
        });

        // Handle 404 — halaman tidak ditemukan
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['pesan' => 'Data tidak ditemukan.'], 404);
            }
            return response()->view('errors.404', [], 404);
        });
    })
    ->create();
*/
