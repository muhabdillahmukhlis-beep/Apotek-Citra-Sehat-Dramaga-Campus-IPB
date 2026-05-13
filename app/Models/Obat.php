<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Obat extends Model
{
    // Nama tabel eksplisit (Mencegah error 'obats' not found)
    protected $table = 'obat';

    protected $fillable = [
        'kode_obat',
        'nama',
        'kategori_id',
        'satuan',
        'harga_beli',    
        'harga_jual',
        'stok',          
        'tgl_kadaluarsa',
        'is_aktif',
        'nama_generik',
        'supplier_id',
        'stok_minimum',
        'status',
        'no_batch',
        'barcode',
        'gambar',
        'deskripsi',
        'created_by',
        'updated_by',
    ];

    // Menggunakan sintaks casts versi terbaru (Laravel 11+)
    protected function casts(): array
    {
        return [
            'harga_beli'     => 'decimal:0', // Biasanya rupiah tidak pakai desimal di belakang koma
            'harga_jual'     => 'decimal:0',
            'stok'           => 'integer',
            'stok_minimum'   => 'integer',
            'tgl_kadaluarsa' => 'date',
            'is_aktif'       => 'boolean',
        ];
    }

    const STATUS_AMAN    = 'aman';
    const STATUS_MENIPIS = 'menipis';
    const STATUS_HABIS   = 'habis';

    protected static function booted(): void
    {
        static::creating(function (Obat $obat) {
            // Generate kode hanya jika belum diisi manual
            if (empty($obat->kode_obat)) {
                $obat->kode_obat = static::generateKode();
            }
            
            $obat->status = static::hitungStatus($obat->stok ?? 0, $obat->stok_minimum ?? 5);
        });

        static::updating(function (Obat $obat) {
            // Update status otomatis jika stok atau stok_minimum berubah
            $obat->status = static::hitungStatus($obat->stok, $obat->stok_minimum ?? 5);
        });
    }

    // ── Relasi (Type-hinted untuk akurasi IDE) ─────────────────
    
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriObat::class, 'kategori_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    // ── Accessors & Mutators ──────────────────────────────────

    // Format Rupiah (Modern style)
    public function getHargaJualFormatAttribute(): string
    {
        return 'Rp' . number_format($this->harga_jual, 0, ',', '.');
    }

    // URL Gambar Otomatis
    public function getUrlGambarAttribute(): string
    {
        if ($this->gambar && Storage::disk('public')->exists($this->gambar)) {
            return asset('storage/' . $this->gambar);
        }
        return asset('images/default-obat.png'); // Pastikan file ini ada
    }

    // ── Static Helper ─────────────────────────────────────────

    public static function generateKode(): string
    {
        // Ambil record terakhir berdasarkan ID
        $last = static::latest('id')->first();
        $nextNum = $last ? ($last->id + 1) : 1;
        
        // Contoh: OBT-001, OBT-010, OBT-100
        return 'OBT-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
    }

    public static function hitungStatus(int $stok, int $stokMinimum): string
    {
        if ($stok <= 0) return self::STATUS_HABIS;
        if ($stok <= $stokMinimum) return self::STATUS_MENIPIS;
        return self::STATUS_AMAN;
    }

    // ── Local Scopes (Untuk memudahkan filter) ────────────────
    
    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true);
    }

    public function scopeMenipis($query)
    {
        return $query->whereRaw('stok <= stok_minimum')->where('stok', '>', 0);
    }
}