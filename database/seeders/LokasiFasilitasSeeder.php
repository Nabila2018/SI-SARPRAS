<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LokasiFasilitasSeeder extends Seeder
{
    /**
     * Seed tabel lokasi_fasilitas dari CSV master_lokasi_fasilitas_SI-SARPRAS_FINAL.csv.
     *
     * Logika:
     * - kode_lokasi pada CSV di-mapping ke id_lokasi dengan cara mem-parse ulang
     *   CSV master_lokasi dan mencocokkan urutan insert dengan id_lokasi di DB.
     * - nama_fasilitas pada CSV di-mapping ke id_fasilitas melalui tabel fasilitas,
     *   dengan normalisasi whitespace di sekitar karakter '/'.
     * - Hanya baris dengan jumlah > 0 yang diinsert sebagai relasi.
     * - Idempotent: menggunakan updateOrIgnore agar aman dijalankan ulang,
     *   tidak ada duplicate kombinasi id_lokasi + id_fasilitas.
     * - Tidak ada fallback parent-child.
     */
    public function run(): void
    {
        $csvPath = database_path('data/master_lokasi_fasilitas_SI-SARPRAS_FINAL.csv');

        if (!file_exists($csvPath)) {
            $this->command->warn("[LokasiFasilitasSeeder] File CSV tidak ditemukan: {$csvPath}");
            return;
        }

        $handle = fopen($csvPath, 'r');
        if ($handle === false) {
            $this->command->error("[LokasiFasilitasSeeder] Tidak dapat membuka file CSV.");
            return;
        }

        // Bangun map: kode_lokasi => id_lokasi
        $lokasiCsvPath = database_path('data/master_lokasi_SI-SARPRAS_FINAL.csv');
        $kodeLokasiMap = $this->buildKodeLokasiMap($lokasiCsvPath);

        // Bangun map: nama_fasilitas_normalized => id_fasilitas
        $fasilitasMap = $this->buildFasilitasMap();

        // Proses CSV lokasi_fasilitas
        $headers          = null;
        $inserted         = 0;
        $skippedZero      = 0;
        $missingLokasi    = [];
        $missingFasilitas = [];

        while (($row = fgetcsv($handle)) !== false) {
            // Lewati baris kosong
            if ($row === [null] || empty(array_filter($row, fn($v) => $v !== null && $v !== ''))) {
                continue;
            }

            // Ambil header (baris pertama non-kosong)
            if ($headers === null) {
                $headers = array_map(fn($h) => trim($h, "\xEF\xBB\xBF"), $row);
                continue;
            }

            if (count($row) !== count($headers)) {
                continue;
            }

            $data = array_combine($headers, $row);
            if ($data === false) {
                continue;
            }

            $kodeLokasi    = trim((string) ($data['kode_lokasi']    ?? ''));
            $namaFasilitas = trim((string) ($data['nama_fasilitas'] ?? ''));
            $jumlah        = (int) trim((string) ($data['jumlah']   ?? '0'));

            if ($kodeLokasi === '' || $namaFasilitas === '') {
                continue;
            }

            // Lewati jika jumlah tidak > 0
            if ($jumlah <= 0) {
                $skippedZero++;
                continue;
            }

            // Mapping kode_lokasi => id_lokasi
            if (!array_key_exists($kodeLokasi, $kodeLokasiMap)) {
                $missingLokasi[] = $kodeLokasi;
                continue;
            }
            $idLokasi = $kodeLokasiMap[$kodeLokasi];

            // Mapping nama_fasilitas => id_fasilitas (dengan normalisasi)
            $namaFasilitasNorm = $this->normalizeFasilitasName($namaFasilitas);
            if (!array_key_exists($namaFasilitasNorm, $fasilitasMap)) {
                $missingFasilitas[] = $namaFasilitas;
                continue;
            }
            $idFasilitas = $fasilitasMap[$namaFasilitasNorm];

            // Idempotent: insert jika belum ada, abaikan jika sudah ada
            // Tidak mengubah jumlah jika record sudah ada (pure insert-or-ignore)
            $exists = DB::table('lokasi_fasilitas')
                ->where('id_lokasi', $idLokasi)
                ->where('id_fasilitas', $idFasilitas)
                ->exists();

            if (!$exists) {
                DB::table('lokasi_fasilitas')->insert([
                    'id_lokasi'   => $idLokasi,
                    'id_fasilitas' => $idFasilitas,
                    'jumlah'      => $jumlah,
                ]);
                $inserted++;
            }
        }

        fclose($handle);

        // Laporan hasil
        $this->command->info("[LokasiFasilitasSeeder] Relasi berhasil diinsert: {$inserted}");

        if ($skippedZero > 0) {
            $this->command->info("[LokasiFasilitasSeeder] Baris dilewati (jumlah <= 0): {$skippedZero}");
        }

        if (!empty($missingLokasi)) {
            $uniqueMissing = array_unique($missingLokasi);
            $this->command->warn(
                "[LokasiFasilitasSeeder] kode_lokasi tidak ditemukan di master lokasi (" .
                count($uniqueMissing) . " unik): " .
                implode(', ', $uniqueMissing)
            );
        }

        if (!empty($missingFasilitas)) {
            $uniqueMissing = array_unique($missingFasilitas);
            $this->command->warn(
                "[LokasiFasilitasSeeder] nama_fasilitas tidak ditemukan di master fasilitas (" .
                count($uniqueMissing) . " unik): " .
                implode(', ', $uniqueMissing)
            );
        }
    }

    /**
     * Rebuild mapping kode_lokasi => id_lokasi dari CSV master_lokasi dan tabel lokasi.
     *
     * Cara kerja:
     * - LokasiSeeder insert baris satu per satu sesuai urutan CSV.
     * - id_lokasi di-generate sequential: LOC001, LOC002, LOC003, ...
     * - Dengan mengambil id_lokasi dari DB order ASC, urutan ini sama dengan urutan insert.
     * - Pasangkan kode_lokasi[i] dari CSV dengan id_lokasi[i] dari DB.
     */
    private function buildKodeLokasiMap(string $csvPath): array
    {
        $map = [];

        if (!file_exists($csvPath)) {
            $this->command->warn("[LokasiFasilitasSeeder] CSV master_lokasi tidak ditemukan: {$csvPath}");
            return $map;
        }

        $handle = fopen($csvPath, 'r');
        if ($handle === false) {
            return $map;
        }

        $headers    = null;
        $kodeUrutan = [];

        while (($row = fgetcsv($handle)) !== false) {
            if ($row === [null] || empty(array_filter($row, fn($v) => $v !== null && $v !== ''))) {
                continue;
            }
            if ($headers === null) {
                $headers = array_map(fn($h) => trim($h, "\xEF\xBB\xBF"), $row);
                continue;
            }
            if (count($row) !== count($headers)) {
                continue;
            }
            $data       = array_combine($headers, $row);
            $kodeLokasi = trim((string) ($data['kode_lokasi'] ?? ''));
            if ($kodeLokasi !== '') {
                $kodeUrutan[] = $kodeLokasi;
            }
        }
        fclose($handle);

        // Ambil id_lokasi dari DB urutan ASC (LOC001 < LOC002 < ...)
        $lokasiIds = DB::table('lokasi')
            ->orderByRaw("CAST(SUBSTRING(id_lokasi, 4) AS UNSIGNED) ASC")
            ->pluck('id_lokasi')
            ->toArray();

        $count = min(count($kodeUrutan), count($lokasiIds));
        for ($i = 0; $i < $count; $i++) {
            $map[$kodeUrutan[$i]] = $lokasiIds[$i];
        }

        return $map;
    }

    /**
     * Bangun mapping nama_fasilitas_normalized => id_fasilitas dari tabel fasilitas.
     */
    private function buildFasilitasMap(): array
    {
        $rows = DB::table('fasilitas')->get(['id_fasilitas', 'nama_fasilitas']);
        $map  = [];
        foreach ($rows as $row) {
            $normalized       = $this->normalizeFasilitasName($row->nama_fasilitas);
            $map[$normalized] = $row->id_fasilitas;
        }
        return $map;
    }

    /**
     * Normalisasi nama fasilitas untuk mengatasi perbedaan spasi di sekitar '/'.
     *
     * Contoh transformasi:
     *   "Musholla / Mesjid"           => "musholla/mesjid"
     *   "Musholla/Mesjid"             => "musholla/mesjid"
     *   "TPA / Ruangan Ibu Menyusui"  => "tpa/ruangan ibu menyusui"
     *   "Pos Kesehatan / Klinik"      => "pos kesehatan/klinik"
     */
    private function normalizeFasilitasName(string $name): string
    {
        $name = mb_strtolower(trim($name));
        $name = preg_replace('/\s*\/\s*/', '/', $name);
        $name = preg_replace('/\s+/', ' ', $name);
        return trim($name);
    }
}
