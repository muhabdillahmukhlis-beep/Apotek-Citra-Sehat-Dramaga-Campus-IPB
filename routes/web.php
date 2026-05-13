<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\ObatController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\StokController;
use App\Http\Controllers\KadaluarsaController;
use App\Http\Controllers\DashboardController; // <-- Tambahan Import

// --- REDIRECT UTAMA ---
Route::get('/', function () { 
    return redirect()->route('login'); 
});

// --- GUEST MIDDLEWARE ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- AUTH MIDDLEWARE ---
Route::middleware('auth')->group(function () {
    
    // DASHBOARD (Sudah Diperbaiki)
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