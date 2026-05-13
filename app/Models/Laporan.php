<?php

// ================================================================
//  app/Models/Laporan.php
// ================================================================

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class Laporan extends Model
{
    // ── Konfigurasi ───────────────────────────────────────────
    protected $table = 'laporan';

    // Laporan tidak bisa diupdate — hanya dibuat dan dibaca
    public $timestamps = false;

    protected $fillable = [
        'jenis',
        'judul',
        'periode_dari',
        'periode_sampai',
        'parameter_json',
        'hasil_json',
        'dibuat_oleh',
    ];

    protected function casts(): array
    {
        return [
            'periode_dari'    => 'date',
            'periode_sampai'  => 'date',
            'parameter_json'  => 'array', // otomatis encode/decode JSON
            'hasil_json'      => 'array',
            'created_at'      => 'datetime',
        ];
    }

    // ── Konstanta ─────────────────────────────────────────────
    const JENIS_PENJUALAN  = 'penjualan';
    const JENIS_STOK       = 'stok';
    const JENIS_PROFIT     = 'profit';
    const JENIS_AKTIVITAS  = 'aktivitas';
    const JENIS_KADALUARSA = 'kadaluarsa';

    const SEMUA_JENIS = [
        self::JENIS_PENJUALAN  => 'Laporan Penjualan',
        self::JENIS_STOK       => 'Laporan Stok',
        self::JENIS_PROFIT     => 'Laporan Profit',
        self::JENIS_AKTIVITAS  => 'Laporan Aktivitas Pengguna',
        self::JENIS_KADALUARSA => 'Laporan Kadaluarsa',
    ];

    // ── Relasi ────────────────────────────────────────────────

    // User yang membuat laporan ini
    public function pembuatLaporan()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    // ── Accessor ─────────────────────────────────────────────

    // Label jenis laporan yang mudah dibaca
    public function getLabelJenisAttribute(): string
    {
        return self::SEMUA_JENIS[$this->jenis] ?? ucfirst($this->jenis);
    }

    // Format periode: "01 Mei 2026 - 31 Mei 2026"
    public function getPeriodeTeksAttribute(): string
    {
        return $this->periode_dari->format('d M Y')
             . ' - '
             . $this->periode_sampai->format('d M Y');
    }

    // ── Query Scope ───────────────────────────────────────────

    // Filter berdasarkan jenis laporan
    public function scopeJenis(Builder $query, string $jenis): Builder
    {
        return $query->where('jenis', $jenis);
    }

    // Filter berdasarkan pembuat laporan
    public function scopeDibuatOleh(Builder $query, int $userId): Builder
    {
        return $query->where('dibuat_oleh', $userId);
    }

    // Urutkan dari yang terbaru
    public function scopeTerbaru(Builder $query): Builder
    {
        return $query->orderByDesc('created_at');
    }

    // ── Static: Generate Laporan ──────────────────────────────
    // Method-method ini mengumpulkan data dari tabel lain
    // lalu menyimpannya ke dalam laporan

    // Generate laporan penjualan
    public static function generatePenjualan(
        string $dari,
        string $sampai,
        int    $userId,
        array  $parameter = []
    ): self {

        // Kumpulkan data ringkasan
        $ringkasan = Transaksi::ringkasan($dari, $sampai);

        // Penjualan per hari (untuk grafik)
        $perHari = Transaksi::selesai()
            ->tanggal($dari, $sampai)
            ->selectRaw("DATE(created_at) as tanggal, COUNT(id) as jumlah, SUM(total) as total")
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('tanggal')
            ->get()
            ->toArray();

        // Penjualan per metode bayar
        $perMetode = Transaksi::selesai()
            ->tanggal($dari, $sampai)
            ->selectRaw('metode_bayar, COUNT(id) as jumlah, SUM(total) as total')
            ->groupBy('metode_bayar')
            ->get()
            ->toArray();

        // Top 10 obat terlaris di periode ini
        $terlaris = DetailTransaksi::join('transaksi', 'transaksi.id', '=', 'detail_transaksi.transaksi_id')
            ->where('transaksi.status', Transaksi::STATUS_SELESAI)
            ->whereDate('transaksi.created_at', '>=', $dari)
            ->whereDate('transaksi.created_at', '<=', $sampai)
            ->selectRaw('nama_obat, SUM(jumlah) as total_unit, SUM(subtotal) as total_penjualan')
            ->groupBy('nama_obat')
            ->orderByDesc('total_unit')
            ->limit(10)
            ->get()
            ->toArray();

        return static::create([
            'jenis'          => self::JENIS_PENJUALAN,
            'judul'          => 'Laporan Penjualan '
                                . now()->parse($dari)->format('d M Y')
                                . ' - '
                                . now()->parse($sampai)->format('d M Y'),
            'periode_dari'   => $dari,
            'periode_sampai' => $sampai,
            'parameter_json' => $parameter,
            'hasil_json'     => [
                'ringkasan'   => $ringkasan,
                'per_hari'    => $perHari,
                'per_metode'  => $perMetode,
                'terlaris'    => $terlaris,
            ],
            'dibuat_oleh'    => $userId,
            'created_at'     => now(),
        ]);
    }

    // Generate laporan stok
    public static function generateStok(int $userId): self
    {
        // Ringkasan stok semua obat
        $ringkasan = Obat::aktif()
            ->selectRaw('
                COUNT(*)                        AS total_jenis,
                SUM(stok)                       AS total_unit,
                SUM(stok * harga_beli)          AS nilai_hpp,
                SUM(stok * harga_jual)          AS nilai_jual,
                SUM(CASE WHEN status = \'aman\'    THEN 1 ELSE 0 END) AS stok_aman,
                SUM(CASE WHEN status = \'menipis\' THEN 1 ELSE 0 END) AS stok_menipis,
                SUM(CASE WHEN status = \'habis\'   THEN 1 ELSE 0 END) AS stok_habis
            ')
            ->first()
            ->toArray();

        // Daftar obat stok kritis
        $kritis = Obat::aktif()
            ->stokKritis()
            ->with('kategori', 'supplier')
            ->orderBy('stok')
            ->get(['id', 'kode_obat', 'nama', 'stok', 'stok_minimum', 'status'])
            ->toArray();

        // Daftar obat kadaluarsa dalam 90 hari
        $kadaluarsa = Obat::aktif()
            ->kadaluarsaDalam(90)
            ->orderBy('tgl_kadaluarsa')
            ->get(['id', 'kode_obat', 'nama', 'stok', 'tgl_kadaluarsa', 'no_batch'])
            ->toArray();

        return static::create([
            'jenis'          => self::JENIS_STOK,
            'judul'          => 'Laporan Stok Obat per ' . now()->format('d M Y'),
            'periode_dari'   => today()->toDateString(),
            'periode_sampai' => today()->toDateString(),
            'parameter_json' => [],
            'hasil_json'     => [
                'ringkasan'   => $ringkasan,
                'kritis'      => $kritis,
                'kadaluarsa'  => $kadaluarsa,
            ],
            'dibuat_oleh'    => $userId,
            'created_at'     => now(),
        ]);
    }
}
