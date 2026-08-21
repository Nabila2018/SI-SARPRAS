<?php

namespace Tests\Feature;

use App\Models\Fasilitas;
use App\Models\Laporan;
use App\Models\Lokasi;
use App\Models\Pasar;
use App\Models\ProgresPerbaikan;
use App\Models\Rab;
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

        $rab = Rab::create([
            'id_rab' => Rab::generateId(),
            'status_verifikasi_rab' => 'Disetujui',
            'tanggal_persetujuan_awal' => now(),
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
            'id_rab' => $rab->id_rab,
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

        $this->actingAs($petugasUptd);

        // GET detail page with ?tab=progress
        $response = $this->get(route('laporan.show', ['id' => $laporan->id_laporan, 'tab' => 'progress']));

        $response->assertOk();
        $response->assertSee('Progress Perbaikan');
        $response->assertSee('Sedang Berjalan');
        $response->assertSee('Dokumentasi kondisi awal.');
        $response->assertSee('Rangka atap diganti, pemasangan genteng 50%.');
        $response->assertDontSee('Tambah Progres');
    }
}
