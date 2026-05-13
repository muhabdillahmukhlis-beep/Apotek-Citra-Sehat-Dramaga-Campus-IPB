<?php

// ================================================================
//  app/Models/Notifikasi.php
// ================================================================

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Notifikasi extends Model
{
    // ── Konfigurasi ───────────────────────────────────────────
    protected $table = 'notifikasi';

    protected $fillable = [
        'user_id',
        'jenis',
        'judul',
        'pesan',
        'is_dibaca',
        'referensi_id',
        'referensi_tipe',
    ];

    protected function casts(): array
    {
        return [
            'is_dibaca'  => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    // ── Konstanta ─────────────────────────────────────────────
    const JENIS_STOK       = 'stok';
    const JENIS_KADALUARSA = 'kadaluarsa';
    const JENIS_SISTEM     = 'sistem';

    // ── Relasi ────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ── Accessor ─────────────────────────────────────────────

    // Ikon berdasarkan jenis notifikasi (untuk tampilan UI)
    public function getIkonAttribute(): string
    {
        return match ($this->jenis) {
            self::JENIS_STOK       => 'box',
            self::JENIS_KADALUARSA => 'alert',
            self::JENIS_SISTEM     => 'info',
            default                => 'bell',
        };
    }

    // Warna badge berdasarkan jenis
    public function getWarnaAttribute(): string
    {
        return match ($this->jenis) {
            self::JENIS_STOK       => 'orange',
            self::JENIS_KADALUARSA => 'red',
            self::JENIS_SISTEM     => 'blue',
            default                => 'gray',
        };
    }

    // Waktu dalam format relatif: "2 menit lalu", "3 jam lalu"
    public function getWaktuRelatifAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    // ── Query Scope ───────────────────────────────────────────

    // Notifikasi yang belum dibaca
    public function scopeBelumDibaca(Builder $query): Builder
    {
        return $query->where('is_dibaca', false);
    }

    // Notifikasi untuk user tertentu
    // Termasuk notifikasi global (user_id = NULL)
    public function scopeUntukUser(Builder $query, int $userId): Builder
    {
        return $query->where(function ($q) use ($userId) {
            $q->whereNull('user_id')
              ->orWhere('user_id', $userId);
        });
    }

    // Filter berdasarkan jenis
    public function scopeJenis(Builder $query, string $jenis): Builder
    {
        return $query->where('jenis', $jenis);
    }

    // Urutkan dari yang terbaru
    public function scopeTerbaru(Builder $query): Builder
    {
        return $query->orderByDesc('created_at');
    }

    // ── Method Bisnis ─────────────────────────────────────────

    // Tandai satu notifikasi sebagai sudah dibaca
    public function tandaiDibaca(): void
    {
        $this->update(['is_dibaca' => true]);
    }

    // Tandai semua notifikasi user sebagai dibaca (static)
    public static function tandaiSemuaDibaca(int $userId): void
    {
        static::untukUser($userId)
            ->belumDibaca()
            ->update(['is_dibaca' => true]);
    }

    // Buat notifikasi stok menipis
    public static function buatNotifikasiStok(Obat $obat): self
    {
        return static::create([
            'user_id'        => null, // kirim ke semua user
            'jenis'          => self::JENIS_STOK,
            'judul'          => "Stok {$obat->nama} " . ($obat->status === 'habis' ? 'Habis!' : 'Menipis'),
            'pesan'          => "Stok {$obat->nama} saat ini tersisa {$obat->stok} unit" .
                                ($obat->status === 'habis'
                                    ? '. Stok HABIS, segera lakukan pemesanan!'
                                    : ", di bawah batas minimum {$obat->stok_minimum} unit."),
            'referensi_id'   => $obat->id,
            'referensi_tipe' => 'obat',
        ]);
    }

    // Buat notifikasi kadaluarsa
    public static function buatNotifikasiKadaluarsa(Obat $obat): self
    {
        $sisa = $obat->sisa_hari_kadaluarsa;

        return static::create([
            'user_id'        => null,
            'jenis'          => self::JENIS_KADALUARSA,
            'judul'          => "{$obat->nama} Hampir Kadaluarsa!",
            'pesan'          => "Batch {$obat->no_batch} akan kadaluarsa pada " .
                                $obat->tgl_kadaluarsa->format('d M Y') .
                                " ({$sisa} hari lagi). Stok tersisa {$obat->stok} unit.",
            'referensi_id'   => $obat->id,
            'referensi_tipe' => 'obat',
        ]);
    }
}
