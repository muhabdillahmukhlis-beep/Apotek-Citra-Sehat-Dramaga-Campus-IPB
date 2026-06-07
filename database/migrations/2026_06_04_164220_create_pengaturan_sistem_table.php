<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // 🌟 IMPOR DB SEEDER

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pengaturan_sistem', function (Blueprint $table) {
            $table->id();
            $table->string('nama_apotek')->default('Apotek Citra Sehat');
            $table->string('lokasi_unit')->default('IPB Dramaga Campus');
            
            // 🌟 TAMBAHKAN KOLOM UTAMA YANG DIBUTUHKAN SISTEM
            $table->integer('stok_minimum')->default(10); // Ambang batas stok kritis
            $table->integer('hari_kadaluarsa')->default(30); // Ambang batas hari menjelang kadaluarsa
            
            $table->timestamps();
        });

        // 🌟 OTOMATIS ISI BARIS PERTAMA (SEED DATA INITIAL)
        // Agar ketika halaman Info Apotek pertama kali dibuka, data tidak kosong/error
        DB::table('pengaturan_sistem')->insert([
            'nama_apotek' => 'Apotek Citra Sehat',
            'lokasi_unit' => 'IPB Dramaga Campus',
            'stok_minimum' => 10,
            'hari_kadaluarsa' => 30,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaturan_sistem');
    }
};