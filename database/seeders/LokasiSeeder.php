<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LokasiSeeder extends Seeder
{
    public function run(): void
    {
        // ============================================
        // 1. PASAR RAYA (id_pasar = 1)
        // Struktur: Pasar → Blok → Lantai
        // ============================================
        
        // Level 1: Pasar Raya Timur I (id_lokasi = 1)
        $pasarRayaTimurI = DB::table('lokasi')->insertGetId([
            'id_pasar' => 1,
            'id_induk' => null,
            'nama_lokasi' => 'Pasar Raya Timur I',
            'tipe_lokasi' => 'Pasar',
            'tahun_mulai_dibangun' => null,
            'tahun_selesai_dibangun' => null,
            'luas_tanah' => null,
            'luas_bangunan' => null,
            'keterangan' => null,
        ]);

        // Blok I (child dari Pasar Raya Timur I)
        $blokI = DB::table('lokasi')->insertGetId([
            'id_pasar' => 1,
            'id_induk' => $pasarRayaTimurI,
            'nama_lokasi' => 'Blok I',
            'tipe_lokasi' => 'Blok',
            'tahun_mulai_dibangun' => null,
            'tahun_selesai_dibangun' => null,
            'luas_tanah' => null,
            'luas_bangunan' => null,
            'keterangan' => null,
        ]);

        // Lantai 1-4 (child dari Blok I)
        DB::table('lokasi')->insert([
            ['id_pasar' => 1, 'id_induk' => $blokI, 'nama_lokasi' => 'Lantai 1', 'tipe_lokasi' => 'Lantai', 'tahun_mulai_dibangun' => null, 'tahun_selesai_dibangun' => null, 'luas_tanah' => null, 'luas_bangunan' => null, 'keterangan' => null],
            ['id_pasar' => 1, 'id_induk' => $blokI, 'nama_lokasi' => 'Lantai 2', 'tipe_lokasi' => 'Lantai', 'tahun_mulai_dibangun' => null, 'tahun_selesai_dibangun' => null, 'luas_tanah' => null, 'luas_bangunan' => null, 'keterangan' => 'Tidak Aktif'],
            ['id_pasar' => 1, 'id_induk' => $blokI, 'nama_lokasi' => 'Lantai 3', 'tipe_lokasi' => 'Lantai', 'tahun_mulai_dibangun' => null, 'tahun_selesai_dibangun' => null, 'luas_tanah' => null, 'luas_bangunan' => null, 'keterangan' => null],
            ['id_pasar' => 1, 'id_induk' => $blokI, 'nama_lokasi' => 'Lantai 4', 'tipe_lokasi' => 'Lantai', 'tahun_mulai_dibangun' => null, 'tahun_selesai_dibangun' => null, 'luas_tanah' => null, 'luas_bangunan' => null, 'keterangan' => 'Masjid'],
        ]);

        // Blok II (child dari Pasar Raya Timur I)
        $blokII = DB::table('lokasi')->insertGetId([
            'id_pasar' => 1,
            'id_induk' => $pasarRayaTimurI,
            'nama_lokasi' => 'Blok II',
            'tipe_lokasi' => 'Blok',
            'tahun_mulai_dibangun' => 2012,
            'tahun_selesai_dibangun' => 2015,
            'luas_tanah' => null,
            'luas_bangunan' => null,
            'keterangan' => null,
        ]);

        // Basement + Lantai 1-5 (child dari Blok II)
        DB::table('lokasi')->insert([
            ['id_pasar' => 1, 'id_induk' => $blokII, 'nama_lokasi' => 'Basement', 'tipe_lokasi' => 'Lantai', 'tahun_mulai_dibangun' => null, 'tahun_selesai_dibangun' => null, 'luas_tanah' => null, 'luas_bangunan' => null, 'keterangan' => 'Sebagian Besar Tidak Aktif'],
            ['id_pasar' => 1, 'id_induk' => $blokII, 'nama_lokasi' => 'Lantai 1', 'tipe_lokasi' => 'Lantai', 'tahun_mulai_dibangun' => null, 'tahun_selesai_dibangun' => null, 'luas_tanah' => null, 'luas_bangunan' => null, 'keterangan' => null],
            ['id_pasar' => 1, 'id_induk' => $blokII, 'nama_lokasi' => 'Lantai 2', 'tipe_lokasi' => 'Lantai', 'tahun_mulai_dibangun' => null, 'tahun_selesai_dibangun' => null, 'luas_tanah' => null, 'luas_bangunan' => null, 'keterangan' => 'Sebagian Besar Tidak Aktif'],
            ['id_pasar' => 1, 'id_induk' => $blokII, 'nama_lokasi' => 'Lantai 3', 'tipe_lokasi' => 'Lantai', 'tahun_mulai_dibangun' => null, 'tahun_selesai_dibangun' => null, 'luas_tanah' => null, 'luas_bangunan' => null, 'keterangan' => 'UKM : 7, Sanggar Tari : 1'],
            ['id_pasar' => 1, 'id_induk' => $blokII, 'nama_lokasi' => 'Lantai 4', 'tipe_lokasi' => 'Lantai', 'tahun_mulai_dibangun' => null, 'tahun_selesai_dibangun' => null, 'luas_tanah' => null, 'luas_bangunan' => null, 'keterangan' => 'Inkubasi, Posko ME'],
            ['id_pasar' => 1, 'id_induk' => $blokII, 'nama_lokasi' => 'Lantai 5', 'tipe_lokasi' => 'Lantai', 'tahun_mulai_dibangun' => null, 'tahun_selesai_dibangun' => null, 'luas_tanah' => null, 'luas_bangunan' => null, 'keterangan' => 'HellyPad'],
        ]);

        // Blok III
        $blokIII = DB::table('lokasi')->insertGetId([
            'id_pasar' => 1,
            'id_induk' => $pasarRayaTimurI,
            'nama_lokasi' => 'Blok III',
            'tipe_lokasi' => 'Blok',
            'tahun_mulai_dibangun' => 2016,
            'tahun_selesai_dibangun' => 2017,
            'luas_tanah' => null,
            'luas_bangunan' => null,
            'keterangan' => null,
        ]);

        DB::table('lokasi')->insert([
            ['id_pasar' => 1, 'id_induk' => $blokIII, 'nama_lokasi' => 'Basement', 'tipe_lokasi' => 'Lantai', 'tahun_mulai_dibangun' => null, 'tahun_selesai_dibangun' => null, 'luas_tanah' => null, 'luas_bangunan' => null, 'keterangan' => 'Ruang Panel, Ruang Kontrol'],
            ['id_pasar' => 1, 'id_induk' => $blokIII, 'nama_lokasi' => 'Lantai 1', 'tipe_lokasi' => 'Lantai', 'tahun_mulai_dibangun' => null, 'tahun_selesai_dibangun' => null, 'luas_tanah' => null, 'luas_bangunan' => null, 'keterangan' => null],
            ['id_pasar' => 1, 'id_induk' => $blokIII, 'nama_lokasi' => 'Lantai 2', 'tipe_lokasi' => 'Lantai', 'tahun_mulai_dibangun' => null, 'tahun_selesai_dibangun' => null, 'luas_tanah' => null, 'luas_bangunan' => null, 'keterangan' => 'Sebagian Besar Tidak Aktif'],
            ['id_pasar' => 1, 'id_induk' => $blokIII, 'nama_lokasi' => 'Lantai 3', 'tipe_lokasi' => 'Lantai', 'tahun_mulai_dibangun' => null, 'tahun_selesai_dibangun' => null, 'luas_tanah' => null, 'luas_bangunan' => null, 'keterangan' => 'Kantor Lurah, Kantor Bapenda, Stand UKM-UKM, BPR'],
            ['id_pasar' => 1, 'id_induk' => $blokIII, 'nama_lokasi' => 'Lantai 4', 'tipe_lokasi' => 'Lantai', 'tahun_mulai_dibangun' => null, 'tahun_selesai_dibangun' => null, 'luas_tanah' => null, 'luas_bangunan' => null, 'keterangan' => 'Ex. MPP'],
            ['id_pasar' => 1, 'id_induk' => $blokIII, 'nama_lokasi' => 'Lantai 5', 'tipe_lokasi' => 'Lantai', 'tahun_mulai_dibangun' => null, 'tahun_selesai_dibangun' => null, 'luas_tanah' => null, 'luas_bangunan' => null, 'keterangan' => 'HellyPad'],
        ]);

        // Blok IV
        $blokIV = DB::table('lokasi')->insertGetId([
            'id_pasar' => 1,
            'id_induk' => $pasarRayaTimurI,
            'nama_lokasi' => 'Blok IV',
            'tipe_lokasi' => 'Blok',
            'tahun_mulai_dibangun' => 2014,
            'tahun_selesai_dibangun' => 2016,
            'luas_tanah' => null,
            'luas_bangunan' => null,
            'keterangan' => null,
        ]);

        DB::table('lokasi')->insert([
            ['id_pasar' => 1, 'id_induk' => $blokIV, 'nama_lokasi' => 'Lantai 1', 'tipe_lokasi' => 'Lantai', 'tahun_mulai_dibangun' => null, 'tahun_selesai_dibangun' => null, 'luas_tanah' => null, 'luas_bangunan' => null, 'keterangan' => null],
            ['id_pasar' => 1, 'id_induk' => $blokIV, 'nama_lokasi' => 'Lantai 2', 'tipe_lokasi' => 'Lantai', 'tahun_mulai_dibangun' => null, 'tahun_selesai_dibangun' => null, 'luas_tanah' => null, 'luas_bangunan' => null, 'keterangan' => null],
            ['id_pasar' => 1, 'id_induk' => $blokIV, 'nama_lokasi' => 'Lantai 3', 'tipe_lokasi' => 'Lantai', 'tahun_mulai_dibangun' => null, 'tahun_selesai_dibangun' => null, 'luas_tanah' => null, 'luas_bangunan' => null, 'keterangan' => 'Tidak Aktif'],
            ['id_pasar' => 1, 'id_induk' => $blokIV, 'nama_lokasi' => 'Lantai 4', 'tipe_lokasi' => 'Lantai', 'tahun_mulai_dibangun' => null, 'tahun_selesai_dibangun' => null, 'luas_tanah' => null, 'luas_bangunan' => null, 'keterangan' => 'Kantor'],
        ]);

        // Blok Bagonjong
        $blokBagonjong = DB::table('lokasi')->insertGetId([
            'id_pasar' => 1,
            'id_induk' => $pasarRayaTimurI,
            'nama_lokasi' => 'Blok Bagonjong',
            'tipe_lokasi' => 'Blok',
            'tahun_mulai_dibangun' => 2018,
            'tahun_selesai_dibangun' => null,
            'luas_tanah' => null,
            'luas_bangunan' => null,
            'keterangan' => null,
        ]);

        DB::table('lokasi')->insert([
            ['id_pasar' => 1, 'id_induk' => $blokBagonjong, 'nama_lokasi' => 'Lantai 1', 'tipe_lokasi' => 'Lantai', 'tahun_mulai_dibangun' => null, 'tahun_selesai_dibangun' => null, 'luas_tanah' => null, 'luas_bangunan' => null, 'keterangan' => 'Pos Damkar (10-02-2021)'],
            ['id_pasar' => 1, 'id_induk' => $blokBagonjong, 'nama_lokasi' => 'Lantai 2', 'tipe_lokasi' => 'Lantai', 'tahun_mulai_dibangun' => null, 'tahun_selesai_dibangun' => null, 'luas_tanah' => null, 'luas_bangunan' => null, 'keterangan' => 'Ruang Pengelola'],
        ]);

        // ============================================
        // 2. PASAR PEMBANTU (Simple Structure)
        // Langsung Lantai 1 & 2 tanpa Blok
        // ============================================

        // Tanah Kongsi (id_pasar = 2)
        $tanahKongsi = DB::table('lokasi')->insertGetId([
            'id_pasar' => 2,
            'id_induk' => null,
            'nama_lokasi' => 'Tanah Kongsi',
            'tipe_lokasi' => 'Pasar',
            'tahun_mulai_dibangun' => 1976,
            'tahun_selesai_dibangun' => 2003,
            'luas_tanah' => null,
            'luas_bangunan' => null,
            'keterangan' => null,
        ]);

        // Ulak Karang (id_pasar = 3)
        $ulakKarang = DB::table('lokasi')->insertGetId([
            'id_pasar' => 3,
            'id_induk' => null,
            'nama_lokasi' => 'Ulak Karang',
            'tipe_lokasi' => 'Pasar',
            'tahun_mulai_dibangun' => 1977,
            'tahun_selesai_dibangun' => null,
            'luas_tanah' => null,
            'luas_bangunan' => null,
            'keterangan' => '2023, 2025',
        ]);

        // Alai (id_pasar = 4)
        $alai = DB::table('lokasi')->insertGetId([
            'id_pasar' => 4,
            'id_induk' => null,
            'nama_lokasi' => 'Alai',
            'tipe_lokasi' => 'Pasar',
            'tahun_mulai_dibangun' => 1973,
            'tahun_selesai_dibangun' => 2008,
            'luas_tanah' => null,
            'luas_bangunan' => null,
            'keterangan' => null,
        ]);

        // Simpang Haru (id_pasar = 5)
        $simpangHaru = DB::table('lokasi')->insertGetId([
            'id_pasar' => 5,
            'id_induk' => null,
            'nama_lokasi' => 'Simpang Haru',
            'tipe_lokasi' => 'Pasar',
            'tahun_mulai_dibangun' => 1973,
            'tahun_selesai_dibangun' => 2000,
            'luas_tanah' => null,
            'luas_bangunan' => null,
            'keterangan' => null,
        ]);

        // Nanggalo (id_pasar = 6) — Punya Lantai
        $nanggalo = DB::table('lokasi')->insertGetId([
            'id_pasar' => 6,
            'id_induk' => null,
            'nama_lokasi' => 'Nanggalo',
            'tipe_lokasi' => 'Pasar',
            'tahun_mulai_dibangun' => 1984,
            'tahun_selesai_dibangun' => 2017,
            'luas_tanah' => null,
            'luas_bangunan' => null,
            'keterangan' => null,
        ]);

        DB::table('lokasi')->insert([
            ['id_pasar' => 6, 'id_induk' => $nanggalo, 'nama_lokasi' => 'Lantai 1', 'tipe_lokasi' => 'Lantai', 'tahun_mulai_dibangun' => null, 'tahun_selesai_dibangun' => null, 'luas_tanah' => null, 'luas_bangunan' => null, 'keterangan' => null],
            ['id_pasar' => 6, 'id_induk' => $nanggalo, 'nama_lokasi' => 'Lantai 2', 'tipe_lokasi' => 'Lantai', 'tahun_mulai_dibangun' => null, 'tahun_selesai_dibangun' => null, 'luas_tanah' => null, 'luas_bangunan' => null, 'keterangan' => null],
        ]);

        // Lubuk Buaya (id_pasar = 7) — Punya Lantai + Los
        $lubukBuaya = DB::table('lokasi')->insertGetId([
            'id_pasar' => 7,
            'id_induk' => null,
            'nama_lokasi' => 'Lubuk Buaya',
            'tipe_lokasi' => 'Pasar',
            'tahun_mulai_dibangun' => 1984,
            'tahun_selesai_dibangun' => 2020,
            'luas_tanah' => null,
            'luas_bangunan' => null,
            'keterangan' => null,
        ]);

        $lubukBuayaLt1 = DB::table('lokasi')->insertGetId([
            'id_pasar' => 7,
            'id_induk' => $lubukBuaya,
            'nama_lokasi' => 'Lantai 1',
            'tipe_lokasi' => 'Lantai',
            'tahun_mulai_dibangun' => null,
            'tahun_selesai_dibangun' => null,
            'luas_tanah' => null,
            'luas_bangunan' => null,
            'keterangan' => 'Tahap I & II',
        ]);

        DB::table('lokasi')->insert([
            ['id_pasar' => 7, 'id_induk' => $lubukBuayaLt1, 'nama_lokasi' => 'Los Ikan', 'tipe_lokasi' => 'Los', 'tahun_mulai_dibangun' => null, 'tahun_selesai_dibangun' => null, 'luas_tanah' => null, 'luas_bangunan' => null, 'keterangan' => null],
            ['id_pasar' => 7, 'id_induk' => $lubukBuayaLt1, 'nama_lokasi' => 'Los Daging', 'tipe_lokasi' => 'Los', 'tahun_mulai_dibangun' => null, 'tahun_selesai_dibangun' => null, 'luas_tanah' => null, 'luas_bangunan' => null, 'keterangan' => null],
            ['id_pasar' => 7, 'id_induk' => $lubukBuayaLt1, 'nama_lokasi' => 'Los Ayam', 'tipe_lokasi' => 'Los', 'tahun_mulai_dibangun' => null, 'tahun_selesai_dibangun' => null, 'luas_tanah' => null, 'luas_bangunan' => null, 'keterangan' => null],
            ['id_pasar' => 7, 'id_induk' => $lubukBuayaLt1, 'nama_lokasi' => 'Los Hasil Bumi', 'tipe_lokasi' => 'Los', 'tahun_mulai_dibangun' => null, 'tahun_selesai_dibangun' => null, 'luas_tanah' => null, 'luas_bangunan' => null, 'keterangan' => null],
            ['id_pasar' => 7, 'id_induk' => $lubukBuayaLt1, 'nama_lokasi' => 'Bagian Selatan', 'tipe_lokasi' => 'Area', 'tahun_mulai_dibangun' => null, 'tahun_selesai_dibangun' => null, 'luas_tanah' => null, 'luas_bangunan' => null, 'keterangan' => null],
        ]);

        DB::table('lokasi')->insert([
            ['id_pasar' => 7, 'id_induk' => $lubukBuaya, 'nama_lokasi' => 'Lantai 2', 'tipe_lokasi' => 'Lantai', 'tahun_mulai_dibangun' => null, 'tahun_selesai_dibangun' => null, 'luas_tanah' => null, 'luas_bangunan' => null, 'keterangan' => null],
        ]);

        // Belimbing (id_pasar = 8)
        $belimbing = DB::table('lokasi')->insertGetId([
            'id_pasar' => 8,
            'id_induk' => null,
            'nama_lokasi' => 'Belimbing',
            'tipe_lokasi' => 'Pasar',
            'tahun_mulai_dibangun' => 2018,
            'tahun_selesai_dibangun' => 2020,
            'luas_tanah' => null,
            'luas_bangunan' => null,
            'keterangan' => null,
        ]);

        // Bandar Buat (id_pasar = 9)
        $bandarBuat = DB::table('lokasi')->insertGetId([
            'id_pasar' => 9,
            'id_induk' => null,
            'nama_lokasi' => 'Bandar Buat',
            'tipe_lokasi' => 'Pasar',
            'tahun_mulai_dibangun' => 1982,
            'tahun_selesai_dibangun' => 2017,
            'luas_tanah' => null,
            'luas_bangunan' => null,
            'keterangan' => null,
        ]);

        DB::table('lokasi')->insert([
            ['id_pasar' => 9, 'id_induk' => $bandarBuat, 'nama_lokasi' => 'Lantai 1', 'tipe_lokasi' => 'Lantai', 'tahun_mulai_dibangun' => null, 'tahun_selesai_dibangun' => null, 'luas_tanah' => null, 'luas_bangunan' => null, 'keterangan' => 'WC Tidak Aktif'],
            ['id_pasar' => 9, 'id_induk' => $bandarBuat, 'nama_lokasi' => 'Lantai 2', 'tipe_lokasi' => 'Lantai', 'tahun_mulai_dibangun' => null, 'tahun_selesai_dibangun' => null, 'luas_tanah' => null, 'luas_bangunan' => null, 'keterangan' => null],
        ]);
    }
}