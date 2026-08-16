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

class KadinLaporanTest extends TestCase
{
    use RefreshDatabase;

    protected User $kadisUser;
    protected User $staffUser;

    protected function setUp(): void
    {
        parent::setUp();

        $roleKadis = Role::create(['nama_role' => 'Kepala Dinas']);
        $roleStaff = Role::create(['nama_role' => 'Staff Sarana dan Prasarana']);

        $this->kadisUser = User::factory()->create([
            'id_role' => $roleKadis->id_role,
            'nama_lengkap' => 'Pak Kadis',
        ]);

        $this->staffUser = User::factory()->create([
            'id_role' => $roleStaff->id_role,
            'nama_lengkap' => 'Staff Test',
        ]);

        $pasarA = Pasar::create(['nama_pasar' => 'Pasar Raya', 'alamat' => 'Jl. Raya']);
        $pasarB = Pasar::create(['nama_pasar' => 'Pasar Alai', 'alamat' => 'Jl. Alai']);

        $lokasiA = Lokasi::create(['id_pasar' => $pasarA->id_pasar, 'nama_lokasi' => 'Blok A', 'tipe_lokasi' => 'Blok']);
        $lokasiB = Lokasi::create(['id_pasar' => $pasarB->id_pasar, 'nama_lokasi' => 'Blok B', 'tipe_lokasi' => 'Blok']);

        $fasilitas = Fasilitas::create(['nama_fasilitas' => 'Atap Pasar']);

        Laporan::create([
            'id_laporan' => 'LAP101',
            'id_pelapor' => $this->staffUser->id_user,
            'id_lokasi' => $lokasiA->id_lokasi,
            'id_fasilitas' => $fasilitas->id_fasilitas,
            'item_kerusakan' => 'Atap Bocor Parah',
            'deskripsi_kerusakan' => 'Atap bocor parah saat hujan deras',
            'kondisi_diharapkan' => 'Diperbaiki dengan atap seng baru',
            'tanggal_lapor' => '2026-08-10',
            'status_laporan' => 'Selesai',
            'kategori_kerusakan' => 'Berat',
        ]);

        Laporan::create([
            'id_laporan' => 'LAP102',
            'id_pelapor' => $this->staffUser->id_user,
            'id_lokasi' => $lokasiB->id_lokasi,
            'id_fasilitas' => $fasilitas->id_fasilitas,
            'item_kerusakan' => 'Lampu Mati',
            'deskripsi_kerusakan' => 'Beberapa titik lampu mati',
            'kondisi_diharapkan' => 'Lampu diganti dengan yang baru',
            'tanggal_lapor' => '2026-08-12',
            'status_laporan' => 'Diproses',
            'kategori_kerusakan' => 'Ringan',
        ]);
    }

    public function test_kadis_can_access_laporan_monitoring_list(): void
    {
        $this->actingAs($this->kadisUser);

        $response = $this->get(route('kadin.laporan.index'));

        $response->assertStatus(200);
        $response->assertSee('Daftar Laporan');
        $response->assertSee('LAP101');
        $response->assertSee('LAP102');
        $response->assertSee('Pasar Raya');
        $response->assertSee('Pasar Alai');
    }

    public function test_kadis_can_filter_reports_by_pasar_and_status(): void
    {
        $this->actingAs($this->kadisUser);

        $response = $this->get(route('kadin.laporan.index', [
            'status' => 'Selesai',
        ]));

        $response->assertStatus(200);
        $response->assertSee('LAP101');
        $response->assertDontSee('LAP102');
    }

    public function test_kadis_can_print_pdf_reports(): void
    {
        $this->actingAs($this->kadisUser);

        $response = $this->get(route('kadin.laporan.print'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }
}
