<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // 🌟 PERBAIKAN: Wajib diimpor agar DB::table() tidak error

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Pengecekan agar tidak membuat ulang jika tabel entah bagaimana sudah ada
        if (!Schema::hasTable('pengaturan_sistem')) {
            Schema::create('pengaturan_sistem', function (Blueprint $table) {
                $table->id();
                
                // Kolom Informasi Instansi Apotek
                $table->string('nama_apotek')->default('Apotek Citra Sehat');
                $table->string('lokasi_unit')->default('IPB Dramaga Campus');
                
                // Kolom Threshold / Ambang Batas Logika Sistem
                $table->integer('stok_minimum')->default(10);     // Stok kritis pemicu restock
                $table->integer('hari_kadaluarsa')->default(30);   // Jumlah hari sebelum expired untuk alert
                
                $table->timestamps();
            });
        }

        // 🌟 LENGKAPI: Seed data awal secara otomatis menggunakan Query Builder
        // Menggunakan count() untuk memastikan baris data ini hanya dimasukkan sekali (mencegah duplikasi data awal)
        if (DB::table('pengaturan_sistem')->count() === 0) {
            DB::table('pengaturan_sistem')->insert([
                'nama_apotek'     => 'Apotek Citra Sehat',
                'lokasi_unit'     => 'IPB Dramaga Campus',
                'stok_minimum'    => 10,
                'hari_kadaluarsa' => 30,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaturan_sistem');
    }
};