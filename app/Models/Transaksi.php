<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaksi extends Model
{
    // 1. Nama tabel tunggal sesuai DB
    protected $table = 'transaksi'; 

    protected $fillable = [
        'no_transaksi',
        'kasir_id',
        'subtotal',
        'diskon_persen',
        'diskon_nominal',
        'pajak_persen',
        'pajak_nominal',
        'total',
        'metode_bayar', // PERBAIKAN: Gunakan metode_bayar (sesuai schema DB Anda)
        'uang_diterima',
        'kembalian',
        'status',
        'catatan',
        'alasan_batal',
    ];

    protected function casts(): array
    {
        return [
            'subtotal'       => 'decimal:0',
            'diskon_persen'  => 'decimal:2',
            'diskon_nominal' => 'decimal:0',
            'pajak_persen'   => 'decimal:2',
            'pajak_nominal'  => 'decimal:0',
            'total'          => 'decimal:0',
            'uang_diterima'  => 'decimal:0',
            'kembalian'      => 'decimal:0',
            'created_at'     => 'datetime',
            'updated_at'     => 'datetime',
        ];
    }

    const STATUS_SELESAI    = 'selesai';
    const STATUS_DIBATALKAN = 'dibatalkan';
    const STATUS_REFUND     = 'refund';

    // 2. Relasi
    public function kasir(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kasir_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(DetailTransaksi::class, 'transaksi_id');
    }

    // 3. Accessor
    public function getWarnaBadgeAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_SELESAI    => 'green',
            self::STATUS_DIBATALKAN => 'red',
            self::STATUS_REFUND     => 'orange',
            default                 => 'gray',
        };
    }

    // 4. Query Scopes
    public function scopeCari(Builder $query, string $keyword): Builder
    {
        return $query->where('no_transaksi', 'like', "%{$keyword}%");
    }

    // 5. Method Bisnis: Hitung Total
    public function hitungTotal(): void
    {
        $subtotal = $this->details()->sum('subtotal');

        $diskonNominal = $subtotal * (($this->diskon_persen ?? 0) / 100);
        $pajakNominal = ($subtotal - $diskonNominal) * (($this->pajak_persen ?? 0) / 100);
        $total = $subtotal - $diskonNominal + $pajakNominal;

        $this->update([
            'subtotal'       => $subtotal,
            'diskon_nominal' => $diskonNominal,
            'pajak_nominal'  => $pajakNominal,
            'total'          => $total,
            // PERBAIKAN: Gunakan metode_bayar
            'kembalian'      => $this->metode_bayar === 'Tunai' ? max(0, $this->uang_diterima - $total) : 0,
        ]);
    }

    // 6. Generate Nomor Transaksi
    public static function generateNomor(): string
    {
        $date = now()->format('Ymd');
        $prefix = "TRX-$date-";

        $lastTrx = self::where('no_transaksi', 'like', $prefix . '%')
                        ->orderBy('id', 'desc')
                        ->first();

        if (!$lastTrx) {
            $urut = '001';
        } else {
            $lastNo = substr($lastTrx->no_transaksi, -3);
            $urut = str_pad((int)$lastNo + 1, 3, '0', STR_PAD_LEFT);
        }

        return $prefix . $urut;
    }
}