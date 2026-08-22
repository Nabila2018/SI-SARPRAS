<?php

namespace Tests\Feature;

use App\Models\DetailRab;
use App\Models\Fasilitas;
use App\Models\Laporan;
use App\Models\Lokasi;
use App\Models\Pasar;
use App\Models\Rab;
use App\Models\Role;
use App\Models\Sab;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StaffProyekRabSabTest extends TestCase
{
    use RefreshDatabase;

    private function setupData(): array
    {
        $roleStaff = Role::create(['nama_role' => 'Staff Sarana dan Prasarana']);
        $pasar = Pasar::create(['nama_pasar' => 'Pasar Raya SAB', 'alamat' => 'Alamat SAB']);

        $lokasi = Lokasi::create([
            'id_pasar' => $pasar->id_pasar,
            'id_induk' => null,
            'nama_lokasi' => 'Blok SAB',
            'tipe_lokasi' => 'Area',
            'tahun_dibangun' => 2020,
            'luas_tanah' => 100,
            'luas_bangunan' => 100,
        ]);

        $fasilitas = Fasilitas::create(['nama_fasilitas' => 'Atap']);

        $staff = User::create([
            'email' => 'staff_sab@sisarpras.test',
            'password' => Hash::make('password123'),
            'nama_lengkap' => 'Staff SAB',
            'id_role' => $roleStaff->id_role,
            'id_pasar' => $pasar->id_pasar,
            'status_akun' => 'Aktif',
        ]);

        $sab1 = Sab::create([
            'nama_kebutuhan' => 'Semen Portland Standard',
            'satuan' => 'Sak',
            'harga_standar' => 75000,
        ]);

        $sab2 = Sab::create([
            'nama_kebutuhan' => 'Cat Tembok 20kg',
            'satuan' => 'Pail',
            'harga_standar' => 600000,
        ]);

        $laporan = Laporan::create([
            'id_lokasi' => $lokasi->id_lokasi,
            'id_fasilitas' => $fasilitas->id_fasilitas,
            'id_pelapor' => $staff->id_user,
            'kategori_laporan' => 'Sanitasi & Air',
            'item_kerusakan' => 'Atap Bocor',
            'lokasi_spesifik' => 'Kios 1',
            'deskripsi_kerusakan' => 'Bocor',
            'kondisi_diharapkan' => 'Bagus',
            'tanggal_lapor' => now(),
            'status_laporan' => 'Disetujui',
            'kategori_kerusakan' => 'Ringan',
        ]);

        return compact('staff', 'sab1', 'sab2', 'laporan');
    }

    public function test_staff_can_select_sab_item_and_save_rab_snapshot(): void
    {
        extract($this->setupData());

        $this->actingAs($staff);

        $response = $this->post(route('staff.rab.store'), [
            'laporan_ids' => [$laporan->id_laporan],
            'action' => 'submit',
            'id_sab' => [$sab1->id_sab],
            'rincian_kebutuhan' => [$sab1->nama_kebutuhan],
            'volume' => [10],
            'satuan' => [$sab1->satuan],
            'harga_satuan' => [$sab1->harga_standar],
        ]);

        $rab = Rab::first();
        $this->assertNotNull($rab);

        $response->assertRedirect(route('staff.rab.show', $rab->id_rab));

        $detail = $rab->detailRab->first();
        $this->assertNotNull($detail);
        $this->assertSame($sab1->id_sab, $detail->id_sab);
        $this->assertSame('Semen Portland Standard', $detail->rincian_kebutuhan);
        $this->assertSame('Sak', $detail->satuan);
        $this->assertEquals(75000, $detail->harga_satuan);
        $this->assertEquals(10, $detail->volume);
    }

    public function test_changing_master_sab_does_not_affect_existing_rab_snapshot(): void
    {
        extract($this->setupData());

        $this->actingAs($staff);

        // 1. Simpan RAB snapshot dengan harga SAB awal (75,000)
        $this->post(route('staff.rab.store'), [
            'laporan_ids' => [$laporan->id_laporan],
            'action' => 'submit',
            'id_sab' => [$sab1->id_sab],
            'rincian_kebutuhan' => [$sab1->nama_kebutuhan],
            'volume' => [5],
            'satuan' => [$sab1->satuan],
            'harga_satuan' => [$sab1->harga_standar],
        ]);

        $rab = Rab::first();

        // 2. Ubah Master SAB di kemudian hari (misal naik jadi 90,000)
        $sab1->update([
            'harga_standar' => 90000,
            'nama_kebutuhan' => 'Semen Premium',
        ]);

        // 3. Pastikan DetailRAB snapshot tetap tidak berubah
        $detail = $rab->fresh()->detailRab->first();
        $this->assertSame('Semen Portland Standard', $detail->rincian_kebutuhan);
        $this->assertEquals(75000, $detail->harga_satuan);
    }

    public function test_staff_can_submit_draft_rab_to_kabid_from_detail_page(): void
    {
        extract($this->setupData());

        $this->actingAs($staff);

        // 1. Simpan RAB sebagai Draft
        $this->post(route('staff.rab.store'), [
            'laporan_ids' => [$laporan->id_laporan],
            'action' => 'draft',
            'id_sab' => [$sab1->id_sab],
            'rincian_kebutuhan' => [$sab1->nama_kebutuhan],
            'volume' => [5],
            'satuan' => [$sab1->satuan],
            'harga_satuan' => [$sab1->harga_standar],
        ]);

        $rab = Rab::first();
        $this->assertSame('Draft', $rab->status_verifikasi_rab);

        // 2. Kirim ke Kabid dari halaman detail
        $response = $this->post(route('staff.rab.submit', $rab->id_rab));
        $response->assertRedirect(route('staff.rab.show', $rab->id_rab));
        $response->assertSessionHas('success');

        // 3. Pastikan status berubah menjadi Menunggu
        $this->assertSame('Menunggu', $rab->fresh()->status_verifikasi_rab);
    }

    public function test_staff_can_preview_and_download_rab_pdf(): void
    {
        extract($this->setupData());

        $this->actingAs($staff);

        $this->post(route('staff.rab.store'), [
            'laporan_ids' => [$laporan->id_laporan],
            'action' => 'submit',
            'id_sab' => [$sab1->id_sab],
            'rincian_kebutuhan' => [$sab1->nama_kebutuhan],
            'volume' => [5],
            'satuan' => [$sab1->satuan],
            'harga_satuan' => [$sab1->harga_standar],
        ]);

        $rab = Rab::first();

        $response = $this->get(route('staff.rab.pdf', $rab->id_rab));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }
}
