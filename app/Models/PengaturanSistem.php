<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengaturanSistem extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model ini di database.
     * Mengunci agar Laravel tidak otomatis mencarinya sebagai 'pengaturan_sistems'.
     *
     * @var string
     */
    protected $table = 'pengaturan_sistem';

    /**
     * Kolom-kolom yang diizinkan untuk diisi atau diubah secara massal (Mass Assignment).
     * Melengkapi semua kolom yang ada di file database migration Anda.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_apotek',
        'lokasi_unit',
        'stok_minimum',
        'hari_kadaluarsa'
    ];

    /**
     * Atribut atau kolom yang harus dikonversi ke tipe data tertentu (Casting).
     * Berguna agar ketika Anda memanggil `$pengaturan->stok_minimum`, 
     * Laravel otomatis mengembalikannya sebagai tipe data Integer murni (bukan teks string).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'stok_minimum'    => 'integer',
        'hari_kadaluarsa' => 'integer',
    ];
}