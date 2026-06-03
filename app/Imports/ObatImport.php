<?php

namespace App\Imports;

use App\Models\Obat;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation; // 1. Wajib import concern validasi ini

class ObatImport implements ToModel, WithHeadingRow, WithValidation
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
            'kategori_id'    => 1, // ID default sesuai kode Anda
        ]);
    }

    /**
     * 2. Menentukan Aturan Validasi untuk Kolom di Excel
     * Memastikan stok wajib berupa angka bulat dan lebih besar dari 0 (gt:0)
     */
    public function rules(): array
    {
        return [
            'kode_obat'          => 'required|unique:obat,kode_obat',
            'nama_obat'          => 'required|unique:obat,nama',
            'stok'               => 'required|integer|gt:0', // Mengunci aturan agar stok harus di atas angka 0 (tidak boleh 0 atau minus)
            'harga_beli'         => 'required|numeric|min:0',
            'harga_jual'         => 'required|numeric|min:0',
            'tanggal_kadaluarsa' => 'required',
            'satuan'             => 'required',
        ];
    }

    /**
     * 3. Custom Pesan Error dalam Bahasa Indonesia
     * Menampilkan pesan yang jelas kepada user jika ada data Excel yang melanggar aturan
     */
    public function customValidationMessages()
    {
        return [
            'stok.required'       => 'Jumlah stok wajib diisi pada file Excel.',
            'stok.integer'        => 'Format stok pada file Excel harus berupa angka bulat.',
            'stok.gt'             => 'Gagal Import! Ditemukan baris obat dengan stok 0 atau minus. Stok obat baru wajib di atas angka 0.',
            'kode_obat.unique'    => 'Gagal Import! Ada Kode Obat di Excel yang sudah terdaftar di sistem.',
            'nama_obat.unique'    => 'Gagal Import! Ada Nama Obat di Excel yang sudah terdaftar di sistem.',
            'harga_beli.min'      => 'Harga beli di Excel tidak boleh bernilai negatif.',
            'harga_jual.min'      => 'Harga jual di Excel tidak boleh bernilai negatif.',
        ];
    }
}