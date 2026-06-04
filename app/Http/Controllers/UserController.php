<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Notifikasi; // Impor model Notifikasi agar dapat digunakan
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Menampilkan halaman Manajemen Pengguna dan Matriks Hak Akses.
     */
    public function index(Request $request)
    {
        // 1. Mengambil seluruh data pengguna dari database berdasarkan nama (A-Z)
        // Menggunakan order-by defensif mengantisipasi kolom 'nama' atau 'name'
        $orderByField = \Schema::hasColumn('users', 'nama') ? 'nama' : 'name';
        $users = User::orderBy($orderByField, 'asc')->get();

        // 2. Definisi statis matriks hak akses sesuai dengan blueprint desain UI Apotek Citra Sehat
        $matriksHakAkses = [
            [
                'fitur'    => 'Dashboard',
                'admin'    => true,
                'kasir'    => true,
                'apoteker' => true,
                'pemilik'  => true
            ],
            [
                'fitur'    => 'Data Obat (CRUD)',
                'admin'    => true,
                'kasir'    => false,
                'apoteker' => true,
                'pemilik'  => false
            ],
            [
                'fitur'    => 'Transaksi Penjualan',
                'admin'    => true,
                'kasir'    => true,
                'apoteker' => false,
                'pemilik'  => false
            ],
            [
                'fitur'    => 'Riwayat Transaksi',
                'admin'    => true,
                'kasir'    => true,
                'apoteker' => false,
                'pemilik'  => true
            ],
            [
                'fitur'    => 'Manajemen Stok',
                'admin'    => true,
                'kasir'    => false,
                'apoteker' => true,
                'pemilik'  => false
            ],
            [
                'fitur'    => 'Monitoring Kadaluarsa',
                'admin'    => true,
                'kasir'    => false,
                'apoteker' => true,
                'pemilik'  => false
            ],
            [
                'fitur'    => 'Laporan & Analitik',
                'admin'    => true,
                'kasir'    => false,
                'apoteker' => false,
                'pemilik'  => true
            ],
            [
                'fitur'    => 'Manajemen Pengguna',
                'admin'    => true,
                'kasir'    => false,
                'apoteker' => false,
                'pemilik'  => false
            ],
            [
                'fitur'    => 'Pengaturan Sistem',
                'admin'    => true,
                'kasir'    => false,
                'apoteker' => false,
                'pemilik'  => true
            ],
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
        // 1. Validasi Input Form
        $request->validate([
            'nama'       => 'required|string|max:255',
            'username'   => 'required|string|alpha_dash|max:50|unique:users,username',
            'email'      => 'required|string|email|max:255|unique:users,email',
            'password'   => 'required|string|min:6',
            'role'       => 'required|in:admin,kasir,apoteker,pemilik',
            'no_telepon' => 'nullable|string|max:15',
        ], [
            // Pesan error kustom bahasa Indonesia jika validasi gagal
            'username.unique' => 'Username ini sudah digunakan oleh staf lain.',
            'email.unique'    => 'Alamat email ini sudah terdaftar.',
            'password.min'    => 'Password minimal harus 6 karakter.',
        ]);

        // 2. Simpan Data ke Database (Dengan penanganan fallback kolom nama/name)
        $namaInput = $request->nama;
        $userData = [
            'username'   => strtolower($request->username),
            'email'      => $request->email,
            'password'   => Hash::make($request->password), 
            'role'       => $request->role,
            'no_telepon' => $request->no_telepon,
            'is_aktif'   => true, 
        ];

        if (\Schema::hasColumn('users', 'nama')) {
            $userData['nama'] = $namaInput;
        } else {
            $userData['name'] = $namaInput;
        }

        $user = User::create($userData);
        $displayNama = $user->nama ?? $user->name ?? $user->username;

        // 🔥 AUTOMATIC TRIGGER: Kirim Notifikasi Registrasi Akun Baru ke Sistem
        Notifikasi::create([
            'kategori'  => 'sistem',
            'judul'     => 'Akun baru berhasil didaftarkan',
            'pesan'     => 'Pengguna baru dengan nama "' . $displayNama . '" (' . ucfirst($user->role) . ') telah ditambahkan ke dalam sistem oleh Admin.',
            'is_dibaca' => false
        ]);

        // 3. Kembalikan ke halaman index dengan Pesan Sukses Alert
        return redirect()->route('user.index')->with('success', 'Pengguna baru berhasil ditambahkan!');
    }

    /**
     * Mengubah status aktif/nonaktif pengguna.
     */
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        
        // 🛡️ SECURITY FIX 1: Mencegah Admin menonaktifkan akunnya sendiri
        if (auth()->id() == $user->id) {
            return redirect()->route('user.index')->withErrors('Anda tidak dapat menonaktifkan akun Anda sendiri!');
        }

        // 🛡️ SECURITY FIX 2: Mencegah Admin menonaktifkan akun Pemilik (Owner) Apotek
        if ($user->role === 'pemilik') {
            return redirect()->route('user.index')->withErrors('Akses akun Pemilik utama tidak boleh dinonaktifkan demi keamanan sistem!');
        }

        // Membalikkan nilai boolean status
        $user->update(['is_aktif' => !$user->is_aktif]);

        $statusTeks = $user->is_aktif ? 'diaktifkan kembali' : 'dinonaktifkan';
        $displayNama = $user->nama ?? $user->name ?? $user->username;

        // 🔥 AUTOMATIC TRIGGER: Kirim Notifikasi Perubahan Status Keaktifan Akun
        Notifikasi::create([
            'kategori'  => 'sistem',
            'judul'     => 'Status akses pengguna berubah',
            'pesan'     => 'Akses masuk untuk akun "' . $displayNama . '" (' . ucfirst($user->role) . ') telah ' . $statusTeks . ' oleh Admin.',
            'is_dibaca' => false
        ]);

        return redirect()->route('user.index')->with('success', "Akun {$displayNama} berhasil {$statusTeks}!");
    }

    /**
     * Menghapus pengguna secara permanen dari database.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // 🛡️ SECURITY FIX 1: Mencegah Admin menghapus akunnya sendiri
        if (auth()->id() == $user->id) {
            return redirect()->route('user.index')->withErrors('Anda tidak dapat menghapus akun Anda sendiri!');
        }

        // 🛡️ SECURITY FIX 2: Mencegah Admin menghapus akun Pemilik (Owner) Apotek
        if ($user->role === 'pemilik') {
            return redirect()->route('user.index')->withErrors('Akun Pemilik utama dilindungi dan tidak dapat dihapus dari sistem!');
        }

        // Simpan nama dan role sebelum objek dihapus untuk keperluan pesan teks notifikasi
        $namaTerhapus = $user->nama ?? $user->name ?? $user->username;
        $roleTerhapus = ucfirst($user->role);

        $user->delete();

        // 🔥 AUTOMATIC TRIGGER: Kirim Notifikasi Penghapusan Akun Permanen
        Notifikasi::create([
            'kategori'  => 'sistem',
            'judul'     => 'Akun pengguna dihapus permanen',
            'pesan'     => 'Data staf bernama "' . $namaTerhapus . '" (' . $roleTerhapus . ') telah dihapus secara permanen dari basis data sistem.',
            'is_dibaca' => false
        ]);

        return redirect()->route('user.index')->with('success', "Data pengguna {$namaTerhapus} berhasil dihapus permanen!");
    }
}