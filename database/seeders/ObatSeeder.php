<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ObatSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'kode_obat' => 'OBT001',
                'nama' => 'Paracetamol 500mg',
                'harga_jual' => 5000,
                'stok' => 100,
                'satuan' => 'Strip',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_obat' => 'OBT002',
                'nama' => 'Amoxicillin 500mg',
                'harga_jual' => 12000,
                'stok' => 50,
                'satuan' => 'Strip',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_obat' => 'OBT003',
                'nama' => 'Promag Tablet',
                'harga_jual' => 8500,
                'stok' => 5,
                'satuan' => 'Box',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_obat' => 'OBT004',
                'nama' => 'Bodrex Extra',
                'harga_jual' => 2500,
                'stok' => 200,
                'satuan' => 'Strip',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_obat' => 'OBT005',
                'nama' => 'Sanmol Syrup',
                'harga_jual' => 18000,
                'stok' => 20,
                'satuan' => 'Botol',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_obat' => 'OBT006',
                'nama' => 'Insto Eye Drops',
                'harga_jual' => 15500,
                'stok' => 30,
                'satuan' => 'Pcs',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_obat' => 'OBT007',
                'nama' => 'Betadine Solution',
                'harga_jual' => 25000,
                'stok' => 12,
                'satuan' => 'Botol',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_obat' => 'OBT008',
                'nama' => 'Neurobion Forte',
                'harga_jual' => 35000,
                'stok' => 8,
                'satuan' => 'Strip',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('obat')->insert($data);
    }
}