<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_transaksi', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->foreignId('transaksi_id')
                  ->constrained('transaksi')
                  ->cascadeOnDelete();

            $table->foreignId('obat_id')
                  ->constrained('obat');

            // ── SNAPSHOT Data Obat Saat Transaksi ──────────────────
            $table->string('nama_obat', 150);
            $table->string('kode_obat', 20)->nullable();
            $table->string('satuan', 20)->nullable();
            $table->decimal('harga_satuan', 15, 2);

            // ── Jumlah & Subtotal ───────────────────────────────────
            $table->integer('jumlah')->default(1);
            $table->decimal('subtotal', 15, 2);

            $table->timestampTz('created_at')->useCurrent();
        });

        // ── Index Standar (Kompatibel dengan MySQL) ─────────────────
        Schema::table('detail_transaksi', function (Blueprint $table) {
            $table->index('transaksi_id', 'idx_detail_transaksi_id');
            $table->index('obat_id', 'idx_detail_obat_id');
            // Index untuk analisis obat terlaris
            $table->index(['obat_id', 'created_at'], 'idx_detail_obat_created');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_transaksi');
    }
};