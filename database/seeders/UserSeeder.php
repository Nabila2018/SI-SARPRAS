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
                'id_user'     => 'USR001',
                'email'       => 'staff@sisarpras.test',
                'password'    => Hash::make('password123'),
                'nama_lengkap'=> 'Admin Staff Sarpras',
                'id_role'     => 'RL002', // Staff Sarana dan Prasarana
                'id_pasar'    => null,
                'status_akun' => 'Aktif',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'id_user'     => 'USR002',
                'email'       => 'uptd.pasar.raya@sisarpras.test',
                'password'    => Hash::make('password123'),
                'nama_lengkap'=> 'Petugas UPTD Pasar Raya',
                'id_role'     => 'RL001', // Petugas UPTD
                'id_pasar'    => 'PSR001', // Pasar Raya
                'status_akun' => 'Aktif',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'id_user'     => 'USR003',
                'email'       => 'uptd.alai@sisarpras.test',
                'password'    => Hash::make('password123'),
                'nama_lengkap'=> 'Petugas UPTD Pasar Alai',
                'id_role'     => 'RL001', // Petugas UPTD
                'id_pasar'    => 'PSR004', // Pasar Alai
                'status_akun' => 'Aktif',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'id_user'     => 'USR004',
                'email'       => 'kabid@sisarpras.test',
                'password'    => Hash::make('password123'),
                'nama_lengkap'=> 'Kepala Bidang',
                'id_role'     => 'RL003', // Kepala Bidang
                'id_pasar'    => null,
                'status_akun' => 'Aktif',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'id_user'     => 'USR005',
                'email'       => 'kadin@sisarpras.test',
                'password'    => Hash::make('password123'),
                'nama_lengkap'=> 'Kepala Dinas',
                'id_role'     => 'RL004', // Kepala Dinas
                'id_pasar'    => null,
                'status_akun' => 'Aktif',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }
}