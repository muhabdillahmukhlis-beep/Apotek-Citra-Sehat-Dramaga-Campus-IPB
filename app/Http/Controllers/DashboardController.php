<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Obat;
use App\Models\Notifikasi; // Impor model Notifikasi agar bisa mengambil data riil database
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Menampilkan Halaman Utama Dashboard beserta Statistik
     */
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
        $listStokMenipis = Obat::where('stok', '<=', 15) 
            ->orderBy('stok', 'asc')
            ->take(5)
            ->get();

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

    /**
     * Menampilkan Halaman Pusat Notifikasi Sistem secara Dinamis
     * (Mengambil data riil modifikasi user, password, stok, dan kadaluarsa dari database)
     */
    public function notifikasi()
    {
        // 1. Ambil data asli dari tabel database, urutkan dari yang paling baru (teranyar)
        $notifikasiDatabase = Notifikasi::latest()->get();

        // 2. Petakan data database tersebut agar sesuai dengan desain UI premium Anda
        $notifikasis = $notifikasiDatabase->map(function ($notif) {
            
            // Atur default ikon dan warna latar untuk Kategori: 'sistem'
            $icon = 'fa-solid fa-shield-halved';
            $icon_bg = 'bg-blue-50 text-blue-600 border border-blue-100/70';

            // Ubah penampilan secara dinamis jika kategorinya berbeda
            if ($notif->kategori === 'stok') {
                $icon = 'fa-solid fa-chart-pie';
                $icon_bg = 'bg-amber-50 text-amber-600 border border-amber-100/70';
            } elseif ($notif->kategori === 'kadaluarsa') {
                $icon = 'fa-solid fa-circle-exclamation';
                $icon_bg = 'bg-red-50 text-red-600 border border-red-100/70';
            }

            return [
                'id'       => $notif->id,
                'kategori' => $notif->kategori,
                'judul'    => $notif->judul,
                'pesan'    => $notif->pesan,
                'waktu'    => $notif->created_at->diffForHumans(), // Menghasilkan: "3 detik yang lalu", "10 menit yang lalu"
                'dibaca'   => $notif->is_dibaca,
                'icon'     => $icon,
                'icon_bg'  => $icon_bg
            ];
        });

        // 3. Kirimkan data terstruktur hasil olahan database ke halaman view blade
        return view('notifikasi.index', compact('notifikasis'));
    }

    /**
     * Menampilkan Halaman Pengaturan Sistem (Khusus Admin)
     */
    public function pengaturan()
    {
        return view('pengaturan.index');
    }
}