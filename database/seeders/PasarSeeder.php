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
                'nama_pasar' => 'Pasar Raya',
                'alamat' => '-',
            ],
            [
                'nama_pasar' => 'Tanah Kongsi',
                'alamat' => '-',
            ],
            [
                'nama_pasar' => 'Ulak Karang',
                'alamat' => '-',
            ],
            [
                'nama_pasar' => 'Alai',
                'alamat' => '-',
            ],
            [
                'nama_pasar' => 'Simpang Haru',
                'alamat' => '-',
            ],
            [
                'nama_pasar' => 'Nanggalo',
                'alamat' => '-',
            ],
            [
                'nama_pasar' => 'Lubuk Buaya',
                'alamat' => '-',
            ],
            [
                'nama_pasar' => 'Belimbing',
                'alamat' => '-',
            ],
            [
                'nama_pasar' => 'Bandar Buat',
                'alamat' => '-',
            ],
        ]);
    }
}