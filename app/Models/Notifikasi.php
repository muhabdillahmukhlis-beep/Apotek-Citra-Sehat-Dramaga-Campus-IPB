<?php

// ================================================================
//  app/Models/Notifikasi.php
// ================================================================

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

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

    /**
     * Relasi ke pengguna penerima notifikasi (jika bersifat privat).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * 🌟 PELENGKAP: Relasi Polimorfik untuk menghubungkan notifikasi 
     * secara fleksibel ke objek target (misal: Model Obat, Transaksi, dll).
     */
    public function referensi(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'referensi_tipe', 'referensi_id');
    }

    // ── Accessor ─────────────────────────────────────────────

    // Ikon berdasarkan jenis notifikasi (untuk tampilan UI Tailwind)
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
        return $this->created_at ? $this->created_at->diffForHumans() : '-';
    }

    // ── Query Scope ───────────────────────────────────────────

    // Notifikasi yang belum dibaca
    public function scopeBelumDibaca(Builder $query): Builder
    {
        return $query->where('is_dibaca', false);
    }

    // Notifikasi untuk user tertentu (termasuk notifikasi global/user_id = NULL)
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

    /**
     * 🌟 PERBAIKAN: Membuat Notifikasi Stok Menipis/Habis secara cerdas
     * Dilengkapi fitur "Anti-Duplikasi" agar tidak membanjiri database jika obat yang sama terus ditransaksikan.
     */
    public static function buatNotifikasiStok(Obat $obat): ?self
    {
        // Cegah duplikasi: Jika notifikasi stok menipis/habis untuk obat ini sudah ada dan BELUM dibaca, jangan buat lagi.
        $sudahAda = static::where('jenis', self::JENIS_STOK)
            ->where('referensi_id', $obat->id)
            ->where('referensi_tipe', 'obat')
            ->belumDibaca()
            ->exists();

        if ($sudahAda) {
            return null;
        }

        $isHabis = ($obat->stok <= 0 || $obat->status === 'habis');

        return static::create([
            'user_id'        => null, // Kirim global ke seluruh staf/admin
            'jenis'          => self::JENIS_STOK,
            'judul'          => "Stok {$obat->nama} " . ($isHabis ? 'Habis!' : 'Menipis'),
            'pesan'          => "Stok obat {$obat->nama} saat ini tersisa {$obat->stok} unit" .
                                ($isHabis
                                    ? '. Status HABIS, mohon segera hubungi distributor/supplier!'
                                    : ", mendekati batas minimum stok keamanan sistem ({$obat->stok_minimum} unit)."),
            'referensi_id'   => $obat->id,
            'referensi_tipe' => 'obat',
            'is_dibaca'      => false
        ]);
    }

    /**
     * 🌟 PERBAIKAN: Membuat Notifikasi Kadaluarsa
     * Dilengkapi proteksi agar tidak menduplikasi peringatan kadaluarsa yang masih aktif.
     */
    public static function buatNotifikasiKadaluarsa(Obat $obat): ?self
    {
        // Cegah duplikasi peringatan kadaluarsa yang belum diselesaikan/dibaca
        $sudahAda = static::where('jenis', self::JENIS_KADALUARSA)
            ->where('referensi_id', $obat->id)
            ->where('referensi_tipe', 'obat')
            ->belumDibaca()
            ->exists();

        if ($sudahAda) {
            return null;
        }

        // Antisipasi jika properti sisa_hari_kadaluarsa bernilai null atau method dinamis
        $sisa = method_exists($obat, 'sisa_hari_kadaluarsa') ? $obat->sisa_hari_kadaluarsa : ($obat->sisa_hari_kadaluarsa ?? 0);
        $formatTanggal = $obat->tgl_kadaluarsa ? $obat->tgl_kadaluarsa->format('d M Y') : '-';

        return static::create([
            'user_id'        => null,
            'jenis'          => self::JENIS_KADALUARSA,
            'judul'          => "Peringatan! {$obat->nama} Hampir Kadaluarsa",
            'pesan'          => "Obat batch {$obat->no_batch} akan memasuki masa kadaluarsa pada tanggal {$formatTanggal} ({$sisa} hari lagi). Segera amankan sisa stok {$obat->stok} unit.",
            'referensi_id'   => $obat->id,
            'referensi_tipe' => 'obat',
            'is_dibaca'      => false
        ]);
    }
}