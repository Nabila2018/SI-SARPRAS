<?php

namespace Tests\Feature;

use App\Models\Fasilitas;
use App\Models\Laporan;
use App\Models\Lokasi;
use App\Models\Pasar;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UptdLaporanScopingTest extends TestCase
{
    use RefreshDatabase;

    public function test_officers_in_same_market_can_both_see_each_others_reports()
    {
        $roleUptd = Role::create(['nama_role' => 'Petugas UPTD']);

        $pasarRaya = Pasar::create(['nama_pasar' => 'Pasar Raya']);
        $pasarAlai = Pasar::create(['nama_pasar' => 'Pasar Alai']);

        $lokasiRaya = Lokasi::create([
            'id_pasar' => $pasarRaya->id_pasar,
            'nama_lokasi' => 'Blok A Pasar Raya',
        ]);

        $lokasiAlai = Lokasi::create([
            'id_pasar' => $pasarAlai->id_pasar,
            'nama_lokasi' => 'Blok B Pasar Alai',
        ]);

        $fasilitas = Fasilitas::create(['nama_fasilitas' => 'Kran Air']);

        // Petugas A dan Petugas B di Pasar Raya
        $petugasA = User::create([
            'email' => 'petugasa@sisarpras.test',
            'password' => Hash::make('password123'),
            'nama_lengkap' => 'Petugas A Pasar Raya',
            'id_role' => $roleUptd->id_role,
            'id_pasar' => $pasarRaya->id_pasar,
            'status_akun' => 'Aktif',
        ]);

        $petugasB = User::create([
            'email' => 'petugasb@sisarpras.test',
            'password' => Hash::make('password123'),
            'nama_lengkap' => 'Petugas B Pasar Raya',
            'id_role' => $roleUptd->id_role,
            'id_pasar' => $pasarRaya->id_pasar,
            'status_akun' => 'Aktif',
        ]);

        // Petugas C di Pasar Alai
        $petugasC = User::create([
            'email' => 'petugasc@sisarpras.test',
            'password' => Hash::make('password123'),
            'nama_lengkap' => 'Petugas C Pasar Alai',
            'id_role' => $roleUptd->id_role,
            'id_pasar' => $pasarAlai->id_pasar,
            'status_akun' => 'Aktif',
        ]);

        // Petugas A membuat laporan untuk Pasar Raya
        $laporanA = Laporan::create([
            'id_lokasi' => $lokasiRaya->id_lokasi,
            'id_fasilitas' => $fasilitas->id_fasilitas,
            'id_pelapor' => $petugasA->id_user,
            'kategori_laporan' => 'Sanitasi & Air',
            'item_kerusakan' => 'Kran Bocor',
            'deskripsi_kerusakan' => 'Kran air tidak bisa ditutup rapat',
            'kondisi_diharapkan' => 'Kran diperbaiki',
            'tanggal_lapor' => now(),
            'status_laporan' => 'Menunggu',
        ]);

        // 1. Verifikasi id_pelapor tetap mencatat Petugas A sebagai pembuat
        $this->assertEquals($petugasA->id_user, $laporanA->id_pelapor);

        // 2. Petugas A dapat melihat laporannya sendiri di daftar dan detail
        $this->actingAs($petugasA)
            ->get(route('laporan.index'))
            ->assertStatus(200)
            ->assertSee('Kran Bocor');

        $this->actingAs($petugasA)
            ->get(route('laporan.show', $laporanA->id_laporan))
            ->assertStatus(200)
            ->assertSee('Kran Bocor');

        // 3. Petugas B (pasar yang sama) JUGA dapat melihat laporan Petugas A
        $this->actingAs($petugasB)
            ->get(route('laporan.index'))
            ->assertStatus(200)
            ->assertSee('Kran Bocor');

        $this->actingAs($petugasB)
            ->get(route('laporan.show', $laporanA->id_laporan))
            ->assertStatus(200)
            ->assertSee('Kran Bocor');

        // 4. Petugas C (pasar lain) TIDAK dapat melihat laporan Pasar Raya di daftar
        $this->actingAs($petugasC)
            ->get(route('laporan.index'))
            ->assertStatus(200)
            ->assertDontSee('Kran Bocor');

        // 5. Akses langsung URL ke laporan Pasar Raya oleh Petugas C ditolak dengan HTTP 403
        $this->actingAs($petugasC)
            ->get(route('laporan.show', $laporanA->id_laporan))
            ->assertStatus(403);
    }
}
