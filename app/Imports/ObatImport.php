<?php

namespace App\Imports;

use App\Models\Obat;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ObatImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Obat([
            'kode_obat'      => $row['kode_obat'],
            'nama'           => $row['nama_obat'],
            'stok'           => $row['stok'],
            'harga_beli'     => $row['harga_beli'],
            'harga_jual'     => $row['harga_jual'],
            'tgl_kadaluarsa' => $row['tanggal_kadaluarsa'],
            'satuan'         => $row['satuan'],
            'kategori_id'    => 1, // Berikan ID default atau sesuaikan logika Anda
        ]);
    }
}