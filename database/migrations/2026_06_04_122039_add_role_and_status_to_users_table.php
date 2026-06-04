<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 1. Cek jika kolom 'username' belum ada, baru tambahkan
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username')->unique()->after('nama');
            }

            // 2. Cek jika kolom 'role' belum ada, baru tambahkan
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('Kasir')->after('email'); // Admin, Kasir, Apoteker, Pemilik
            }

            // 3. Cek jika kolom 'is_aktif' belum ada, baru tambahkan
            if (!Schema::hasColumn('users', 'is_aktif')) {
                $table->boolean('is_aktif')->default(true)->after('role');
            }

            // 4. Cek jika kolom 'last_login_at' belum ada, baru tambahkan
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('is_aktif');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'role', 'is_aktif', 'last_login_at']);
        });
    }
};