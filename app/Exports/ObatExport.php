<?php

namespace App\Exports;

use App\Models\Obat;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ObatExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        // Memilih kolom yang ingin di-export
        return Obat::select('kode_obat', 'nama', 'stok', 'harga_beli', 'harga_jual', 'tgl_kadaluarsa', 'satuan')->get();
    }

    public function headings(): array
    {
        return ["Kode Obat", "Nama Obat", "Stok", "Harga Beli", "Harga Jual", "Tanggal Kadaluarsa", "Satuan"];
    }
}