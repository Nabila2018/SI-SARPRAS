<?php

namespace Tests\Feature;

use App\Models\Fasilitas;
use App\Models\Laporan;
use App\Models\Lokasi;
use App\Models\Pasar;
use App\Models\Proyek;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StaffProyekTest extends TestCase
{
    use RefreshDatabase;

    private function setupData(): array
    {
        $roleStaff = Role::create(['nama_role' => 'Staff Sarana dan Prasarana']);

        $pasarA = Pasar::create(['nama_pasar' => 'Pasar Raya A', 'alamat' => 'Alamat A']);
        $pasarB = Pasar::create(['nama_pasar' => 'Pasar B', 'alamat' => 'Alamat B']);

        $lokasiA = Lokasi::create([
            'id_pasar' => $pasarA->id_pasar,
            'id_induk' => null,
            'nama_lokasi' => 'Blok A',
            'tipe_lokasi' => 'Area',
            'tahun_dibangun' => 2020,
            'luas_tanah' => 100,
            'luas_bangunan' => 100,
        ]);

        $lokasiB = Lokasi::create([
            'id_pasar' => $pasarB->id_pasar,
            'id_induk' => null,
            'nama_lokasi' => 'Blok B',
            'tipe_lokasi' => 'Area',
            'tahun_dibangun' => 2020,
            'luas_tanah' => 100,
            'luas_bangunan' => 100,
        ]);

        $fasilitas = Fasilitas::create(['nama_fasilitas' => 'Kran Air']);

        $staff1 = User::create([
            'email' => 'staff1_prj@sisarpras.test',
            'password' => Hash::make('password123'),
            'nama_lengkap' => 'Staff Pertama',
            'id_role' => $roleStaff->id_role,
            'id_pasar' => $pasarA->id_pasar,
            'status_akun' => 'Aktif',
        ]);

        $staff2 = User::create([
            'email' => 'staff2_prj@sisarpras.test',
            'password' => Hash::make('password123'),
            'nama_lengkap' => 'Staff Kedua',
            'id_role' => $roleStaff->id_role,
            'id_pasar' => $pasarA->id_pasar,
            'status_akun' => 'Aktif',
        ]);

        // Laporan A1 (Disetujui, Pasar A, belum punya proyek)
        $laporanA1 = Laporan::create([
            'id_lokasi' => $lokasiA->id_lokasi,
            'id_fasilitas' => $fasilitas->id_fasilitas,
            'id_pelapor' => $staff1->id_user,
            'kategori_laporan' => 'Sanitasi & Air',
            'item_kerusakan' => 'Kran bocor A1',
            'lokasi_spesifik' => 'Kios 1',
            'deskripsi_kerusakan' => 'Bocor',
            'kondisi_diharapkan' => 'Normal',
            'tanggal_lapor' => now(),
            'status_laporan' => 'Disetujui',
            'id_proyek' => null,
        ]);

        // Laporan A2 (Disetujui, Pasar A, belum punya proyek)
        $laporanA2 = Laporan::create([
            'id_lokasi' => $lokasiA->id_lokasi,
            'id_fasilitas' => $fasilitas->id_fasilitas,
            'id_pelapor' => $staff1->id_user,
            'kategori_laporan' => 'Sanitasi & Air',
            'item_kerusakan' => 'Kran bocor A2',
            'lokasi_spesifik' => 'Kios 2',
            'deskripsi_kerusakan' => 'Bocor',
            'kondisi_diharapkan' => 'Normal',
            'tanggal_lapor' => now(),
            'status_laporan' => 'Disetujui',
            'id_proyek' => null,
        ]);

        // Laporan Menunggu (Belum Disetujui, Pasar A)
        $laporanMenunggu = Laporan::create([
            'id_lokasi' => $lokasiA->id_lokasi,
            'id_fasilitas' => $fasilitas->id_fasilitas,
            'id_pelapor' => $staff1->id_user,
            'kategori_laporan' => 'Sanitasi & Air',
            'item_kerusakan' => 'Kran Menunggu',
            'lokasi_spesifik' => 'Kios 3',
            'deskripsi_kerusakan' => 'Bocor',
            'kondisi_diharapkan' => 'Normal',
            'tanggal_lapor' => now(),
            'status_laporan' => 'Menunggu',
            'id_proyek' => null,
        ]);

        // Laporan Pasar B (Disetujui, Pasar B)
        $laporanB = Laporan::create([
            'id_lokasi' => $lokasiB->id_lokasi,
            'id_fasilitas' => $fasilitas->id_fasilitas,
            'id_pelapor' => $staff1->id_user,
            'kategori_laporan' => 'Sanitasi & Air',
            'item_kerusakan' => 'Kran Pasar B',
            'lokasi_spesifik' => 'Kios 4',
            'deskripsi_kerusakan' => 'Bocor',
            'kondisi_diharapkan' => 'Normal',
            'tanggal_lapor' => now(),
            'status_laporan' => 'Disetujui',
            'id_proyek' => null,
        ]);

        return compact('pasarA', 'pasarB', 'staff1', 'staff2', 'laporanA1', 'laporanA2', 'laporanMenunggu', 'laporanB');
    }

    public function test_all_staff_can_view_proyek_index_and_create_form(): void
    {
        extract($this->setupData());

        $this->actingAs($staff2);

        $responseIndex = $this->get(route('staff.proyek.index'));
        $responseIndex->assertStatus(200);
        $responseIndex->assertSee('Proyek Perbaikan');

        $responseCreate = $this->get(route('staff.proyek.create', ['id_pasar' => $pasarA->id_pasar]));
        $responseCreate->assertStatus(200);
        $responseCreate->assertSee($laporanA1->id_laporan);
        $responseCreate->assertSee($laporanA2->id_laporan);
        // Laporan Menunggu tidak boleh tampil di list eligible
        $responseCreate->assertDontSee($laporanMenunggu->id_laporan);
    }

    public function test_staff_can_create_proyek_with_eligible_disetujui_laporan(): void
    {
        extract($this->setupData());

        $this->actingAs($staff1);

        $response = $this->post(route('staff.proyek.store'), [
            'nama_proyek' => 'Proyek Sanitasi Pasar Raya A',
            'deskripsi_proyek' => 'Perbaikan fasilitas sanitasi tahun 2026',
            'id_pasar' => $pasarA->id_pasar,
            'id_laporan' => [$laporanA1->id_laporan, $laporanA2->id_laporan],
        ]);

        $proyek = Proyek::first();
        $this->assertNotNull($proyek);
        $this->assertSame('PRJ001', $proyek->id_proyek);
        $this->assertSame('Proyek Sanitasi Pasar Raya A', $proyek->nama_proyek);
        $this->assertSame($pasarA->id_pasar, $proyek->id_pasar);
        $this->assertSame($staff1->id_user, $proyek->id_pembuat);

        $response->assertRedirect(route('staff.proyek.show', $proyek->id_proyek));

        $laporanA1->refresh();
        $laporanA2->refresh();
        $this->assertSame($proyek->id_proyek, $laporanA1->id_proyek);
        $this->assertSame($proyek->id_proyek, $laporanA2->id_proyek);
    }

    public function test_backend_rejects_laporan_not_disetujui(): void
    {
        extract($this->setupData());

        $this->actingAs($staff1);

        $response = $this->post(route('staff.proyek.store'), [
            'nama_proyek' => 'Proyek Uji Invalid Status',
            'id_pasar' => $pasarA->id_pasar,
            'id_laporan' => [$laporanMenunggu->id_laporan],
        ]);

        $response->assertSessionHasErrors('id_laporan');
        $this->assertCount(0, Proyek::all());
    }

    public function test_backend_rejects_laporan_from_different_pasar(): void
    {
        extract($this->setupData());

        $this->actingAs($staff1);

        $response = $this->post(route('staff.proyek.store'), [
            'nama_proyek' => 'Proyek Uji Bedas Pasar',
            'id_pasar' => $pasarA->id_pasar,
            'id_laporan' => [$laporanB->id_laporan],
        ]);

        $response->assertSessionHasErrors('id_laporan');
        $this->assertCount(0, Proyek::all());
    }

    public function test_backend_rejects_laporan_already_in_another_proyek(): void
    {
        extract($this->setupData());

        // Buat proyek 1 dulu untuk Laporan A1
        $proyek1 = Proyek::create([
            'nama_proyek' => 'Proyek 1',
            'id_pasar' => $pasarA->id_pasar,
            'id_pembuat' => $staff1->id_user,
        ]);
        $laporanA1->update(['id_proyek' => $proyek1->id_proyek]);

        $this->actingAs($staff1);

        // Coba masukkan Laporan A1 ke proyek baru
        $response = $this->post(route('staff.proyek.store'), [
            'nama_proyek' => 'Proyek 2',
            'id_pasar' => $pasarA->id_pasar,
            'id_laporan' => [$laporanA1->id_laporan],
        ]);

        $response->assertSessionHasErrors('id_laporan');
        $this->assertCount(1, Proyek::all());
    }

    public function test_staff_can_view_proyek_detail_with_associated_laporan(): void
    {
        extract($this->setupData());

        $proyek = Proyek::create([
            'nama_proyek' => 'Proyek Detail Uji',
            'id_pasar' => $pasarA->id_pasar,
            'id_pembuat' => $staff1->id_user,
        ]);
        $laporanA1->update(['id_proyek' => $proyek->id_proyek]);

        $this->actingAs($staff2); // Staff 2 bisa melihat detail proyek yang dibuat Staff 1

        $response = $this->get(route('staff.proyek.show', $proyek->id_proyek));
        $response->assertStatus(200);
        $response->assertSee('Proyek Detail Uji');
        $response->assertSee($laporanA1->id_laporan);
        $response->assertSee('Staff Pertama'); // audit pembuat
    }
}
