<?php

namespace Tests\Feature;

use App\Models\Fasilitas;
use App\Models\KategoriLaporan;
use App\Models\Lokasi;
use App\Models\Pasar;
use App\Models\Role;
use App\Models\Sab;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StaffMasterDataTest extends TestCase
{
    use RefreshDatabase;

    private function setupData(): array
    {
        $roleStaff = Role::create(['nama_role' => 'Staff Sarana dan Prasarana']);
        $roleKabid = Role::create(['nama_role' => 'Kepala Bidang']);

        $pasar = Pasar::create([
            'nama_pasar' => 'Pasar Induk Test',
            'alamat' => 'Jl. Master Test No. 1',
            'status_aktif' => 'Aktif',
        ]);

        $lokasi = Lokasi::create([
            'id_pasar' => $pasar->id_pasar,
            'id_induk' => null,
            'nama_lokasi' => 'Blok Utama',
            'tipe_lokasi' => 'Area',
            'status_aktif' => 'Aktif',
        ]);

        $fasilitas = Fasilitas::create([
            'nama_fasilitas' => 'Kran Air Master',
            'status_aktif' => 'Aktif',
        ]);

        $kategori = KategoriLaporan::create([
            'nama_kategori' => 'Sanitasi Master',
            'status_aktif' => 'Aktif',
        ]);

        $sab = Sab::create([
            'nama_kebutuhan' => 'Pipa PVC 2 inch',
            'satuan' => 'Batang',
            'harga_standar' => 65000,
            'status_aktif' => 'Aktif',
        ]);

        $staff = User::create([
            'email' => 'staff_master@sisarpras.test',
            'password' => Hash::make('password123'),
            'nama_lengkap' => 'Staff Master Data',
            'id_role' => $roleStaff->id_role,
            'id_pasar' => $pasar->id_pasar,
            'status_akun' => 'Aktif',
        ]);

        $kabid = User::create([
            'email' => 'kabid_master@sisarpras.test',
            'password' => Hash::make('password123'),
            'nama_lengkap' => 'Kabid Master Data',
            'id_role' => $roleKabid->id_role,
            'id_pasar' => $pasar->id_pasar,
            'status_akun' => 'Aktif',
        ]);

        return compact('staff', 'kabid', 'pasar', 'lokasi', 'fasilitas', 'kategori', 'sab');
    }

    public function test_staff_can_view_master_data_hub_and_tabs(): void
    {
        extract($this->setupData());

        $this->actingAs($staff);

        foreach (['pasar', 'lokasi', 'fasilitas', 'kategori', 'sab'] as $tab) {
            $response = $this->get(route('staff.master.index', ['tab' => $tab]));
            $response->assertOk();
        }
    }

    public function test_staff_can_add_edit_and_toggle_pasar(): void
    {
        extract($this->setupData());
        $this->actingAs($staff);

        // Store
        $responseStore = $this->post(route('staff.master.pasar.store'), [
            'nama_pasar' => 'Pasar Satelit Baru',
            'alamat' => 'Jl. Satelit No. 10',
        ]);
        $responseStore->assertRedirect(route('staff.master.index', ['tab' => 'pasar']));

        $newPasar = Pasar::where('nama_pasar', 'Pasar Satelit Baru')->first();
        $this->assertNotNull($newPasar);

        // Update
        $responseUpdate = $this->put(route('staff.master.pasar.update', $newPasar->id_pasar), [
            'nama_pasar' => 'Pasar Satelit Renovasi',
            'alamat' => 'Jl. Satelit No. 12',
        ]);
        $responseUpdate->assertRedirect(route('staff.master.index', ['tab' => 'pasar']));
        $this->assertSame('Pasar Satelit Renovasi', $newPasar->fresh()->nama_pasar);

        // Toggle
        $this->patch(route('staff.master.pasar.toggle-status', $newPasar->id_pasar));
        $this->assertSame('Nonaktif', $newPasar->fresh()->status_aktif);
    }

    public function test_staff_can_add_edit_and_toggle_lokasi_with_hierarchy(): void
    {
        extract($this->setupData());
        $this->actingAs($staff);

        // Store Sub-lokasi
        $responseStore = $this->post(route('staff.master.lokasi.store'), [
            'id_pasar' => $pasar->id_pasar,
            'id_induk' => $lokasi->id_lokasi,
            'nama_lokasi' => 'Kios No 01',
            'tipe_lokasi' => 'Kios',
        ]);
        $responseStore->assertRedirect(route('staff.master.index', ['tab' => 'lokasi']));

        $subLokasi = Lokasi::where('nama_lokasi', 'Kios No 01')->first();
        $this->assertNotNull($subLokasi);
        $this->assertSame($lokasi->id_lokasi, $subLokasi->id_induk);

        // Toggle
        $this->patch(route('staff.master.lokasi.toggle-status', $subLokasi->id_lokasi));
        $this->assertSame('Nonaktif', $subLokasi->fresh()->status_aktif);
    }

    public function test_staff_can_add_edit_and_toggle_fasilitas(): void
    {
        extract($this->setupData());
        $this->actingAs($staff);

        // Store
        $responseStore = $this->post(route('staff.master.fasilitas.store'), [
            'nama_fasilitas' => 'Penerangan Kios',
            'lokasi_ids' => [$lokasi->id_lokasi],
        ]);
        $responseStore->assertRedirect(route('staff.master.index', ['tab' => 'fasilitas']));

        $newFas = Fasilitas::where('nama_fasilitas', 'Penerangan Kios')->first();
        $this->assertNotNull($newFas);
        $this->assertCount(1, $newFas->lokasiFasilitas);

        // Toggle
        $this->patch(route('staff.master.fasilitas.toggle-status', $newFas->id_fasilitas));
        $this->assertSame('Nonaktif', $newFas->fresh()->status_aktif);
    }

    public function test_staff_can_add_edit_and_toggle_kategori(): void
    {
        extract($this->setupData());
        $this->actingAs($staff);

        // Store
        $responseStore = $this->post(route('staff.master.kategori.store'), [
            'nama_kategori' => 'Kategori Kebersihan',
        ]);
        $responseStore->assertRedirect(route('staff.master.index', ['tab' => 'kategori']));

        $kat = KategoriLaporan::where('nama_kategori', 'Kategori Kebersihan')->first();
        $this->assertNotNull($kat);

        // Toggle
        $this->patch(route('staff.master.kategori.toggle-status', $kat->id_kategori));
        $this->assertSame('Nonaktif', $kat->fresh()->status_aktif);
    }

    public function test_fallback_kategori_lainnya_cannot_be_disabled(): void
    {
        extract($this->setupData());
        $this->actingAs($staff);

        $lainnya = KategoriLaporan::create([
            'nama_kategori' => 'Lainnya',
            'status_aktif' => 'Aktif',
        ]);

        $response = $this->patch(route('staff.master.kategori.toggle-status', $lainnya->id_kategori));
        $response->assertSessionHas('error');
        $this->assertSame('Aktif', $lainnya->fresh()->status_aktif);
    }

    public function test_non_staff_role_cannot_access_master_data_management(): void
    {
        extract($this->setupData());
        $this->actingAs($kabid);

        $response = $this->get(route('staff.master.index'));
        $response->assertStatus(403);
    }
}
