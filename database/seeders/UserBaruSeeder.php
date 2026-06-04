<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserBaruSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Siti Aminah, A.Md.Farm.',
                'username' => 'siti.admin',
                'email' => 'siti@apotek.com',
                'password' => Hash::make('password123'),
                'role' => 'Admin',
                'is_aktif' => true,
                'last_login_at' => now()->subHours(2),
            ],
            [
                'name' => 'Dr. apt. Budi Santoso, M.Kes.',
                'username' => 'budi.apoteker',
                'email' => 'budi@apotek.com',
                'password' => Hash::make('password123'),
                'role' => 'Apoteker',
                'is_aktif' => true,
                'last_login_at' => now()->subHours(3),
            ],
            [
                'name' => 'Rina Kartika, S.Farm.',
                'username' => 'rina.kasir',
                'email' => 'rina@apotek.com',
                'password' => Hash::make('password123'),
                'role' => 'Kasir',
                'is_aktif' => true,
                'last_login_at' => now()->subMinutes(15),
            ],
            [
                'name' => 'Hendra Wijaya, S.E., Apt.',
                'username' => 'hendra.pemilik',
                'email' => 'hendra@apotek.com',
                'password' => Hash::make('password123'),
                'role' => 'Pemilik',
                'is_aktif' => true,
                'last_login_at' => now()->subDays(1),
            ],
            [
                'name' => 'Sela Marlina',
                'username' => 'sela.kasir2',
                'email' => 'sela@apotek.com',
                'password' => Hash::make('password123'),
                'role' => 'Kasir',
                'is_aktif' => false,
                'last_login_at' => now()->subDays(5),
            ],
        ];

        foreach ($users as $u) {
            User::updateOrCreate(['username' => $u['username']], $u);
        }
    }
}