<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Identitas supplier / distributor
            $table->string('nama', 100);

            // Nama PIC atau sales yang bisa dihubungi
            $table->string('nama_kontak', 100)->nullable();

            // Kontak supplier
            $table->string('telepon', 30)->nullable();
            $table->string('email', 100)->nullable();
            $table->text('alamat')->nullable();
            $table->string('kota', 50)->nullable();

            // Status — false = supplier nonaktif
            $table->boolean('is_aktif')->default(true);

            // Catatan bebas
            $table->text('catatan')->nullable();

            $table->timestampsTz();

            // Index menggunakan Blueprint (Lebih aman untuk MySQL)
            $table->index('is_aktif', 'idx_supplier_is_aktif');
            $table->index('nama', 'idx_supplier_nama');
        });

        // Bagian COMMENT ON TABLE dihapus karena tidak didukung MySQL via Laravel dengan cara ini
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier');
    }
};