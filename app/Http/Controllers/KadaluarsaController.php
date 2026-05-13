<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use Illuminate\Http\Request;
use Carbon\Carbon;

class KadaluarsaController extends Controller
{
    /**
     * Menampilkan halaman monitoring obat kadaluarsa.
     */
    public function index(Request $request)
    {
        // 1. Ambil input filter 'days', default 30 hari.
        $days = (int) $request->get('days', 30);

        // 2. Tentukan rentang waktu
        $today = Carbon::now()->startOfDay();
        $thresholdDate = Carbon::now()->addDays($days)->endOfDay();

        // 3. Ambil data obat yang akan kadaluarsa
        // Menggunakan with('kategori') untuk menghindari N+1 Query Problem
        $obats = Obat::with('kategori')
                     ->whereBetween('tgl_kadaluarsa', [$today, $thresholdDate])
                     ->orderBy('tgl_kadaluarsa', 'asc')
                     ->get();

        // 4. Pastikan path view sesuai dengan folder fisik Anda
        // Jika file ada di resources/views/monitoring/kadaluarsa.blade.php
        return view('monitoring.kadaluarsa', compact('obats', 'days'));
    }
}