<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->bigIncrements('id');

            // ── Identitas Transaksi ─────────────────────────────────
            $table->string('no_transaksi', 30)->unique();

            // ── Kasir yang Memproses ────────────────────────────────
            $table->foreignId('kasir_id')
                  ->constrained('users');

            // ── Nilai Transaksi ─────────────────────────────────────
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('diskon_persen', 5, 2)->default(0);
            $table->decimal('diskon_nominal', 15, 2)->default(0);
            $table->decimal('pajak_persen', 5, 2)->default(0);
            $table->decimal('pajak_nominal', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);

            // ── Pembayaran ──────────────────────────────────────────
            $table->string('metode_bayar', 20)->default('tunai');
            $table->decimal('uang_diterima', 15, 2)->default(0);
            $table->decimal('kembalian', 15, 2)->default(0);

            // ── Status & Catatan ────────────────────────────────────
            $table->string('status', 20)->default('selesai');
            $table->text('catatan')->nullable();
            $table->text('alasan_batal')->nullable();

            $table->timestampsTz();
        });

        // ── Index Standar (Kompatibel dengan MySQL) ─────────────────
        Schema::table('transaksi', function (Blueprint $table) {
            $table->index('kasir_id', 'idx_transaksi_kasir_id');
            $table->index('status', 'idx_transaksi_status');
            $table->index('metode_bayar', 'idx_transaksi_metode');
            $table->index('created_at', 'idx_transaksi_created');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};