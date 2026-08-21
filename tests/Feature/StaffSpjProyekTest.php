<?php

namespace Tests\Feature;

use App\Models\Fasilitas;
use App\Models\Laporan;
use App\Models\Lokasi;
use App\Models\Pasar;
use App\Models\ProgresPerbaikan;
use App\Models\Rab;
use App\Models\Role;
use App\Models\Spj;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StaffSpjProyekTest extends TestCase
{
    use RefreshDatabase;

    private function setupData(): array
    {
        $roleStaff = Role::create(['nama_role' => 'Staff Sarana dan Prasarana']);

        $pasar = Pasar::create(['nama_pasar' => 'Pasar SPJ Test', 'alamat' => 'Alamat SPJ']);

        $lokasi = Lokasi::create([
            'id_pasar' => $pasar->id_pasar,
            'id_induk' => null,
            'nama_lokasi' => 'Blok SPJ',
            'tipe_lokasi' => 'Area',
            'tahun_dibangun' => 2020,
            'luas_tanah' => 100,
            'luas_bangunan' => 100,
        ]);

        $fasilitas = Fasilitas::create(['nama_fasilitas' => 'Lantai']);

        $staff = User::create([
            'email' => 'staff_spj_prj@sisarpras.test',
            'password' => Hash::make('password123'),
            'nama_lengkap' => 'Staff SPJ Proyek',
            'id_role' => $roleStaff->id_role,
            'id_pasar' => $pasar->id_pasar,
            'status_akun' => 'Aktif',
        ]);

        // RAB 1 (Seluruh Laporan 100% Selesai) -> Eligible SPJ
        $rabEligible = Rab::create([
            'id_rab' => Rab::generateId(),
            'status_verifikasi_rab' => 'Disetujui',
            'tanggal_persetujuan_awal' => now(),
        ]);
        $laporanE1 = Laporan::create([
            'id_lokasi' => $lokasi->id_lokasi,
            'id_fasilitas' => $fasilitas->id_fasilitas,
            'id_pelapor' => $staff->id_user,
            'kategori_laporan' => 'Sanitasi & Air',
            'item_kerusakan' => 'Lantai 1',
            'lokasi_spesifik' => 'Kios E1',
            'deskripsi_kerusakan' => 'Rusak',
            'kondisi_diharapkan' => 'Baik',
            'tanggal_lapor' => now(),
            'status_laporan' => 'Selesai',
            'kategori_kerusakan' => 'Ringan',
            'id_rab' => $rabEligible->id_rab,
        ]);
        ProgresPerbaikan::create([
            'id_laporan' => $laporanE1->id_laporan,
            'persentase_penyelesaian' => '100',
            'keterangan_perkembangan' => 'Selesai 100%',
            'tanggal_update' => now(),
        ]);

        // RAB 2 (Laporan Belum 100% Selesai) -> Ineligible SPJ
        $rabIneligible = Rab::create([
            'id_rab' => Rab::generateId(),
            'status_verifikasi_rab' => 'Disetujui',
            'tanggal_persetujuan_awal' => now(),
        ]);
        $laporanIE1 = Laporan::create([
            'id_lokasi' => $lokasi->id_lokasi,
            'id_fasilitas' => $fasilitas->id_fasilitas,
            'id_pelapor' => $staff->id_user,
            'kategori_laporan' => 'Sanitasi & Air',
            'item_kerusakan' => 'Lantai 2',
            'lokasi_spesifik' => 'Kios IE1',
            'deskripsi_kerusakan' => 'Rusak',
            'kondisi_diharapkan' => 'Baik',
            'tanggal_lapor' => now(),
            'status_laporan' => 'Diproses',
            'kategori_kerusakan' => 'Sedang',
            'id_rab' => $rabIneligible->id_rab,
        ]);

        return compact('staff', 'rabEligible', 'rabIneligible', 'laporanE1', 'laporanIE1');
    }

    public function test_create_form_shows_only_eligible_rab(): void
    {
        extract($this->setupData());

        $this->actingAs($staff);

        $response = $this->get(route('staff.spj.create'));

        $response->assertOk();
        $response->assertSee($rabEligible->id_rab);
        $response->assertDontSee($rabIneligible->id_rab);
    }

    public function test_staff_can_create_spj_for_eligible_rab(): void
    {
        Storage::fake('public');
        extract($this->setupData());

        $this->actingAs($staff);

        $filePdf = UploadedFile::fake()->create('spj_test.pdf', 500, 'application/pdf');

        $response = $this->post(route('staff.spj.store'), [
            'id_rab' => $rabEligible->id_rab,
            'nama_pekerjaan' => 'Pekerjaan Perbaikan Pasar SPJ Test',
            'periode_mulai' => '2026-08-01',
            'periode_selesai' => '2026-08-15',
            'keterangan' => 'SPJ Perbaikan Pasar Test',
            'file_spj' => $filePdf,
        ]);

        $response->assertRedirect(route('staff.spj.index'));
        $response->assertSessionHas('success');

        $spj = Spj::where('id_rab', $rabEligible->id_rab)->first();
        $this->assertNotNull($spj);
        $this->assertSame('Pekerjaan Perbaikan Pasar SPJ Test', $spj->nama_pekerjaan);
        $this->assertNotNull($spj->file_spj);
    }

    public function test_cannot_create_duplicate_spj_for_same_rab(): void
    {
        Storage::fake('public');
        extract($this->setupData());

        $this->actingAs($staff);

        $filePdf1 = UploadedFile::fake()->create('spj_1.pdf', 500, 'application/pdf');
        $filePdf2 = UploadedFile::fake()->create('spj_2.pdf', 500, 'application/pdf');

        // SPJ Pertama
        $this->post(route('staff.spj.store'), [
            'id_rab' => $rabEligible->id_rab,
            'nama_pekerjaan' => 'Pekerjaan SPJ Pertama',
            'periode_mulai' => '2026-08-01',
            'periode_selesai' => '2026-08-15',
            'file_spj' => $filePdf1,
        ]);

        // SPJ Kedua pada RAB yang sama
        $response2 = $this->post(route('staff.spj.store'), [
            'id_rab' => $rabEligible->id_rab,
            'nama_pekerjaan' => 'Pekerjaan SPJ Kedua',
            'periode_mulai' => '2026-08-01',
            'periode_selesai' => '2026-08-15',
            'file_spj' => $filePdf2,
        ]);

        $response2->assertSessionHas('error');
        $this->assertCount(1, Spj::where('id_rab', $rabEligible->id_rab)->get());
    }

    public function test_detail_spj_shows_linked_rab_and_reports(): void
    {
        Storage::fake('public');
        extract($this->setupData());

        $this->actingAs($staff);

        $filePdf = UploadedFile::fake()->create('spj_detail.pdf', 500, 'application/pdf');

        $this->post(route('staff.spj.store'), [
            'id_rab' => $rabEligible->id_rab,
            'nama_pekerjaan' => 'Pekerjaan Detail SPJ',
            'periode_mulai' => '2026-08-01',
            'periode_selesai' => '2026-08-15',
            'file_spj' => $filePdf,
        ]);

        $spj = Spj::where('id_rab', $rabEligible->id_rab)->first();

        $response = $this->get(route('staff.spj.show', $spj->id_spj));

        $response->assertOk();
        $response->assertSee($rabEligible->id_rab);
        $response->assertSee('Lantai');
    }
}
