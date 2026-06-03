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
        // LENGKAP & INTEGRASI: Validasi pencegahan input nol, minus, atau kosong (TC_STOK_002)
        $request->validate([
            'obat_id' => 'required|exists:obat,id',
            'type'    => 'required|in:tambah,kurang',
            'jumlah'  => 'required|integer|min:1', // Mengunci nilai minimal wajib 1 (memblokir 0 dan negatif)
            'alasan'  => 'required|string|max:255',
            'batch'   => 'nullable|string|max:50',
        ], [
            // Pesan kesalahan kustom Bahasa Indonesia untuk UI SweetAlert2 Anda
            'obat_id.required' => 'Silahkan pilih item obat yang akan disesuaikan.',
            'obat_id.exists'   => 'Obat yang dipilih tidak terdaftar di database.',
            'type.required'    => 'Jenis penyesuaian (Tambah/Kurang) wajib ditentukan.',
            'type.in'          => 'Jenis penyesuaian harus bernilai tambah atau kurang.',
            'jumlah.required'  => 'Jumlah kuantitas stok wajib diisi.',
            'jumlah.integer'   => 'Jumlah penyesuaian stok harus berupa bilangan bulat.',
            'jumlah.min'       => 'Gagal! Kuantitas penyesuaian stok tidak boleh nol atau bernilai negatif (Minimal 1).',
            'alasan.required'  => 'Alasan penyesuaian stok wajib diisi untuk catatan log internal.',
            'alasan.max'       => 'Alasan terlalu panjang (maksimal 255 karakter).',
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
                // Cek validasi kecukupan stok sebelum dikurangi agar tidak menghasilkan nilai minus di DB
                if ($obat->stok < $jumlah) {
                    return back()->with('error', "Gagal! Stok obat {$obat->nama} saat ini hanya tersedia {$obat->stok} unit, tidak cukup jika dikurangi sebanyak {$jumlah} unit.");
                }
                $obat->stok -= $jumlah;
            }

            // Simpan perubahan ke tabel obat
            $obat->save();

            /** * CATATAN OPERASIONAL: 
             * Jika kelompok Anda memiliki tabel 'stok_log' atau 'riwayat_stok', 
             * baris untuk insert data log audit bisa diletakkan di bawah sini:
             * * DB::table('stok_log')->insert([
             * 'obat_id' => $obat->id,
             * 'user_id' => auth()->id(),
             * 'tipe' => $request->type,
             * 'jumlah' => $jumlah,
             * 'alasan' => $request->alasan,
             * 'created_at' => now()
             * ]);
             */
            
            DB::commit();
            return back()->with('success', "Stok obat {$obat->nama} berhasil diperbarui!");

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan sistem internal: ' . $e->getMessage());
        }
    }
}