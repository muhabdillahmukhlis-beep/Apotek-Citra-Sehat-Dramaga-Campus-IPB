<?php

// ================================================================
//  app/Models/User.php
// ================================================================

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // ── Konfigurasi Tabel ─────────────────────────────────────
    protected $table = 'users';

    // Kolom yang boleh diisi secara massal (mass assignment)
    protected $fillable = [
        'nama',
        'username',
        'email',
        'password',
        'role',
        'no_telepon',
        'is_aktif',
    ];
    
        public function getAuthPassword()
        {
            return $this->password;
        }
    // Kolom yang disembunyikan saat model dikonversi ke array/JSON
    // Penting: password tidak boleh pernah dikirim ke frontend!
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Cast tipe data otomatis
    protected function casts(): array
    {
        return [
            // Password otomatis di-hash saat disimpan ke database
            'password'      => 'hashed',
            'is_aktif'      => 'boolean',
            'last_login_at' => 'datetime',
            'created_at'    => 'datetime',
            'updated_at'    => 'datetime',
        ];
    }

    // ── Konstanta Role ────────────────────────────────────────
    // Gunakan konstanta ini agar tidak ada typo di kode lain
    // Contoh: if ($user->role === User::ROLE_ADMIN)
    const ROLE_ADMIN    = 'admin';
    const ROLE_KASIR    = 'kasir';
    const ROLE_APOTEKER = 'apoteker';
    const ROLE_PEMILIK  = 'pemilik';

    // Daftar semua role dengan label yang mudah dibaca
    const ROLES = [
        self::ROLE_ADMIN    => 'Admin',
        self::ROLE_KASIR    => 'Kasir',
        self::ROLE_APOTEKER => 'Apoteker',
        self::ROLE_PEMILIK  => 'Pemilik Apotek',
    ];

    // ── Pengecekan Role ───────────────────────────────────────
    // Helper method untuk cek role dengan mudah
    // Contoh penggunaan di controller: auth()->user()->isAdmin()

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isKasir(): bool
    {
        return $this->role === self::ROLE_KASIR;
    }

    public function isApoteker(): bool
    {
        return $this->role === self::ROLE_APOTEKER;
    }

    public function isPemilik(): bool
    {
        return $this->role === self::ROLE_PEMILIK;
    }

    // Cek apakah user memiliki salah satu dari beberapa role
    // Contoh: $user->hasRole(['admin', 'apoteker'])
    public function hasRole(string|array $roles): bool
    {
        return in_array($this->role, (array) $roles);
    }

    // ── Accessor ─────────────────────────────────────────────
    // Accessor adalah computed property yang bisa dipanggil
    // seperti kolom biasa: $user->nama_role

    // Nama role yang mudah dibaca manusia
    // Contoh: 'admin' → 'Admin', 'pemilik' → 'Pemilik Apotek'
    public function getNamaRoleAttribute(): string
    {
        return self::ROLES[$this->role] ?? ucfirst($this->role);
    }

    // Inisial nama untuk avatar di sidebar
    // Contoh: 'Rina Kartika' → 'RK'
    public function getInisialsAttribute(): string
    {
        $kata    = explode(' ', trim($this->nama));
        $inisial = '';

        foreach (array_slice($kata, 0, 2) as $k) {
            $inisial .= strtoupper(substr($k, 0, 1));
        }

        return $inisial ?: '?';
    }

    // Status aktif dalam bentuk teks
    public function getStatusTeksAttribute(): string
    {
        return $this->is_aktif ? 'Aktif' : 'Nonaktif';
    }

    // ── Query Scope ───────────────────────────────────────────
    // Scope adalah filter query yang bisa dirantai
    // Contoh: User::aktif()->role('kasir')->get()

    // Hanya ambil user yang aktif
    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('is_aktif', true);
    }

    // Filter berdasarkan role tertentu
    public function scopeRole(Builder $query, string $role): Builder
    {
        return $query->where('role', $role);
    }

    // Cari berdasarkan nama, username, atau email
    public function scopeCari(Builder $query, string $keyword): Builder
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('nama',     'ilike', "%{$keyword}%")
              ->orWhere('username', 'ilike', "%{$keyword}%")
              ->orWhere('email',    'ilike', "%{$keyword}%");
        });
    }

    // ── Relasi ke Tabel Lain ──────────────────────────────────

    // Transaksi yang diproses oleh user ini (sebagai kasir)
    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'kasir_id');
    }

    // Notifikasi yang ditujukan khusus ke user ini
    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'user_id');
    }

    // Perubahan stok yang dilakukan oleh user ini
    public function stokLog()
    {
        return $this->hasMany(StokLog::class, 'user_id');
    }

    // Laporan yang pernah dibuat oleh user ini
    public function laporan()
    {
        return $this->hasMany(Laporan::class, 'dibuat_oleh');
    }

    // ── Method Bisnis ─────────────────────────────────────────

    // Catat waktu login terakhir
    // Dipanggil di AuthController setelah login berhasil
    public function catatLoginTerakhir(): void
    {
        $this->update(['last_login_at' => now()]);
    }

    // Hitung notifikasi yang belum dibaca
    // Termasuk notifikasi global (user_id = NULL) dan khusus user ini
    public function jumlahNotifikasiBelumDibaca(): int
    {
        return Notifikasi::where(function ($q) {
            $q->whereNull('user_id')
              ->orWhere('user_id', $this->id);
        })
        ->where('is_dibaca', false)
        ->count();
    }
}
