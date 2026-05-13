<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stok_log', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Obat yang stoknya berubah
            $table->foreignId('obat_id')
                  ->constrained('obat');

            // Siapa yang melakukan perubahan
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // Transaksi terkait
            $table->foreignId('transaksi_id')
                  ->nullable()
                  ->constrained('transaksi')
                  ->nullOnDelete();

            // ── Jenis & Data Perubahan ──────────────────────────────
            $table->string('jenis', 20);
            $table->integer('jumlah_sebelum');
            $table->integer('perubahan');
            $table->integer('jumlah_sesudah');

            // ── Info Tambahan ───────────────────────────────────────
            $table->string('alasan', 255)->nullable();
            $table->string('no_batch', 50)->nullable();
            $table->decimal('harga_beli_saat_itu', 15, 2)->nullable();

            $table->timestampTz('created_at')->useCurrent();
        });

        // ── Index Standar (Kompatibel dengan MySQL) ─────────────────
        Schema::table('stok_log', function (Blueprint $table) {
            $table->index('obat_id', 'idx_stok_log_obat_id');
            $table->index('user_id', 'idx_stok_log_user_id');
            $table->index('jenis', 'idx_stok_log_jenis');
            $table->index('created_at', 'idx_stok_log_created');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stok_log');
    }
};