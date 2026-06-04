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
use App\Http\Controllers\SettingSistemController; // 🌟 Controller Manajemen Sistem Apotek
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

// --- LOGOUT ---
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// =============================================================================
// --- AUTH MIDDLEWARE (Hanya bisa diakses jika SUDAH login) ---
// =============================================================================
Route::middleware(['auth', 'cek.akun.aktif'])->group(function () {
    
    // DASHBOARD (Semua pengguna yang sudah login bisa mengakses halaman utama ini)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // -------------------------------------------------------------------------
    // KELOMPOK ALL-ROLE: ADMIN, APOTEKER, DAN KASIR (Semua Level BISA MASUK)
    // -------------------------------------------------------------------------
    Route::middleware('role:admin,apoteker,kasir')->group(function () {
        
        // --- MODUL PENJUALAN (TRANSAKSI) ---
        // 🌟 PERBAIKAN: Menghapus variabel salah "$Route =" agar rute terbaca global
        Route::prefix('transaksi')->name('transaksi.')->group(function () {
            Route::get('/riwayat', [TransaksiController::class, 'index'])->name('index');
            Route::get('/baru', [TransaksiController::class, 'create'])->name('create');
            Route::post('/simpan', [TransaksiController::class, 'store'])->name('store');
            
            Route::get('/export/pdf', [TransaksiController::class, 'exportPdf'])->name('export.pdf');
            Route::get('/export/excel', [TransaksiController::class, 'exportExcel'])->name('export.excel');
            
            Route::get('/{id}', [TransaksiController::class, 'showDetail'])->name('show');
            Route::get('/{id}/print', [TransaksiController::class, 'print'])->name('print');
        });

        // --- MODUL PUSAT NOTIFIKASI DASBOR ---
        Route::get('/notifikasi', [DashboardController::class, 'notifikasi'])->name('notifikasi.index');

        // --- HALAMAN PENGATURAN PROFIL SAYA (UNTUK SEMUA ROLE) ---
        Route::get('/pengaturan', [DashboardController::class, 'pengaturan'])->name('pengaturan.index');
        Route::put('/pengaturan/update', [AuthController::class, 'updateProfilDanPassword'])->name('pengaturan.update');
    });

    // -------------------------------------------------------------------------
    // KELOMPOK 2: ADMIN & APOTEKER (Kasir AKAN DITOLAK)
    // -------------------------------------------------------------------------
    Route::middleware('role:admin,apoteker')->group(function () {
        
        // --- MODUL INVENTORI & OBAT ---
        Route::prefix('obat')->name('obat.')->group(function () {
            Route::post('/import', [ObatController::class, 'importExcel'])->name('import');
            Route::get('/export', [ObatController::class, 'exportExcel'])->name('export');
            Route::get('/expired', [KadaluarsaController::class, 'index'])->name('expired');
        });
        Route::resource('obat', ObatController::class)->parameters(['obat' => 'obat_id']);
        Route::get('/obat', [ObatController::class, 'index'])->name('obat.index');

        // --- MODUL STOK ---
        Route::prefix('stok')->name('stok.')->group(function () {
            Route::get('/', [StokController::class, 'index'])->name('index');
            Route::post('/update', [StokController::class, 'update'])->name('update');
        });
    });

    // -------------------------------------------------------------------------
    // KELOMPOK 3: KHUSUS ADMIN (Apoteker & Kasir AKAN DITOLAK)
    // -------------------------------------------------------------------------
    Route::middleware('role:admin')->group(function () {
        
        // --- MODUL MANAJEMEN PENGGUNA (USER) ---
        Route::get('/user', [UserController::class, 'index'])->name('user.index');
        Route::post('/user/simpan', [UserController::class, 'store'])->name('user.store');
        Route::patch('/user/{id}/status', [UserController::class, 'toggleStatus'])->name('user.status');
        Route::delete('/user/{id}', [UserController::class, 'destroy'])->name('user.destroy');

        // --- MODUL ANALITIK (LAPORAN) ---
        Route::prefix('laporan')->name('laporan.')->group(function () {
            Route::get('/', [LaporanController::class, 'index'])->name('index'); 
            Route::get('/export/pdf', [LaporanController::class, 'exportPdf'])->name('export.pdf');
            Route::get('/export/excel', [LaporanController::class, 'exportExcel'])->name('export.excel');
        });

        // 🌟 LENGKAPI: KELOMPOK MODUL KONFIGURASI SISTEM APOTEK 🌟
        // Menambahkan rute agar tombol sidebar Keamanan, Struk, Backup, Log, & Notifikasi aktif murni.
        Route::prefix('pengaturan')->name('pengaturan.')->group(function () {
            // Info Apotek
            Route::get('/sistem', [SettingSistemController::class, 'edit'])->name('sistem.index');
            Route::put('/sistem', [SettingSistemController::class, 'update'])->name('sistem.update');
            
            // Keamanan
            Route::get('/keamanan', [SettingSistemController::class, 'keamanan'])->name('keamanan');
            
            // Format Struk Printer
            Route::get('/format-struk', [SettingSistemController::class, 'struk'])->name('struk');
            
            // Backup & Restore Database
            Route::get('/backup-database', [SettingSistemController::class, 'backup'])->name('backup');
            
            // Log Audit Aksi Operator
            Route::get('/log-audit', [SettingSistemController::class, 'logAudit'])->name('log_audit');
            
            // Kebijakan Alert Notifikasi
            Route::get('/notifikasi-sistem', [SettingSistemController::class, 'notifikasi'])->name('notifikasi');
        });
        
    });

}); // Penutup akhir file routes