<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('user')->insert([
            [
                'username' => 'admin',
                'email' => 'staff@sisarpras.test',
                'password' => Hash::make('password123'),
                'nama_lengkap' => 'Admin Staff Sarpras',
                'id_role' => 2, // Staff Sarana dan Prasarana
                'id_pasar' => null,
                'status_akun' => 'Aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'uptd_pasar_raya',
                'email' => 'uptd.raya@sisarpras.test',
                'password' => Hash::make('password123'),
                'nama_lengkap' => 'Petugas UPTD Pasar Raya',
                'id_role' => 1, // Petugas UPTD
                'id_pasar' => 1, // Pasar Raya
                'status_akun' => 'Aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'uptd_alai',
                  'email' => 'uptd.alai@sisarpras.test',
                'password' => Hash::make('password123'),
                'nama_lengkap' => 'Petugas UPTD Pasar Alai',
                'id_role' => 1,
                'id_pasar' => 4,
                'status_akun' => 'Aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'kabid',
                'email' => 'kabid@sisarpras.test',
                'password' => Hash::make('password123'),
                'nama_lengkap' => 'Kepala Bidang',
                'id_role' => 3, // Kepala Bidang
                'id_pasar' => null,
                'status_akun' => 'Aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'kadin',
                'email' => 'kadin@sisarpras.test',
                'password' => Hash::make('password123'),
                'nama_lengkap' => 'Kepala Dinas',
                'id_role' => 4, // Kepala Dinas
                'id_pasar' => null,
                'status_akun' => 'Aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}