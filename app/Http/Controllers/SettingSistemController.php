<?php

namespace App\Http\Controllers;

use App\Models\PengaturanSistem;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class SettingSistemController extends Controller
{
    /**
     * Menampilkan halaman konfigurasi sistem apotek (Info Apotek) dengan data dari database.
     * * @return \Illuminate\Contracts\View\View
     */
    public function edit(): View
    {
        // Ambil data baris pertama dari tabel pengaturan_sistem
        $pengaturan = PengaturanSistem::first();

        // Jika data seeder belum berjalan atau terhapus secara tidak sengaja,
        // sediakan objek dengan nilai default agar View tidak error membaca properti
        if (!$pengaturan) {
            $pengaturan = new PengaturanSistem([
                'nama_apotek' => 'Apotek Citra Sehat',
                'lokasi_unit' => 'IPB Dramaga Campus',
                'stok_minimum' => 10,
                'hari_kadaluarsa' => 30
            ]);
        }

        // Mengembalikan view pengaturan sistem (Info Apotek)
        return view('pengaturan.sistem', compact('pengaturan'));
    }

    /**
     * Memproses pembaruan data konfigurasi ambang batas sistem.
     * * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request): RedirectResponse
    {
        // 1. Validasi Input Form (Bahasa Indonesia)
        $request->validate([
            'stok_minimum'    => 'required|integer|min:0',
            'hari_kadaluarsa' => 'required|integer|min:1|max:365',
        ], [
            'stok_minimum.required'    => 'Batas minimum stok kritis wajib diisi.',
            'stok_minimum.integer'     => 'Batas minimum stok harus berupa angka.',
            'stok_minimum.min'         => 'Batas minimum stok tidak boleh kurang dari 0.',
            'hari_kadaluarsa.required' => 'Pengingat hari kadaluarsa wajib diisi.',
            'hari_kadaluarsa.integer'  => 'Pengingat hari kadaluarsa harus berupa angka.',
            'hari_kadaluarsa.min'      => 'Pengingat minimal adalah 1 hari.',
            'hari_kadaluarsa.max'      => 'Pengingat maksimal adalah 365 hari (1 tahun).',
        ]);

        // 2. Gunakan Database Transaction untuk Keamanan Data multi-tabel
        DB::beginTransaction();

        try {
            // Ambil data pengaturan yang ada di database
            $pengaturan = PengaturanSistem::first();

            // Antisipasi jika data baris pertama belum ada di DB
            if (!$pengaturan) {
                $pengaturan = new PengaturanSistem();
            }

            // Update data pengaturan ke database
            $pengaturan->stok_minimum = $request->stok_minimum;
            $pengaturan->hari_kadaluarsa = $request->hari_kadaluarsa;
            $pengaturan->save();

            // 3. AUTOMATIC TRIGGER: Catat perubahan kebijakan sistem ke tabel Notifikasi / Log Audit
            $namaUser = auth()->user()->nama ?? auth()->user()->name ?? 'Administrator';
            
            $notif = new Notifikasi();
            $notif->kategori  = 'sistem';
            $notif->judul     = 'Konfigurasi sistem diubah';
            $notif->pesan     = 'Kebijakan sistem diperbarui oleh ' . $namaUser . '. Batas stok kritis disesuaikan menjadi ' . $request->stok_minimum . ' unit dan ambang batas kadaluarsa menjadi ' . $request->hari_kadaluarsa . ' hari.';
            $notif->is_dibaca = false;
            $notif->save();

            // Commit transaksi jika kedua proses di atas berhasil tanpa error
            DB::commit();

            // 4. Kembali ke form dengan membawa pesan sukses session
            return redirect()->back()->with('success', 'Konfigurasi sistem Apotek Citra Sehat berhasil diperbarui!');

        } catch (Exception $e) {
            // Batalkan semua perubahan jika salah satu proses SQL gagal
            DB::rollBack();

            // Catat error asli ke file storage/logs/laravel.log untuk mempermudah debugging developer
            Log::error('Gagal memperbarui konfigurasi sistem: ' . $e->getMessage());

            // Kembalikan pengguna ke halaman dengan pesan error kegagalan sistem
            return redirect()->back()
                ->withInput()
                ->withErrors(['sistem' => 'Gagal menyimpan perubahan konfigurasi akibat gangguan pada database.']);
        }
    }

    // =============================================================================
    // 🌟 LENGKAPI: METHOD TAMBAHAN UNTUK INTEGRASI SIDEBAR SEBELUMNYA
    // =============================================================================

    /**
     * Menampilkan halaman pengaturan Keamanan Sistem.
     * * @return \Illuminate\Contracts\View\View
     */
    public function keamanan(): View
    {
        return view('pengaturan.keamanan');
    }

    /**
     * Menampilkan halaman pengaturan Format Struk Printer.
     * * @return \Illuminate\Contracts\View\View
     */
    public function struk(): View
    {
        return view('pengaturan.struk');
    }

    /**
     * Menampilkan halaman manajemen Backup & Restore Database.
     * * @return \Illuminate\Contracts\View\View
     */
    public function backup(): View
    {
        return view('pengaturan.backup');
    }

    /**
     * Menampilkan halaman Log Audit aktivitas operator.
     * * @return \Illuminate\Contracts\View\View
     */
    public function logAudit(): View
    {
        return view('pengaturan.log_audit');
    }

    /**
     * Menampilkan halaman konfigurasi Alert Notifikasi Sistem.
     * * @return \Illuminate\Contracts\View\View
     */
    public function notifikasi(): View
    {
        return view('pengaturan.notifikasi');
    }
}