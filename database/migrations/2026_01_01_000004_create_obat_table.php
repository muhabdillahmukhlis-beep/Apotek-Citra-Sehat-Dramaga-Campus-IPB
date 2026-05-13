<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('obat', function (Blueprint $table) {
            $table->bigIncrements('id');

            // ── Identitas Obat ──────────────────────────────────────
            $table->string('kode_obat', 20)->unique();
            $table->string('nama', 150);
            $table->string('nama_generik', 150)->nullable();

            // ── Relasi ──────────────────────────────────────────────
            $table->foreignId('kategori_id')
                  ->nullable()
                  ->constrained('kategori_obat')
                  ->nullOnDelete();

            $table->foreignId('supplier_id')
                  ->nullable()
                  ->constrained('supplier')
                  ->nullOnDelete();

            // ── Satuan ──────────────────────────────────────────────
            $table->string('satuan', 20)->default('tablet');

            // ── Harga ────────────────────────────────────────────────
            $table->decimal('harga_beli', 15, 2)->default(0);
            $table->decimal('harga_jual', 15, 2)->default(0);

            // ── Stok ─────────────────────────────────────────────────
            $table->integer('stok')->default(0);
            $table->integer('stok_minimum')->default(10);
            $table->string('status', 20)->default('aman');

            // ── Batch & Kadaluarsa ───────────────────────────────────
            $table->string('no_batch', 50)->nullable();
            $table->string('barcode', 50)->nullable()->unique();
            $table->date('tgl_kadaluarsa')->nullable();

            // ── Info Tambahan ────────────────────────────────────────
            $table->string('gambar', 255)->nullable();
            $table->text('deskripsi')->nullable();

            // ── Audit Trail ──────────────────────────────────────────
            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->foreignId('updated_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->boolean('is_aktif')->default(true);
            $table->timestampsTz();
        });

        // ── Index Standar (Kompatibel dengan MySQL) ─────────────────
        Schema::table('obat', function (Blueprint $table) {
            $table->index('status');
            $table->index('is_aktif');
            $table->index('tgl_kadaluarsa');
            $table->index('stok');
            $table->index('barcode');
            $table->index('nama'); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('obat');
    }
};