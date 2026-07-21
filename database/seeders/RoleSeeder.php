<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $daftarRole = [
            'Petugas UPTD',
            'Staff Sarana dan Prasarana',
            'Kepala Bidang',
            'Kepala Dinas',
        ];

        foreach ($daftarRole as $nama) {
            Role::create(['nama_role' => $nama]);
        }
    }
}