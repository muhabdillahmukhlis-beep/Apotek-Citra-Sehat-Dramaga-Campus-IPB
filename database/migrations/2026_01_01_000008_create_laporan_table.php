<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('jenis', 30);
            $table->string('judul', 200);
            $table->date('periode_dari');
            $table->date('periode_sampai');

            // MySQL menggunakan json(), bukan jsonb()
            $table->json('parameter_json')->nullable();
            $table->json('hasil_json')->nullable();

            $table->foreignId('dibuat_oleh')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->timestampTz('created_at')->useCurrent();

            // Index menggunakan Blueprint (Aman untuk MySQL)
            $table->index('jenis', 'idx_laporan_jenis');
            $table->index('dibuat_oleh', 'idx_laporan_dibuat');
            $table->index(['periode_dari', 'periode_sampai'], 'idx_laporan_periode');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan');
    }
};