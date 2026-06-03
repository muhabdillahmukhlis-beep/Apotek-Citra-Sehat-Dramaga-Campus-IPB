<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Casts\Attribute;

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
            'harga_beli'     => 'decimal:0', 
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

    // =========================================================================
    // LOGIKA MODEL BOOTED
    // =========================================================================
    protected static function booted(): void
    {
        static::creating(function (Obat $obat) {
            if (empty($obat->kode_obat)) {
                $obat->kode_obat = static::generateKode();
            }
            
            // Pengunci Level Model Akhir untuk Stok
            $obat->stok = (int)$obat->stok <= 0 ? 1 : (int)$obat->stok;
            $obat->status = static::hitungStatus($obat->stok, $obat->stok_minimum ?? 5);
        });

        static::updating(function (Obat $obat) {
            if ((int)$obat->stok < 0) {
                $obat->stok = 0;
            }
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

    // ── Accessors & Mutators (Sintaks Modern Laravel 11+) ──────────────────────────────────

    /**
     * MUTATOR STOK MODERN
     * Mengunci nilai stok di level model. Jika ada proses simpan data 
     * bernilai minus atau 0, otomatis diubah paksa menjadi 1.
     */
    protected function stok(): Attribute
    {
        return Attribute::make(
            set: function (mixed $value) {
                $angka = (int)$value;
                return $angka <= 0 ? 1 : $angka;
            },
        );
    }

    /**
     * MUTATOR HARGA BELI MODERN
     * Mengunci nilai harga beli di level model. Jika ada input bernilai
     * negatif, otomatis diubah paksa menjadi 0.
     */
    protected function hargaBeli(): Attribute
    {
        return Attribute::make(
            set: function (mixed $value) {
                $harga = (float)$value;
                return $harga < 0 ? 0 : $harga;
            },
        );
    }

    /**
     * MUTATOR HARGA JUAL MODERN
     * Mengunci nilai harga jual di level model. Jika ada input bernilai
     * negatif, otomatis diubah paksa menjadi 0.
     */
    protected function hargaJual(): Attribute
    {
        return Attribute::make(
            set: function (mixed $value) {
                $harga = (float)$value;
                return $harga < 0 ? 0 : $harga;
            },
        );
    }

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
        return asset('images/default-obat.png'); 
    }

    // ── Static Helper ─────────────────────────────────────────

    public static function generateKode(): string
    {
        $last = static::latest('id')->first();
        $nextNum = $last ? ($last->id + 1) : 1;
        
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