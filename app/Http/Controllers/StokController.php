<?php

namespace App\Http\Controllers;

use App\Models\Obat; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StokController extends Controller
{
    /**
     * Menampilkan halaman Manajemen Stok
     */
    public function index()
    {
        // 1. Stats Cards - Menggunakan data riil dari database
        $totalJenis = Obat::count();
        $totalUnit = Obat::sum('stok');
        
        // Perbaikan: Gunakan kolom 'stok_minimum' dari DB, bukan angka statis 10
        $perluRestock = Obat::whereColumn('stok', '<=', 'stok_minimum')->count();

        // 2. Visualisasi Stok (Sisi Kiri)
        // Perbaikan: Ambil 10 obat dengan stok paling kritis (paling sedikit) 
        // agar relevan dengan kartu "Perlu Restock"
        $obatVisual = Obat::orderBy('stok', 'asc')
            ->take(10)
            ->get();

        // 3. Daftar Semua Obat (Dropdown Sisi Kanan)
        // Diurutkan berdasarkan nama agar mudah dicari oleh user
        $obatList = Obat::orderBy('nama', 'asc')->get();

        return view('stok.index', compact(
            'totalJenis', 
            'totalUnit', 
            'perluRestock', 
            'obatVisual', 
            'obatList'
        ));
    }

    /**
     * Memproses formulir penyesuaian stok
     */
    public function update(Request $request)
    {
        // Validasi input dengan pesan kustom jika perlu
        $request->validate([
            'obat_id' => 'required|exists:obat,id',
            'type'    => 'required|in:tambah,kurang',
            'jumlah'  => 'required|integer|min:1',
            'alasan'  => 'required|string|max:255',
            'batch'   => 'nullable|string|max:50',
        ]);

        try {
            DB::beginTransaction();

            // Menggunakan lockForUpdate untuk mencegah "race condition" 
            // jika dua admin mengupdate stok di waktu yang bersamaan
            $obat = Obat::where('id', $request->obat_id)->lockForUpdate()->firstOrFail();
            $jumlah = $request->jumlah;

            if ($request->type === 'tambah') {
                $obat->stok += $jumlah;
            } else {
                // Cek validasi stok sebelum dikurangi
                if ($obat->stok < $jumlah) {
                    return back()->with('error', "Gagal! Stok {$obat->nama} saat ini hanya {$obat->stok}, tidak cukup untuk dikurangi {$jumlah}.");
                }
                $obat->stok -= $jumlah;
            }

            // Simpan perubahan ke tabel obat
            $obat->save();

            /** * CATATAN: Sangat disarankan untuk menyimpan riwayat ini ke tabel 'stok_log' 
             * agar Anda bisa melacak siapa yang mengubah stok dan alasannya.
             */
            
            DB::commit();
            return back()->with('success', "Stok obat {$obat->nama} berhasil diperbarui!");

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}