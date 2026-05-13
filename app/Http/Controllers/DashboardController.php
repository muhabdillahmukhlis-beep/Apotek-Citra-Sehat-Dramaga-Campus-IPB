<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Obat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Set locale agar nama hari dalam Bahasa Indonesia
        Carbon::setLocale('id');

        // 1. Data Statistik Atas
        $penjualanHariIni = Transaksi::whereDate('created_at', Carbon::today())
            ->where('status', 'selesai')
            ->sum('total');
            
        $totalTransaksi = Transaksi::whereDate('created_at', Carbon::today())
            ->where('status', 'selesai')
            ->count();
            
        $stokMenipisCount = Obat::where('stok', '<=', 10)->count();
        
        $hampirKadaluarsaCount = Obat::whereBetween('tgl_kadaluarsa', [
            Carbon::now(), 
            Carbon::now()->addDays(30)
        ])->count();

        // 2. Data Grafik (7 Hari Terakhir)
        $grafikData = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $formattedDate = $date->format('Y-m-d');
            
            $total = Transaksi::whereDate('created_at', $formattedDate)
                ->where('status', 'selesai')
                ->sum('total');
            
            $grafikData->push([
                'label' => $date->isoFormat('ddd'), // Format: Sen, Sel, Rab, dst.
                'total' => $total
            ]);
        }

        // 3. List Stok Menipis (Sidebar)
       // Ganti bagian ini (Baris 51-54)
$listStokMenipis = Obat::where('stok', '<=', 15) 
    ->orderBy('stok', 'asc')
    ->take(5)
    ->get(); // Hapus isian di dalam get() agar tidak error

        // 4. Riwayat Transaksi Terbaru (Tabel)
        $transaksiTerbaru = Transaksi::with('kasir')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'penjualanHariIni', 
            'totalTransaksi', 
            'stokMenipisCount', 
            'hampirKadaluarsaCount', 
            'listStokMenipis', 
            'transaksiTerbaru', 
            'grafikData'
        ));
    }
}