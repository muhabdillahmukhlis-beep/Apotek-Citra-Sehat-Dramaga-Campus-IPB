<?php

// ================================================================
//  app/Models/Supplier.php
// ================================================================

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Supplier extends Model
{
    // ── Konfigurasi ───────────────────────────────────────────
    protected $table = 'supplier';

    protected $fillable = [
        'nama',
        'nama_kontak',
        'telepon',
        'email',
        'alamat',
        'kota',
        'is_aktif',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'is_aktif'   => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    // ── Relasi ────────────────────────────────────────────────

    // Semua obat yang dipasok oleh supplier ini
    public function obat()
    {
        return $this->hasMany(Obat::class, 'supplier_id');
    }

    // Hanya obat aktif dari supplier ini
    public function obatAktif()
    {
        return $this->hasMany(Obat::class, 'supplier_id')
                    ->where('is_aktif', true);
    }

    // ── Accessor ─────────────────────────────────────────────

    // Status dalam bentuk teks
    public function getStatusTeksAttribute(): string
    {
        return $this->is_aktif ? 'Aktif' : 'Nonaktif';
    }

    // Jumlah jenis obat yang dipasok
    public function getJumlahObatAttribute(): int
    {
        return $this->obatAktif()->count();
    }

    // ── Query Scope ───────────────────────────────────────────

    // Hanya supplier yang aktif
    // Contoh: Supplier::aktif()->get()
    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('is_aktif', true);
    }

    // Cari berdasarkan nama supplier atau nama kontak
    public function scopeCari(Builder $query, string $keyword): Builder
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('nama',        'ilike', "%{$keyword}%")
              ->orWhere('nama_kontak', 'ilike', "%{$keyword}%")
              ->orWhere('kota',        'ilike', "%{$keyword}%");
        });
    }

    // Urutkan berdasarkan nama
    public function scopeUrut(Builder $query): Builder
    {
        return $query->orderBy('nama');
    }

    // ── Method Bisnis ─────────────────────────────────────────

    // Nonaktifkan supplier
    // Supplier tidak dihapus permanen — hanya dinonaktifkan
    public function nonaktifkan(): bool
    {
        return $this->update(['is_aktif' => false]);
    }

    // Aktifkan kembali supplier
    public function aktifkan(): bool
    {
        return $this->update(['is_aktif' => true]);
    }

    // Cek apakah supplier masih punya obat aktif
    // Berguna sebelum menonaktifkan supplier
    public function masihDipakai(): bool
    {
        return $this->obatAktif()->exists();
    }
}
