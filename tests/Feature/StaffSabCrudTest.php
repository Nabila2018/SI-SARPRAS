<?php

namespace Tests\Feature;

use App\Models\Fasilitas;
use App\Models\Laporan;
use App\Models\Lokasi;
use App\Models\Pasar;
use App\Models\Role;
use App\Models\Sab;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StaffSabCrudTest extends TestCase
{
    use RefreshDatabase;

    private function setupData(): array
    {
        $roleStaff = Role::create(['nama_role' => 'Staff Sarana dan Prasarana']);
        $roleKabid = Role::create(['nama_role' => 'Kepala Bidang']);

        $pasar = Pasar::create(['nama_pasar' => 'Pasar SAB CRUD', 'alamat' => 'Alamat CRUD']);

        $lokasi = Lokasi::create([
            'id_pasar' => $pasar->id_pasar,
            'id_induk' => null,
            'nama_lokasi' => 'Blok SAB CRUD',
            'tipe_lokasi' => 'Area',
            'tahun_dibangun' => 2020,
            'luas_tanah' => 100,
            'luas_bangunan' => 100,
        ]);

        $fasilitas = Fasilitas::create(['nama_fasilitas' => 'Atap']);

        $staff = User::create([
            'email' => 'staff_crud_sab@sisarpras.test',
            'password' => Hash::make('password123'),
            'nama_lengkap' => 'Staff SAB CRUD',
            'id_role' => $roleStaff->id_role,
            'id_pasar' => $pasar->id_pasar,
            'status_akun' => 'Aktif',
        ]);

        $kabid = User::create([
            'email' => 'kabid_crud_sab@sisarpras.test',
            'password' => Hash::make('password123'),
            'nama_lengkap' => 'Kabid SAB CRUD',
            'id_role' => $roleKabid->id_role,
            'id_pasar' => $pasar->id_pasar,
            'status_akun' => 'Aktif',
        ]);

        return compact('staff', 'kabid');
    }

    public function test_staff_can_view_sab_index_and_crud(): void
    {
        extract($this->setupData());

        $this->actingAs($staff);

        // 1. Index
        $responseIndex = $this->get(route('staff.sab.index'));
        $responseIndex->assertRedirect(route('staff.master.index', ['tab' => 'sab']));

        // 2. Store Baru
        $responseStore = $this->post(route('staff.master.sab.store'), [
            'nama_kebutuhan' => 'Batu Spilt 2/3 (m3)',
            'satuan' => 'M3',
            'harga_standar' => 350000,
            'status_aktif' => 'Aktif',
        ]);
        $responseStore->assertRedirect(route('staff.master.index', ['tab' => 'sab']));

        $sab = Sab::where('nama_kebutuhan', 'Batu Spilt 2/3 (m3)')->first();
        $this->assertNotNull($sab);
        $this->assertSame('Aktif', $sab->status_aktif);

        // 3. Update
        $responseUpdate = $this->put(route('staff.master.sab.update', $sab->id_sab), [
            'nama_kebutuhan' => 'Batu Split 2/3 Super (m3)',
            'satuan' => 'M3',
            'harga_standar' => 380000,
            'status_aktif' => 'Aktif',
        ]);
        $responseUpdate->assertRedirect(route('staff.master.index', ['tab' => 'sab']));

        $sab->refresh();
        $this->assertSame('Batu Split 2/3 Super (m3)', $sab->nama_kebutuhan);
        $this->assertEquals(380000, $sab->harga_standar);
    }

    public function test_staff_can_toggle_sab_status(): void
    {
        extract($this->setupData());

        $sab = Sab::create([
            'nama_kebutuhan' => 'Kabel Listrik NYM 2x1.5mm',
            'satuan' => 'Roll',
            'harga_standar' => 450000,
            'status_aktif' => 'Aktif',
        ]);

        $this->actingAs($staff);

        // Nonaktifkan
        $responseToggle1 = $this->patch(route('staff.master.sab.toggle-status', $sab->id_sab));
        $responseToggle1->assertRedirect(route('staff.master.index', ['tab' => 'sab']));

        $sab->refresh();
        $this->assertSame('Nonaktif', $sab->status_aktif);

        // Aktifkan kembali
        $responseToggle2 = $this->patch(route('staff.master.sab.toggle-status', $sab->id_sab));
        $responseToggle2->assertRedirect(route('staff.master.index', ['tab' => 'sab']));

        $sab->refresh();
        $this->assertSame('Aktif', $sab->status_aktif);
    }

    public function test_non_active_sab_is_filtered_out_from_rab_form_dropdown(): void
    {
        extract($this->setupData());

        $sabAktif = Sab::create([
            'nama_kebutuhan' => 'SAB Aktif Untuk RAB',
            'satuan' => 'Pcs',
            'harga_standar' => 10000,
            'status_aktif' => 'Aktif',
        ]);

        $sabNonaktif = Sab::create([
            'nama_kebutuhan' => 'SAB Nonaktif Sembunyi',
            'satuan' => 'Pcs',
            'harga_standar' => 20000,
            'status_aktif' => 'Nonaktif',
        ]);

        $this->actingAs($staff);

        $response = $this->get(route('staff.rab.create'));
        $response->assertStatus(200);

        $response->assertSee('SAB Aktif Untuk RAB');
        $response->assertDontSee('SAB Nonaktif Sembunyi');
    }

    public function test_non_staff_role_cannot_access_sab_crud(): void
    {
        extract($this->setupData());

        $this->actingAs($kabid);

        $responseIndex = $this->get(route('staff.sab.index'));
        $responseIndex->assertStatus(403);

        $responseStore = $this->post(route('staff.sab.store'), [
            'nama_kebutuhan' => 'Illegal Item',
            'satuan' => 'Pcs',
            'harga_standar' => 1000,
        ]);
        $responseStore->assertStatus(403);
    }
}
