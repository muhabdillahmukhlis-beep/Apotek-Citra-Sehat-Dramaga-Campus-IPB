<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KategoriObat extends Model
{
    use HasFactory;

    // ── Konfigurasi ───────────────────────────────────────────
    protected $table = 'kategori_obat';

    protected $fillable = [
        'nama',      // Pastikan kolom di database memang 'nama'
        'deskripsi',
    ];

    // Laravel secara otomatis menganggap created_at & updated_at sebagai datetime, 
    // jadi method casts() sebenarnya opsional kecuali Anda ingin format khusus.

    // ── Relasi ────────────────────────────────────────────────

    /**
     * Pastikan kolom di tabel 'obat' adalah 'id_kategori' (sesuai dropdown Blade sebelumnya)
     * atau 'kategori_id'. Di sini saya sesuaikan ke 'id_kategori'.
     */
    public function obat()
    {
        return $this->hasMany(Obat::class, 'id_kategori');
    }

    public function obatAktif()
    {
        return $this->hasMany(Obat::class, 'id_kategori')
                    ->where('stok', '>', 0); // Biasanya "aktif" di apotek berarti stok ada
    }

    // ── Accessor ─────────────────────────────────────────────

    public function getJumlahObatAttribute(): int
    {
        return $this->obatAktif()->count();
    }

    // ── Query Scope ───────────────────────────────────────────

    public function scopeUrut(Builder $query): Builder
    {
        return $query->orderBy('nama');
    }

    public function scopeCari(Builder $query, string $keyword): Builder
    {
        // Gunakan 'like' untuk MySQL (XAMPP). 'ilike' hanya untuk PostgreSQL.
        return $query->where('nama', 'like', "%{$keyword}%");
    }

    // ── Method Bisnis ─────────────────────────────────────────

    public function masihDipakai(): bool
    {
        return $this->obat()->exists(); // Cek semua obat, baik stok ada atau tidak
    }
}