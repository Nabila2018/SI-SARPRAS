<?php

namespace Tests\Feature;

use App\Models\Fasilitas;
use App\Models\Laporan;
use App\Models\Lokasi;
use App\Models\Notifikasi;
use App\Models\Pasar;
use App\Models\Rab;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class NotifikasiTest extends TestCase
{
    use RefreshDatabase;

    private function setupRolesAndUsers(): array
    {
        $roleUptd = Role::create(['nama_role' => 'Petugas UPTD']);
        $roleStaff = Role::create(['nama_role' => 'Staff Sarana dan Prasarana']);
        $roleKabid = Role::create(['nama_role' => 'Kepala Bidang']);

        $pasar = Pasar::create([
            'nama_pasar' => 'Pasar Notif Test',
            'alamat' => 'Jl. Notif No. 1',
            'status_aktif' => 'Aktif',
        ]);

        $lokasi = Lokasi::create([
            'id_pasar' => $pasar->id_pasar,
            'id_induk' => null,
            'nama_lokasi' => 'Area Notif',
            'tipe_lokasi' => 'Area',
            'status_aktif' => 'Aktif',
        ]);

        $fasilitas = Fasilitas::create([
            'nama_fasilitas' => 'Fasilitas Notif',
            'status_aktif' => 'Aktif',
        ]);

        \App\Models\LokasiFasilitas::create([
            'id_lokasi' => $lokasi->id_lokasi,
            'id_fasilitas' => $fasilitas->id_fasilitas,
            'jumlah' => 1,
        ]);

        $uptd = User::create([
            'email' => 'uptd_notif@sisarpras.test',
            'password' => Hash::make('password123'),
            'nama_lengkap' => 'Petugas UPTD Notif',
            'id_role' => $roleUptd->id_role,
            'id_pasar' => $pasar->id_pasar,
            'status_akun' => 'Aktif',
        ]);

        $staff = User::create([
            'email' => 'staff_notif@sisarpras.test',
            'password' => Hash::make('password123'),
            'nama_lengkap' => 'Staff Sarpras Notif',
            'id_role' => $roleStaff->id_role,
            'id_pasar' => $pasar->id_pasar,
            'status_akun' => 'Aktif',
        ]);

        $kabid = User::create([
            'email' => 'kabid_notif@sisarpras.test',
            'password' => Hash::make('password123'),
            'nama_lengkap' => 'Kabid Notif',
            'id_role' => $roleKabid->id_role,
            'id_pasar' => $pasar->id_pasar,
            'status_akun' => 'Aktif',
        ]);

        return compact('uptd', 'staff', 'kabid', 'pasar', 'lokasi', 'fasilitas');
    }

    public function test_event_1_uptd_creates_report_notifies_staff(): void
    {
        extract($this->setupRolesAndUsers());

        $this->actingAs($uptd);

        $response = $this->post(route('laporan.store'), [
            'id_pasar' => $pasar->id_pasar,
            'id_lokasi' => $lokasi->id_lokasi,
            'id_fasilitas' => $fasilitas->id_fasilitas,
            'kategori_laporan' => 'Sanitasi & Air',
            'item_kerusakan' => 'Pipa Bocor Notif',
            'lokasi_spesifik' => 'Dekat pintu masuk',
            'deskripsi_kerusakan' => 'Deskripsi pipa bocor',
            'kondisi_diharapkan' => 'Air lancar kembali',
            'foto_laporan' => [UploadedFile::fake()->create('laporan.png', 100, 'image/png')],
        ]);

        $response->assertRedirect(route('laporan.index'));

        $laporan = Laporan::where('item_kerusakan', 'Pipa Bocor Notif')->first();
        $this->assertNotNull($laporan);

        // Verify Staff notification created
        $notif = Notifikasi::where('id_user', $staff->id_user)
            ->where('id_laporan', $laporan->id_laporan)
            ->first();

        $this->assertNotNull($notif);
        $this->assertSame('Laporan Masuk Baru', $notif->judul_notifikasi);
        $this->assertStringContainsString($laporan->id_laporan, $notif->pesan_notifikasi);
    }

    public function test_event_2_and_3_staff_evaluates_and_kabid_verifies(): void
    {
        extract($this->setupRolesAndUsers());

        $laporan = Laporan::create([
            'id_laporan' => Laporan::generateId(),
            'id_lokasi' => $lokasi->id_lokasi,
            'id_fasilitas' => $fasilitas->id_fasilitas,
            'id_pelapor' => $uptd->id_user,
            'kategori_laporan' => 'Sanitasi & Air',
            'item_kerusakan' => 'Kerusakan Keramik',
            'lokasi_spesifik' => 'Kios A1',
            'deskripsi_kerusakan' => 'Pecah 5 ubin',
            'kondisi_diharapkan' => 'Rapi kembali',
            'tanggal_lapor' => now(),
            'status_laporan' => 'Menunggu',
            'kategori_kerusakan' => 'Ringan',
            'catatan_pemeriksaan' => 'Dapat diperbaiki cepat',
        ]);

        // Event 2: Staff forward evaluation -> Kabid
        $this->actingAs($staff);
        $this->post(route('staff.laporan.forward', $laporan->id_laporan));

        $notifKabid = Notifikasi::where('id_user', $kabid->id_user)
            ->where('judul_notifikasi', 'Evaluasi Laporan Baru')
            ->first();

        $this->assertNotNull($notifKabid);

        // Event 3: Kabid setujui evaluasi -> Staff
        $this->actingAs($kabid);
        $this->post(route('kabid.laporan.setujui', $laporan->id_laporan));

        $notifStaff = Notifikasi::where('id_user', $staff->id_user)
            ->where('judul_notifikasi', 'Evaluasi Laporan Disetujui')
            ->first();

        $this->assertNotNull($notifStaff);
    }

    public function test_event_4_and_5_staff_submits_rab_and_kabid_verifies(): void
    {
        extract($this->setupRolesAndUsers());

        $laporan = Laporan::create([
            'id_laporan' => Laporan::generateId(),
            'id_lokasi' => $lokasi->id_lokasi,
            'id_fasilitas' => $fasilitas->id_fasilitas,
            'id_pelapor' => $uptd->id_user,
            'kategori_laporan' => 'Sanitasi & Air',
            'item_kerusakan' => 'Kerusakan Atap',
            'lokasi_spesifik' => 'Blok B',
            'deskripsi_kerusakan' => 'Bocor saat hujan',
            'kondisi_diharapkan' => 'Atap diganti',
            'tanggal_lapor' => now(),
            'status_laporan' => 'Disetujui',
            'kategori_kerusakan' => 'Ringan',
        ]);

        // Event 4: Staff submit RAB -> Kabid
        $this->actingAs($staff);
        $responseRab = $this->post(route('staff.rab.store'), [
            'laporan_ids' => [$laporan->id_laporan],
            'rincian_kebutuhan' => ['Seng Gelombang 0.3mm'],
            'volume' => [10],
            'satuan' => ['Lembar'],
            'harga_satuan' => [75000],
            'action' => 'submit',
        ]);

        $rab = Rab::first();
        $this->assertNotNull($rab);

        $notifKabid = Notifikasi::where('id_user', $kabid->id_user)
            ->where('judul_notifikasi', 'Pengajuan RAB Baru')
            ->first();

        $this->assertNotNull($notifKabid);

        // Event 5: Kabid setujui RAB -> Staff
        $this->actingAs($kabid);
        $this->post(route('kabid.rab.setujui', $rab->id_rab));

        $notifStaff = Notifikasi::where('id_user', $staff->id_user)
            ->where('judul_notifikasi', 'RAB Disetujui Kabid')
            ->first();

        $this->assertNotNull($notifStaff);
    }

    public function test_notification_api_mark_read_and_mark_all_read(): void
    {
        extract($this->setupRolesAndUsers());

        $notif1 = Notifikasi::create([
            'id_user' => $uptd->id_user,
            'judul_notifikasi' => 'Notif Test 1',
            'pesan_notifikasi' => 'Pesan 1',
            'link_target' => route('laporan.index'),
            'is_read' => 0,
            'created_at' => now(),
        ]);

        $notif2 = Notifikasi::create([
            'id_user' => $uptd->id_user,
            'judul_notifikasi' => 'Notif Test 2',
            'pesan_notifikasi' => 'Pesan 2',
            'link_target' => route('laporan.index'),
            'is_read' => 0,
            'created_at' => now(),
        ]);

        $this->actingAs($uptd);

        // 1. Check API endpoint
        $resApi = $this->get(route('notifikasi.api'));
        $resApi->assertOk()
            ->assertJsonPath('unread_count', 2);

        // 2. Click Notif 1 -> Mark as read & redirect
        $resRead = $this->get(route('notifikasi.read', $notif1->id_notifikasi));
        $resRead->assertRedirect(route('laporan.index'));
        $this->assertSame(1, (int) $notif1->fresh()->is_read);

        // 3. Mark All Read
        $resMarkAll = $this->post(route('notifikasi.mark-all-read'));
        $resMarkAll->assertRedirect();
        $this->assertSame(1, (int) $notif2->fresh()->is_read);
    }
}
