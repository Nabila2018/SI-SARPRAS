<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PasarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('pasar')->insert([
            [
                'id_pasar' => 'PSR001',
                'nama_pasar' => 'Pasar Raya',
                'alamat' => '-',
            ],
            [
                'id_pasar' => 'PSR002',
                'nama_pasar' => 'Tanah Kongsi',
                'alamat' => '-',
            ],
            [
                'id_pasar' => 'PSR003',
                'nama_pasar' => 'Ulak Karang',
                'alamat' => '-',
            ],
            [
                'id_pasar' => 'PSR004',
                'nama_pasar' => 'Alai',
                'alamat' => '-',
            ],
            [
                'id_pasar' => 'PSR005',
                'nama_pasar' => 'Simpang Haru',
                'alamat' => '-',
            ],
            [
                'id_pasar' => 'PSR006',
                'nama_pasar' => 'Nanggalo',
                'alamat' => '-',
            ],
            [
                'id_pasar' => 'PSR007',
                'nama_pasar' => 'Lubuk Buaya',
                'alamat' => '-',
            ],
            [
                'id_pasar' => 'PSR008',
                'nama_pasar' => 'Belimbing',
                'alamat' => '-',
            ],
            [
                'id_pasar' => 'PSR009',
                'nama_pasar' => 'Bandar Buat',
                'alamat' => '-',
            ],
        ]);
    }
}