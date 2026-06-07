<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema; // 🌟 Diperlukan untuk pengaturan panjang string database
use Illuminate\Support\Facades\URL;    // 🌟 Diperlukan untuk memaksa koneksi aman HTTPS

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /**
         * 1. Perbaikan Error Migrasi Database (Default String Length)
         * Mengatasi error "Specified key was too long; max key length is 1000 bytes"
         * yang sangat sering terjadi pada MySQL versi lama atau MariaDB bawaan shared hosting cPanel.
         */
        Schema::defaultStringLength(191);

        /**
         * 2. Pemaksaan Skema HTTPS di Server Produksi
         * Memastikan semua aset (CSS, JS, Gambar) otomatis dimuat menggunakan protokol HTTPS (SSL).
         * Jika tidak dikunci seperti ini, tampilan CSS/Tailwind sering kali rusak (tidak termuat) saat di-deploy,
         * atau tombol pintas JavaScript (seperti shortcut F2) diblokir oleh browser karena dianggap tidak aman.
         */
        if (app()->environment('production') || config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}