<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UserController extends Controller
{
    /**
     * Helper privat untuk mendeteksi kolom nama pengguna secara defensif.
     */
    private function getUserNameField(): string
    {
        return Schema::hasColumn('users', 'nama') ? 'nama' : 'name';
    }

    /**
     * Menampilkan halaman Manajemen Pengguna dan Matriks Hak Akses.
     */
    public function index(Request $request)
    {
        // 1. Mengambil seluruh data pengguna diurutkan secara A-Z berdasarkan kolom yang tersedia
        $nameField = $this->getUserNameField();
        $users = User::orderBy($nameField, 'asc')->get();

        // 2. 🌟 PERBAIKAN MATRIKS: Mengubah hak akses Pemilik menjadi 'true' pada semua lini fitur strategis
        $matriksHakAkses = [
            ['fitur' => 'Dashboard', 'admin' => true, 'kasir' => true, 'apoteker' => true, 'pemilik' => true],
            ['fitur' => 'Data Obat (CRUD)', 'admin' => true, 'kasir' => false, 'apoteker' => true, 'pemilik' => true],
            ['fitur' => 'Transaksi Penjualan', 'admin' => true, 'kasir' => true, 'apoteker' => false, 'pemilik' => true],
            ['fitur' => 'Riwayat Transaksi', 'admin' => true, 'kasir' => true, 'apoteker' => false, 'pemilik' => true],
            ['fitur' => 'Manajemen Stok', 'admin' => true, 'kasir' => false, 'apoteker' => true, 'pemilik' => true],
            ['fitur' => 'Monitoring Kadaluarsa', 'admin' => true, 'kasir' => false, 'apoteker' => true, 'pemilik' => true],
            ['fitur' => 'Laporan & Analitik', 'admin' => true, 'kasir' => false, 'apoteker' => false, 'pemilik' => true],
            ['fitur' => 'Manajemen Pengguna', 'admin' => true, 'kasir' => false, 'apoteker' => false, 'pemilik' => true],
            ['fitur' => 'Pengaturan Sistem', 'admin' => true, 'kasir' => false, 'apoteker' => false, 'pemilik' => true],
        ];

        // 3. Mengirimkan data pengguna dan struktur matriks ke view blade
        return view('user.index', [
            'users'           => $users,
            'matriksHakAkses' => $matriksHakAkses
        ]);
    }

    /**
     * Menyimpan pengguna baru ke dalam database.
     */
    public function store(Request $request)
    {
        // Perbaikan: Ubah username ke lowercase SEBELUM validasi untuk mencegah bypass case-sensitive
        if ($request->has('username')) {
            $request->merge(['username' => strtolower($request->username)]);
        }

        // 1. Validasi Input Form
        $request->validate([
            'nama'       => 'required|string|max:255',
            'username'   => 'required|string|alpha_dash|max:50|unique:users,username',
            'email'      => 'required|string|email|max:255|unique:users,email',
            'password'   => 'required|string|min:6',
            'role'       => 'required|in:admin,kasir,apoteker,pemilik',
            'no_telepon' => 'nullable|string|max:15',
        ], [
            'username.unique' => 'Username ini sudah digunakan oleh staf lain.',
            'email.unique'    => 'Alamat email ini sudah terdaftar.',
            'password.min'    => 'Password minimal harus 6 karakter.',
        ]);

        // 2. Simpan Data ke Database dengan penanganan kolom dinamis (nama/name)
        $nameField = $this->getUserNameField();
        
        $userData = [
            'username'   => $request->username,
            'email'      => $request->email,
            'password'   => Hash::make($request->password), 
            'role'       => $request->role,
            'no_telepon' => $request->no_telepon,
            'is_aktif'   => true, 
            $nameField   => $request->nama,
        ];

        $user = User::create($userData);
        $displayNama = $user->{$nameField} ?? $user->username;

        // 🌟 PERBAIKAN NOTIFIKASI: Mendeteksi siapa yang sedang mengeksekusi aksi (Admin / Pemilik)
        $actorRole = ucfirst(auth()->user()->role ?? 'Admin');

        Notifikasi::create([
            'jenis'     => 'sistem',
            'judul'     => 'Akun baru berhasil didaftarkan',
            'pesan'     => 'Pengguna baru dengan nama "' . $displayNama . '" (' . ucfirst($user->role) . ') telah ditambahkan ke dalam sistem oleh ' . $actorRole . '.',
            'is_dibaca' => false
        ]);

        return redirect()->route('user.index')->with('success', 'Pengguna baru berhasil ditambahkan!');
    }

    /**
     * Mengubah status aktif/nonaktif pengguna.
     */
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        
        // 🛡️ SECURITY FIX 1: Mencegah Pengguna menonaktifkan akunnya sendiri
        if (auth()->id() === $user->id) {
            return redirect()->route('user.index')->withErrors('Anda tidak dapat menonaktifkan akun Anda sendiri!');
        }

        // 🛡️ SECURITY FIX 2: Mencegah Admin/siapa pun menonaktifkan akun Pemilik utama dari tabel
        if ($user->role === 'pemilik' && auth()->user()->role !== 'pemilik') {
            return redirect()->route('user.index')->withErrors('Akses akun Pemilik utama dilindungi dan tidak boleh dinonaktifkan oleh staf lain!');
        }

        // Membalikkan nilai boolean status
        $user->update(['is_aktif' => !$user->is_aktif]);

        $statusTeks = $user->is_aktif ? 'diaktifkan kembali' : 'dinonaktifkan';
        $nameField = $this->getUserNameField();
        $displayNama = $user->{$nameField} ?? $user->username;

        // 🌟 PERBAIKAN NOTIFIKASI: Dinamis sesuai aktor pem pem pembuat keputusan
        $actorRole = ucfirst(auth()->user()->role ?? 'Admin');

        Notifikasi::create([
            'jenis'     => 'sistem',
            'judul'     => 'Status akses pengguna berubah',
            'pesan'     => 'Akses masuk untuk akun "' . $displayNama . '" (' . ucfirst($user->role) . ') telah ' . $statusTeks . ' oleh ' . $actorRole . '.',
            'is_dibaca' => false
        ]);

        return redirect()->route('user.index')->with('success', "Akun {$displayNama} berhasil {$statusTeks}!");
    }

    /**
     * Menghapus pengguna secara permanen dari database dengan aman.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // 🛡️ SECURITY FIX 1: Mencegah pengguna menghapus akunnya sendiri
        if (auth()->id() === $user->id) {
            return redirect()->route('user.index')->withErrors('Anda tidak dapat menghapus akun Anda sendiri!');
        }

        // 🛡️ SECURITY FIX 2: Mencegah siapa pun menghapus akun Pemilik (Owner) Apotek
        if ($user->role === 'pemilik') {
            return redirect()->route('user.index')->withErrors('Akun Pemilik utama dilindungi dan tidak dapat dihapus dari sistem!');
        }

        // Simpan info nama sebelum dihapus
        $nameField = $this->getUserNameField();
        $namaTerhapus = $user->{$nameField} ?? $user->username;
        $roleTerhapus = ucfirst($user->role);

        // 🛡️ DATA INTEGRITY SAFEGUARD: Jalankan Database Transaction
        DB::transaction(function () use ($user) {
            if (Schema::hasTable('notifikasi') && Schema::hasColumn('notifikasi', 'user_id')) {
                Notifikasi::where('user_id', $user->id)->update(['user_id' => null]);
            }
            $user->delete();
        });

        // 🌟 PERBAIKAN NOTIFIKASI: Teks dinamis pelacak aksi
        $actorRole = ucfirst(auth()->user()->role ?? 'Admin');

        Notifikasi::create([
            'jenis'     => 'sistem',
            'judul'     => 'Akun pengguna dihapus permanen',
            'pesan'     => 'Data staf bernama "' . $namaTerhapus . '" (' . $roleTerhapus . ') telah dihapus secara permanen dari basis data sistem oleh ' . $actorRole . '.',
            'is_dibaca' => false
        ]);

        return redirect()->route('user.index')->with('success', "Data pengguna {$namaTerhapus} berhasil dihapus permanen!");
    }
}