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
    // Sesuai dengan database Anda menggunakan 'nama'
    protected $fillable = [
        'nama',
        'username',
        'email',
        'password',
        'role',
        'no_telepon',
        'is_aktif',
        'last_login_at', // Ditambahkan agar bisa mencatat waktu login terbaru
    ];
    
    public function getAuthPassword()
    {
        return $this->password;
    }

    // Kolom yang disembunyikan saat model dikonversi ke array/JSON
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Menggunakan penanganan cast tipe data versi terbaru (Laravel 11+)
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_aktif'          => 'boolean',
            'last_login_at'     => 'datetime',
            'created_at'        => 'datetime',
            'updated_at'        => 'datetime',
        ];
    }

    // ── Konstanta Role ────────────────────────────────────────
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

    public function hasRole(string|array $roles): bool
    {
        return in_array($this->role, (array) $roles);
    }

    // ── Accessor ─────────────────────────────────────────────
    public function getNamaRoleAttribute(): string
    {
        return self::ROLES[$this->role] ?? ucfirst($this->role);
    }

    // Inisial nama untuk avatar di sidebar (Kembali menggunakan properti $this->nama)
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
    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('is_aktif', true);
    }

    public function scopeRole(Builder $query, string $role): Builder
    {
        return $query->where('role', $role);
    }

    // Cari berdasarkan nama, username, atau email (Kembali menggunakan properti 'nama')
    public function scopeCari(Builder $query, string $keyword): Builder
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('nama',       'LIKE', "%{$keyword}%")
              ->orWhere('username', 'LIKE', "%{$keyword}%")
              ->orWhere('email',    'LIKE', "%{$keyword}%");
        });
    }

    // ── Relasi ke Tabel Lain ──────────────────────────────────
    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'kasir_id');
    }

    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'user_id');
    }

    public function stokLog()
    {
        return $this->hasMany(StokLog::class, 'user_id');
    }

    public function laporan()
    {
        return $this->hasMany(Laporan::class, 'dibuat_oleh');
    }

    // ── Method Bisnis ─────────────────────────────────────────
    public function catatLoginTerakhir(): void
    {
        $this->update(['last_login_at' => now()]);
    }

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