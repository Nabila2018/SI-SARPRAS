<?php

namespace Tests\Feature;

use App\Models\Fasilitas;
use App\Models\Laporan;
use App\Models\Lokasi;
use App\Models\Pasar;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DashboardPeriodFilterTest extends TestCase
{
    use RefreshDatabase;

    private User $uptdUser;
    private User $staffUser;
    private User $kabidUser;
    private User $kadisUser;

    private Pasar $pasar1;
    private Pasar $pasar2;
    private Lokasi $lokasi1;
    private Lokasi $lokasi2;
    private Fasilitas $fasilitas;

    protected function setUp(): void
    {
        parent::setUp();

        $roleUptd = Role::create(['nama_role' => 'Petugas UPTD']);
        $roleStaff = Role::create(['nama_role' => 'Staff Sarana dan Prasarana']);
        $roleKabid = Role::create(['nama_role' => 'Kepala Bidang']);
        $roleKadis = Role::create(['nama_role' => 'Kepala Dinas']);

        $this->pasar1 = Pasar::create(['nama_pasar' => 'Pasar Raya Test', 'alamat' => 'Jl. Pasar 1']);
        $this->pasar2 = Pasar::create(['nama_pasar' => 'Pasar Alai Test', 'alamat' => 'Jl. Pasar 2']);

        $this->lokasi1 = Lokasi::create([
            'id_pasar' => $this->pasar1->id_pasar,
            'nama_lokasi' => 'Blok A',
            'tipe_lokasi' => 'Bangunan',
        ]);

        $this->lokasi2 = Lokasi::create([
            'id_pasar' => $this->pasar2->id_pasar,
            'nama_lokasi' => 'Blok B',
            'tipe_lokasi' => 'Bangunan',
        ]);

        $this->fasilitas = Fasilitas::create([
            'nama_fasilitas' => 'Atap Pasar',
        ]);

        $this->uptdUser = User::create([
            'email' => 'uptd_dashboard@test.com',
            'password' => Hash::make('password123'),
            'nama_lengkap' => 'Petugas UPTD Test',
            'id_role' => $roleUptd->id_role,
            'id_pasar' => $this->pasar1->id_pasar,
            'status_akun' => 'Aktif',
        ]);

        $this->staffUser = User::create([
            'email' => 'staff_dashboard@test.com',
            'password' => Hash::make('password123'),
            'nama_lengkap' => 'Staff Sarpras Test',
            'id_role' => $roleStaff->id_role,
            'status_akun' => 'Aktif',
        ]);

        $this->kabidUser = User::create([
            'email' => 'kabid_dashboard@test.com',
            'password' => Hash::make('password123'),
            'nama_lengkap' => 'Kabid Sarpras Test',
            'id_role' => $roleKabid->id_role,
            'status_akun' => 'Aktif',
        ]);

        $this->kadisUser = User::create([
            'email' => 'kadis_dashboard@test.com',
            'password' => Hash::make('password123'),
            'nama_lengkap' => 'Kadis Test',
            'id_role' => $roleKadis->id_role,
            'status_akun' => 'Aktif',
        ]);

        // Create sample reports across dates
        Laporan::create([
            'id_laporan' => 'LAP001',
            'id_lokasi' => $this->lokasi1->id_lokasi,
            'id_fasilitas' => $this->fasilitas->id_fasilitas,
            'id_pelapor' => $this->uptdUser->id_user,
            'kategori_laporan' => 'Prasarana Bangunan',
            'item_kerusakan' => 'Bocor Atap Blok A',
            'deskripsi_kerusakan' => 'Deskripsi 1',
            'kondisi_diharapkan' => 'Diperbaiki',
            'tanggal_lapor' => '2026-08-10', // August 2026
            'status_laporan' => 'Menunggu',
            'kategori_kerusakan' => 'Sedang',
        ]);

        Laporan::create([
            'id_laporan' => 'LAP002',
            'id_lokasi' => $this->lokasi1->id_lokasi,
            'id_fasilitas' => $this->fasilitas->id_fasilitas,
            'id_pelapor' => $this->uptdUser->id_user,
            'kategori_laporan' => 'Prasarana Bangunan',
            'item_kerusakan' => 'Bocor Atap Blok A Lanjutan',
            'deskripsi_kerusakan' => 'Deskripsi 2',
            'kondisi_diharapkan' => 'Diperbaiki',
            'tanggal_lapor' => '2026-07-15', // July 2026
            'status_laporan' => 'Selesai',
            'kategori_kerusakan' => 'Ringan',
        ]);

        Laporan::create([
            'id_laporan' => 'LAP003',
            'id_lokasi' => $this->lokasi2->id_lokasi,
            'id_fasilitas' => $this->fasilitas->id_fasilitas,
            'id_pelapor' => $this->uptdUser->id_user,
            'kategori_laporan' => 'Instalasi Listrik',
            'item_kerusakan' => 'Kabel Putus Pasar Alai',
            'deskripsi_kerusakan' => 'Deskripsi 3',
            'kondisi_diharapkan' => 'Diganti',
            'tanggal_lapor' => '2026-08-05', // August 2026
            'status_laporan' => 'Diproses',
            'kategori_kerusakan' => 'Berat',
        ]);
    }

    public function test_default_period_loads_current_month(): void
    {
        $this->actingAs($this->staffUser);

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('Ringkasan Laporan');
        $response->assertSee('Semua Bulan');
    }

    public function test_monthly_period_filter_displays_correct_label_and_data(): void
    {
        $this->actingAs($this->staffUser);

        // July 2026 filter
        $response = $this->get(route('home', [
            'bulan' => 7,
            'tahun' => 2026,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Ringkasan Laporan Juli 2026');
        $response->assertSee('15 Jul 2026');
        $response->assertDontSee('10 Aug 2026');
    }

    public function test_yearly_period_filter_displays_all_reports_for_that_year(): void
    {
        $this->actingAs($this->staffUser);

        // Year 2026 filter (All months)
        $response = $this->get(route('home', [
            'bulan' => 'semua',
            'tahun' => 2026,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Ringkasan Laporan Tahun 2026');
        $response->assertSee('10 Aug 2026');
        $response->assertSee('15 Jul 2026');
    }

    public function test_uptd_dashboard_respects_market_scope_and_period(): void
    {
        $this->actingAs($this->uptdUser);

        // August 2026 filter for UPTD User (Assigned to Pasar Raya Test)
        $response = $this->get(route('home', [
            'bulan' => 8,
            'tahun' => 2026,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Ringkasan Laporan Agustus 2026');
        $response->assertSee('10 Aug 2026');
        $response->assertDontSee('05 Aug 2026');
    }

    public function test_kabid_and_kadis_dashboards_render_period_filters_cleanly(): void
    {
        $this->actingAs($this->kabidUser);
        $resKabid = $this->get(route('home', ['bulan' => 8, 'tahun' => 2026]));
        $resKabid->assertStatus(200);
        $resKabid->assertSee('Ringkasan Laporan Agustus 2026');

        $this->actingAs($this->kadisUser);
        $resKadis = $this->get(route('home', ['bulan' => 'semua', 'tahun' => 2026]));
        $resKadis->assertStatus(200);
        $resKadis->assertSee('Ringkasan Laporan Tahun 2026');
    }
}
