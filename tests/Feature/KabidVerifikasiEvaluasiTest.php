<?php

namespace Tests\Feature;

use App\Models\Fasilitas;
use App\Models\Laporan;
use App\Models\Lokasi;
use App\Models\Pasar;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KabidVerifikasiEvaluasiTest extends TestCase
{
    use RefreshDatabase;

    protected function setupData()
    {
        $roleKabid = Role::create([
            'nama_role' => 'Kepala Bidang',
            'deskripsi' => 'Kepala Bidang Sarpras',
            'status_aktif' => 'Aktif',
        ]);

        $kabid = User::create([
            'nama_lengkap' => 'Budi Kabid',
            'username' => 'kabid1',
            'email' => 'kabid@sarpras.com',
            'password' => bcrypt('password'),
            'id_role' => $roleKabid->id_role,
            'status_aktif' => 'Aktif',
        ]);

        $pasar = Pasar::create([
            'nama_pasar' => 'Pasar Raya',
            'alamat' => 'Jl. Pasar Raya No. 1',
            'status_aktif' => 'Aktif',
        ]);

        $lokasi = Lokasi::create([
            'id_pasar' => $pasar->id_pasar,
            'nama_lokasi' => 'Blok A',
            'tipe_lokasi' => 'Blok',
            'status_aktif' => 'Aktif',
        ]);

        $fasilitas = Fasilitas::create([
            'nama_fasilitas' => 'Kios Utama',
            'status_aktif' => 'Aktif',
        ]);

        return compact('kabid', 'pasar', 'lokasi', 'fasilitas');
    }

    public function test_kabid_can_view_all_filter_and_menunggu_verifikasi_appears_first(): void
    {
        extract($this->setupData());

        $this->actingAs($kabid);

        // Laporan 1: Disetujui (dibuat lebih baru)
        $lapApproved = Laporan::create([
            'id_laporan' => 'LAP101',
            'id_pelapor' => $kabid->id_user,
            'id_lokasi' => $lokasi->id_lokasi,
            'id_fasilitas' => $fasilitas->id_fasilitas,
            'item_kerusakan' => 'Atap Bocor',
            'deskripsi_kerusakan' => 'Atap bocor parah',
            'kondisi_diharapkan' => 'Diperbaiki dengan baik',
            'kategori_laporan' => 'Fasilitas Umum',
            'status_laporan' => 'Disetujui',
            'kategori_kerusakan' => 'Ringan',
            'tanggal_lapor' => now(),
        ]);

        // Laporan 2: Diproses (Menunggu Verifikasi, dibuat lebih lama)
        $lapPending = Laporan::create([
            'id_laporan' => 'LAP102',
            'id_pelapor' => $kabid->id_user,
            'id_lokasi' => $lokasi->id_lokasi,
            'id_fasilitas' => $fasilitas->id_fasilitas,
            'item_kerusakan' => 'Pintu Rusak',
            'deskripsi_kerusakan' => 'Pintu lepas',
            'kondisi_diharapkan' => 'Pintu terpasang kembali',
            'kategori_laporan' => 'Fasilitas Umum',
            'status_laporan' => 'Diproses',
            'kategori_kerusakan' => 'Sedang',
            'tanggal_lapor' => now()->subDay(),
        ]);

        // Filter 'semua'
        $response = $this->get(route('kabid.laporan.index', ['status' => 'semua']));
        $response->assertStatus(200);
        $response->assertSee('Semua');

        // Pastikan laporan 'Diproses' (Menunggu Verifikasi) muncul lebih dahulu daripada 'Disetujui'
        $laporans = $response->viewData('laporans');
        $this->assertEquals('LAP102', $laporans->first()->id_laporan);
    }
}
