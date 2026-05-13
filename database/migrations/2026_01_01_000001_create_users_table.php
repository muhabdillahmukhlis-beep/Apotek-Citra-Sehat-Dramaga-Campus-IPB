<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Buat tabel users.
     * Jalankan: php artisan migrate
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {

            $table->bigIncrements('id');

            // Data diri pengguna
            $table->string('nama', 100);
            $table->string('username', 50)->unique();
            $table->string('email', 100)->unique();

            // Password 
            $table->string('password');

            
            $table->string('role', 20)->default('kasir');

            // Info kontak
            $table->string('no_telepon', 20)->nullable();

            // Status akun — false = akun dinonaktifkan, tidak bisa login
            $table->boolean('is_aktif')->default(true);

            // Token untuk fitur "ingat saya" saat login
            $table->rememberToken();

            // Catat waktu login terakhir untuk kebutuhan audit
            $table->timestampTz('last_login_at')->nullable();

            // Timestamp otomatis (created_at & updated_at)
            $table->timestampsTz();
        });

        // Index untuk mempercepat query filter
        DB::statement('CREATE INDEX idx_users_role     ON users (role)');
        DB::statement('CREATE INDEX idx_users_is_aktif ON users (is_aktif)');
    }

    
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
