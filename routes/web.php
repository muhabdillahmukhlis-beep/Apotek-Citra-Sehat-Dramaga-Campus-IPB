<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\ObatController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\StokController;
use App\Http\Controllers\KadaluarsaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController; 
use App\Http\Controllers\SettingSistemController; 
use App\Http\Controllers\Auth\ForgotPasswordController;

// --- REDIRECT UTAMA ---
Route::get('/', function () { 
    return redirect()->route('login'); 
});

// --- GUEST MIDDLEWARE (Hanya bisa diakses jika BELUM login) ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // --- FITUR LUPA SANDI ---
    Route::get('/lupa-sandi', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/lupa-sandi', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-sandi/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-sandi', [ForgotPasswordController::class, 'resetPassword'])->name('password.update');
});

// --- LOGOUT (Menggunakan POST untuk menghindari Error 419) ---
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// =============================================================================
// --- AUTH MIDDLEWARE (Hanya bisa diakses jika SUDAH login) ---
// =============================================================================
Route::middleware(['auth', 'cek.akun.aktif'])->group(function () {
    
    // DASHBOARD & NOTIFIKASI GLOBAL (Akses Terbuka untuk Semua Role)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/notifikasi', [DashboardController::class, 'notifikasi'])->name('notifikasi.index');
    
    // HALAMAN PENGATURAN PROFIL MANDIRI (Akses Terbuka untuk Semua Role)
    Route::get('/pengaturan', [DashboardController::class, 'pengaturan'])->name('pengaturan.index');
    Route::put('/pengaturan/update', [AuthController::class, 'updateProfilDanPassword'])->name('pengaturan.update');

    // -------------------------------------------------------------------------
    // KELOMPOK PROTEKSI: MODUL CONFIG SISTEM (KHUSUS ADMIN & PEMILIK)
    // -------------------------------------------------------------------------
    Route::middleware('role:admin,pemilik')->group(function () {
        Route::prefix('pengaturan-sistem')->group(function () {
            // Info Apotek (Gunakan name 'sistem.index' agar sinkron dengan sidebar utama)
            Route::get('sistem', [SettingSistemController::class, 'edit'])->name('sistem.index');
            Route::put('sistem', [SettingSistemController::class, 'update'])->name('sistem.update');
            
            // Sub-Menu Pengaturan (Gunakan prefix 'pengaturan.' agar sinkron dengan sub-sidebar)
            Route::name('pengaturan.')->group(function () {
                Route::get('keamanan', [SettingSistemController::class, 'keamanan'])->name('keamanan');
                Route::get('format-struk', [SettingSistemController::class, 'struk'])->name('struk');
                Route::get('backup-database', [SettingSistemController::class, 'backup'])->name('backup');
                Route::get('log-audit', [SettingSistemController::class, 'logAudit'])->name('log_audit');
                Route::get('notifikasi-sistem', [SettingSistemController::class, 'notifikasi'])->name('notifikasi');
            });
        });
    });

    // -------------------------------------------------------------------------
    // KELOMPOK PROTEKSI: MODUL PENJUALAN (ADMIN, PEMILIK, KASIR - Apoteker Dilarang)
    // -------------------------------------------------------------------------
    Route::middleware('role:admin,pemilik,kasir')->group(function () {
        Route::prefix('transaksi')->name('transaksi.')->group(function () {
            Route::get('riwayat', [TransaksiController::class, 'index'])->name('index');
            Route::get('baru', [TransaksiController::class, 'create'])->name('create'); 
            Route::post('simpan', [TransaksiController::class, 'store'])->name('store');
            
            Route::get('export/pdf', [TransaksiController::class, 'exportPdf'])->name('export.pdf');
            Route::get('export/excel', [TransaksiController::class, 'exportExcel'])->name('export.excel');
            
            Route::get('{id}', [TransaksiController::class, 'showDetail'])->name('show');
            Route::get('{id}/print', [TransaksiController::class, 'print'])->name('print');
        });
    });

    // -------------------------------------------------------------------------
    // KELOMPOK PROTEKSI: MANAJEMEN BARANG (ADMIN, PEMILIK, APOTEKER - Kasir Dilarang)
    // -------------------------------------------------------------------------
    Route::middleware('role:admin,pemilik,apoteker')->group(function () {
        // --- MODUL INVENTORI & OBAT ---
        Route::prefix('obat')->name('obat.')->group(function () {
            Route::post('import', [ObatController::class, 'importExcel'])->name('import');
            Route::get('export', [ObatController::class, 'exportExcel'])->name('export');
            Route::get('expired', [KadaluarsaController::class, 'index'])->name('expired');
        });
        
        // Resource diletakkan di bawah rute spesifik agar rute 'obat/expired' tidak dikira ID obat
        Route::resource('obat', ObatController::class)->parameters(['obat' => 'obat_id']);

        // --- MODUL MANAJEMEN STOK ---
        Route::prefix('stok')->name('stok.')->group(function () {
            Route::get('/', [StokController::class, 'index'])->name('index');
            Route::post('update', [StokController::class, 'update'])->name('update');
        });
    });

    // -------------------------------------------------------------------------
    // KELOMPOK STRATEGIS & DATA MASTER (KHUSUS ADMIN & PEMILIK)
    // -------------------------------------------------------------------------
    Route::middleware('role:admin,pemilik')->group(function () {
        // --- MODUL ANALITIK (LAPORAN) ---
        Route::prefix('laporan')->name('laporan.')->group(function () {
            Route::get('/', [LaporanController::class, 'index'])->name('index'); 
            Route::get('export/pdf', [LaporanController::class, 'exportPdf'])->name('export.pdf');
            Route::get('export/excel', [LaporanController::class, 'exportExcel'])->name('export.excel');
        });

        // --- MODUL MANAJEMEN PENGGUNA (USER) ---
        Route::get('/user', [UserController::class, 'index'])->name('user.index');
        Route::post('/user', [UserController::class, 'store'])->name('user.store');
        Route::patch('/user/{id}/status', [UserController::class, 'toggleStatus'])->name('user.status')->where('id', '[0-9]+');
        Route::delete('/user/{id}', [UserController::class, 'destroy'])->name('user.destroy')->where('id', '[0-9]+');
    });

}); // Akhir penutup middleware auth