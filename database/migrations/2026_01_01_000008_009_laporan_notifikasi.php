<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->string('jenis', 20);
            $table->string('judul', 200);
            $table->text('pesan');
            $table->boolean('is_dibaca')->default(false);
            $table->unsignedBigInteger('referensi_id')->nullable();
            $table->string('referensi_tipe', 20)->nullable();
            $table->timestampsTz();

            // Index Blueprint
            $table->index('user_id', 'idx_notifikasi_user_id');
            $table->index('is_dibaca', 'idx_notifikasi_is_dibaca');
            $table->index('jenis', 'idx_notifikasi_jenis');
            $table->index('created_at', 'idx_notifikasi_created');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};