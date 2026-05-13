<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Kita buat user manual tanpa lewat Factory agar tidak memanggil kolom email_verified_at
        User::create([
            'nama'     => 'Administrator',
            'username' => 'admin',
            'email'    => 'admin@example.com',
            'password' => Hash::make('password'), // passwordnya: password
            'role'     => 'admin',
        ]);
    }
}