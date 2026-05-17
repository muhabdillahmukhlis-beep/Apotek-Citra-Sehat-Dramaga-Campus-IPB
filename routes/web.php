<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\ObatController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\StokController;
use App\Http\Controllers\KadaluarsaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\ForgotPasswordController; // <-- DI SINI: Import Controller Baru

// --- REDIRECT UTAMA ---
Route::get('/', function () { 
    return redirect()->route('login'); 
});

// --- GUEST MIDDLEWARE (Hanya bisa diakses jika BELUM login) ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // --- DI SINI: Tambahan Rute Fitur Lupa Sandi ---
    // Halaman untuk masukkan email
    Route::get('/lupa-sandi', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/lupa-sandi', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

    // Halaman untuk ganti password baru
    Route::get('/reset-sandi/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-sandi', [ForgotPasswordController::class, 'resetPassword'])->name('password.update');
});

// --- LOGOUT ---
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- AUTH MIDDLEWARE (Hanya bisa diakses jika SUDAH login) ---
Route::middleware('auth')->group(function () {
    
    // DASHBOARD
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // --- MODUL PENJUALAN ---
    Route::prefix('transaksi')->name('transaksi.')->group(function () {
        Route::get('/riwayat', [TransaksiController::class, 'index'])->name('index');
        Route::get('/baru', [TransaksiController::class, 'create'])->name('create');
        Route::post('/simpan', [TransaksiController::class, 'store'])->name('store');
        
        Route::get('/export/pdf', [TransaksiController::class, 'exportPdf'])->name('export.pdf');
        Route::get('/export/excel', [TransaksiController::class, 'exportExcel'])->name('export.excel');
        
        Route::get('/{id}', [TransaksiController::class, 'showDetail'])->name('show');
        Route::get('/{id}/print', [TransaksiController::class, 'print'])->name('print');
    });

    // --- MODUL INVENTORI & OBAT ---
    Route::prefix('obat')->name('obat.')->group(function () {
        Route::post('/import', [ObatController::class, 'importExcel'])->name('import');
        Route::get('/export', [ObatController::class, 'exportExcel'])->name('export');
        Route::get('/expired', [KadaluarsaController::class, 'index'])->name('expired');
    });

    Route::resource('obat', ObatController::class)->parameters(['obat' => 'obat_id']);

    // --- MODUL STOK ---
    Route::prefix('stok')->name('stok.')->group(function () {
        Route::get('/', [StokController::class, 'index'])->name('index');
        Route::post('/update', [StokController::class, 'update'])->name('update');
    });
    
    Route::get('/obat', [ObatController::class, 'index'])->name('obat.index');
    
    // --- MODUL ANALITIK (LAPORAN) ---
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/', [LaporanController::class, 'index'])->name('index'); 
        Route::get('/export/pdf', [LaporanController::class, 'exportPdf'])->name('export.pdf');
        Route::get('/export/excel', [LaporanController::class, 'exportExcel'])->name('export.excel');
    });
});