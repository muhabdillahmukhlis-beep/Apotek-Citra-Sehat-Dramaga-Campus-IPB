<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Kita gunakan dropIfExists dulu agar saat di-migrate ulang tidak bentrok dengan tabel lama
        Schema::dropIfExists('notifikasis');

        Schema::create('notifikasis', function (Blueprint $table) {
            $table->id();
            $table->string('kategori'); // Isinya: 'stok', 'kadaluarsa', atau 'sistem'
            $table->string('judul');
            $table->text('pesan');
            $table->boolean('is_dibaca')->default(false); // false = belum dibaca, true = sudah dibaca
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifikasis');
    }
};