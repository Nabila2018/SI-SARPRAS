<?php

namespace Tests\Feature;

use App\Models\Fasilitas;
use App\Models\Laporan;
use App\Models\Lokasi;
use App\Models\Pasar;
use App\Models\ProgresPerbaikan;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UptdProgressViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_uptd_officer_sees_read_only_monitoring_progress_tab()
    {
        $roleUptd = Role::create(['nama_role' => 'Petugas UPTD']);
        $pasar = Pasar::create([
            'nama_pasar' => 'Pasar Raya',
            'alamat' => 'Jl. Pasar Raya No. 1',
        ]);
        $lokasi = Lokasi::create([
            'id_pasar' => $pasar->id_pasar,
            'nama_lokasi' => 'Blok A',
            'tipe_lokasi' => 'Blok',
        ]);
        $fasilitas = Fasilitas::create(['nama_fasilitas' => 'Kran Air']);

        $petugasUptd = User::create([
            'email' => 'uptd@sisarpras.test',
            'password' => Hash::make('password123'),
            'nama_lengkap' => 'Petugas UPTD Test',
            'id_role' => $roleUptd->id_role,
            'id_pasar' => $pasar->id_pasar,
            'status_akun' => 'Aktif',
        ]);

        $laporan = Laporan::create([
            'id_lokasi' => $lokasi->id_lokasi,
            'id_fasilitas' => $fasilitas->id_fasilitas,
            'id_pelapor' => $petugasUptd->id_user,
            'kategori_laporan' => 'Sanitasi & Air',
            'item_kerusakan' => 'Kran air bocor di Blok A',
            'deskripsi_kerusakan' => 'Kran air tidak dapat ditutup rapat',
            'kondisi_diharapkan' => 'Diperbaiki',
            'tanggal_lapor' => now(),
            'status_laporan' => 'Diproses',
            'status_verifikasi_rab' => 'Disetujui',
            'kategori_kerusakan' => 'Ringan',
        ]);

        // Add 2 progress updates (0% and 50%)
        $progres0 = ProgresPerbaikan::create([
            'id_laporan' => $laporan->id_laporan,
            'persentase_penyelesaian' => '0',
            'keterangan_perkembangan' => 'Dokumentasi kondisi awal.',
            'tanggal_update' => '2025-06-29 08:00:00',
        ]);

        $progres50 = ProgresPerbaikan::create([
            'id_laporan' => $laporan->id_laporan,
            'persentase_penyelesaian' => '50',
            'keterangan_perkembangan' => 'Rangka atap diganti, pemasangan genteng 50%.',
            'tanggal_update' => '2025-07-02 10:00:00',
        ]);

        $response = $this->actingAs($petugasUptd)
            ->get(route('laporan.show', $laporan->id_laporan) . '?tab=progress');

        $response->assertStatus(200);

        // Assert Header & Milestone Titles
        $response->assertSee('Progress Perbaikan');
        $response->assertSee('Status: Sedang Berjalan');
        $response->assertSee('Kondisi Awal');
        $response->assertSee('Proses (50%)');
        $response->assertSee('Selesai (100%)');
        $response->assertSee('Belum tersedia');

        // Assert History Content & Dates
        $response->assertSee('Riwayat Progres');
        $response->assertSee('Dokumentasi kondisi awal.');
        $response->assertSee('Rangka atap diganti, pemasangan genteng 50%.');
        $response->assertSee('2025');

        // Verify history chronological order (0% before 50%)
        $content = $response->getContent();
        $pos0 = strpos($content, 'Dokumentasi kondisi awal.');
        $pos50 = strpos($content, 'Rangka atap diganti, pemasangan genteng 50%.');
        $this->assertTrue($pos0 !== false && $pos50 !== false && $pos0 < $pos50);

        // Assert NO edit controls or progress input forms exist
        $response->assertDontSee('name="keterangan_perkembangan"', false);
        $response->assertDontSee('name="foto_progres[]"', false);
        $response->assertDontSee('Simpan Progres');
        $response->assertDontSee('Tambah Progres');
        $response->assertDontSee('textarea');
    }
}
