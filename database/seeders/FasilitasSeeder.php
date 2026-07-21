<?php

namespace Database\Seeders;

use App\Models\Fasilitas;
use Illuminate\Database\Seeder;

class FasilitasSeeder extends Seeder
{
    public function run(): void
    {
        $daftarFasilitas = [
            'Toko',
            'Kios',
            'Los/Meja Batu',
            'Counter',
            'PKL',
            'Musholla/Mesjid',
            'Pos Kesehatan/Klinik',
            'ATM',
            'TPA/Ruangan Ibu Menyusui',
            'Cool Storage',
            'Kantor',
            'Pos Keamanan',
            'Pos Pemadam Kebakaran',
            'Toilet Umum',
            'Ruang Lainnya',
        ];

        foreach ($daftarFasilitas as $nama) {
            Fasilitas::create(['nama_fasilitas' => $nama]);
        }
    }
}