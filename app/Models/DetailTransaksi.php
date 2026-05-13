<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailTransaksi extends Model
{
    // ── Konfigurasi ───────────────────────────────────────────
    protected $table = 'detail_transaksi';

    // Karena detail transaksi tidak boleh diedit (hanya insert), 
    // kita matikan timestamps standar dan tangani created_at secara manual jika kolom updated_at memang tidak ada.
    public $timestamps = false;

    protected $fillable = [
        'transaksi_id',
        'obat_id',
        'nama_obat',    
        'kode_obat',    
        'satuan',       
        'harga_satuan', 
        'jumlah',
        'subtotal',     
    ];

    protected function casts(): array
    {
        return [
            'harga_satuan' => 'decimal:0', // Disamakan dengan Transaksi & Obat
            'jumlah'       => 'integer',
            'subtotal'     => 'decimal:0',
            'created_at'   => 'datetime',
        ];
    }

    // ── Model Event ───────────────────────────────────────────
    protected static function booted(): void
    {
        static::creating(function (DetailTransaksi $detail) {
            // Hitung subtotal jika belum ada nilainya
            if (!$detail->subtotal) {
                $detail->subtotal = $detail->harga_satuan * $detail->jumlah;
            }

            // Set created_at secara manual karena $timestamps = false
            $detail->created_at = now();
        });
    }

    // ── Relasi ────────────────────────────────────────────────

    public function transaksi(): BelongsTo
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_id');
    }

    public function obat(): BelongsTo
    {
        return $this->belongsTo(Obat::class, 'obat_id');
    }

    // ── Accessor ─────────────────────────────────────────────

    public function getHargaFormatAttribute(): string
    {
        return 'Rp ' . number_format($this->harga_satuan, 0, ',', '.');
    }

    public function getSubtotalFormatAttribute(): string
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }

    // ── Query Scope ───────────────────────────────────────────

    public function scopeTransaksi(Builder $query, int $transaksiId): Builder
    {
        return $query->where('transaksi_id', $transaksiId);
    }

    // ── Static Helper ─────────────────────────────────────────

    /**
     * Helper untuk insert data detail dengan snapshot otomatis
     */
    public static function buatDariObat(
        int   $transaksiId,
        Obat  $obat,
        int   $jumlah
    ): self {
        return static::create([
            'transaksi_id' => $transaksiId,
            'obat_id'      => $obat->id,
            'nama_obat'    => $obat->nama,
            'kode_obat'    => $obat->kode_obat,
            'satuan'       => $obat->satuan,
            'harga_satuan' => $obat->harga_jual,
            'jumlah'       => $jumlah,
            'subtotal'     => $obat->harga_jual * $jumlah,
        ]);
    }
}