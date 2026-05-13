<?php

// ================================================================
//  app/Models/StokLog.php
// ================================================================

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class StokLog extends Model
{
    // ── Konfigurasi ───────────────────────────────────────────
    protected $table = 'stok_log';

    // Tabel ini tidak punya updated_at
    // Karena stok_log bersifat IMMUTABLE — tidak boleh diedit
    public $timestamps = false;

    protected $fillable = [
        'obat_id',
        'user_id',
        'transaksi_id',
        'jenis',
        'jumlah_sebelum',
        'perubahan',
        'jumlah_sesudah',
        'alasan',
        'no_batch',
        'harga_beli_saat_itu',
    ];

    protected function casts(): array
    {
        return [
            'jumlah_sebelum'      => 'integer',
            'perubahan'           => 'integer',
            'jumlah_sesudah'      => 'integer',
            'harga_beli_saat_itu' => 'decimal:2',
            'created_at'          => 'datetime',
        ];
    }

    // ── Konstanta Jenis ───────────────────────────────────────
    const JENIS_MASUK       = 'masuk';
    const JENIS_KELUAR      = 'keluar';
    const JENIS_PENYESUAIAN = 'penyesuaian';
    const JENIS_RETUR       = 'retur';

    const SEMUA_JENIS = [
        self::JENIS_MASUK       => 'Stok Masuk',
        self::JENIS_KELUAR      => 'Stok Keluar',
        self::JENIS_PENYESUAIAN => 'Penyesuaian Manual',
        self::JENIS_RETUR       => 'Retur ke Supplier',
    ];

    // ── Relasi ────────────────────────────────────────────────

    public function obat()
    {
        return $this->belongsTo(Obat::class, 'obat_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_id');
    }

    // ── Accessor ─────────────────────────────────────────────

    // Label jenis yang mudah dibaca
    public function getLabelJenisAttribute(): string
    {
        return self::SEMUA_JENIS[$this->jenis] ?? ucfirst($this->jenis);
    }

    // Warna badge berdasarkan jenis perubahan
    public function getWarnaBadgeAttribute(): string
    {
        return match ($this->jenis) {
            self::JENIS_MASUK  => 'green',
            self::JENIS_KELUAR => 'red',
            self::JENIS_RETUR  => 'blue',
            default            => 'yellow',
        };
    }

    // Perubahan dalam format teks dengan tanda +/-
    // Contoh: +50, -3
    public function getPerubahanFormatAttribute(): string
    {
        return $this->perubahan > 0
            ? "+{$this->perubahan}"
            : (string) $this->perubahan;
    }

    // ── Query Scope ───────────────────────────────────────────

    // Filter berdasarkan jenis
    public function scopeJenis(Builder $query, string $jenis): Builder
    {
        return $query->where('jenis', $jenis);
    }

    // Filter berdasarkan obat
    public function scopeUntukObat(Builder $query, int $obatId): Builder
    {
        return $query->where('obat_id', $obatId);
    }

    // Filter berdasarkan tanggal
    public function scopeTanggal(Builder $query, string $dari, string $sampai): Builder
    {
        return $query->whereBetween('created_at', [
            $dari . ' 00:00:00',
            $sampai . ' 23:59:59',
        ]);
    }

    // Urutkan dari yang terbaru
    public function scopeTerbaru(Builder $query): Builder
    {
        return $query->orderByDesc('created_at');
    }
}
