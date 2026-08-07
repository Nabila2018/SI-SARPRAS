<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LokasiSeeder extends Seeder
{
    public function run(): void
    {
        $csvPath = database_path('data/master_lokasi_SI-SARPRAS_FINAL.csv');

        if (!file_exists($csvPath)) {
            return;
        }

        $handle = fopen($csvPath, 'r');

        if ($handle === false) {
            return;
        }

        $headers = null;
        $kodeLokasiMap = [];

        while (($row = fgetcsv($handle)) !== false) {
            if ($row === [null] || empty(array_filter($row, function ($value) {
                return $value !== null && $value !== '';
            }))) {
                continue;
            }

            if ($headers === null) {
                $headers = array_map(function ($header) {
                    return trim($header, "\xEF\xBB\xBF");
                }, $row);
                continue;
            }

            $data = array_combine($headers, $row);

            if ($data === false) {
                continue;
            }

            $kodeLokasi = trim((string) ($data['kode_lokasi'] ?? ''));

            if ($kodeLokasi === '') {
                continue;
            }

            $idInduk = null;
            $kodeInduk = trim((string) ($data['kode_induk'] ?? ''));

            if ($kodeInduk !== '') {
                if (!array_key_exists($kodeInduk, $kodeLokasiMap)) {
                    throw new \RuntimeException("Kode induk '{$kodeInduk}' tidak ditemukan dalam mapping lokasi.");
                }

                $idInduk = $kodeLokasiMap[$kodeInduk];
            }

            $rawIdPasar = trim((string) ($data['id_pasar'] ?? ''));
            $idPasar = $rawIdPasar === '' ? null : ('PSR' . str_pad((int) $rawIdPasar, 3, '0', STR_PAD_LEFT));
            $idLokasi = \App\Models\Lokasi::generateId();

            $insertData = [
                'id_lokasi' => $idLokasi,
                'id_pasar' => $idPasar,
                'id_induk' => $idInduk,
                'nama_lokasi' => trim((string) ($data['nama_lokasi'] ?? '')) !== '' ? trim((string) $data['nama_lokasi']) : null,
                'tipe_lokasi' => trim((string) ($data['tipe_lokasi'] ?? '')) !== '' ? trim((string) $data['tipe_lokasi']) : null,
                'tahun_mulai_dibangun' => trim((string) ($data['tahun_mulai_dibangun'] ?? '')) !== '' ? (int) $data['tahun_mulai_dibangun'] : null,
                'tahun_selesai_dibangun' => trim((string) ($data['tahun_selesai_dibangun'] ?? '')) !== '' ? (int) $data['tahun_selesai_dibangun'] : null,
                'luas_tanah' => trim((string) ($data['luas_tanah'] ?? '')) !== '' ? (float) $data['luas_tanah'] : null,
                'luas_bangunan' => trim((string) ($data['luas_bangunan'] ?? '')) !== '' ? (float) $data['luas_bangunan'] : null,
                'keterangan' => trim((string) ($data['keterangan'] ?? '')) !== '' ? trim((string) $data['keterangan']) : null,
            ];

            DB::table('lokasi')->insert($insertData);
            $kodeLokasiMap[$kodeLokasi] = $idLokasi;
        }

        fclose($handle);
    }
}