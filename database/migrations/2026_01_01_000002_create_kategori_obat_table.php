<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kategori_obat', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nama', 50)->unique();
            $table->text('deskripsi')->nullable();
            $table->timestampsTz();
        });
        
    }

    public function down(): void
    {
        Schema::dropIfExists('kategori_obat');
    }
};