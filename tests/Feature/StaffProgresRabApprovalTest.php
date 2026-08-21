<?php

namespace Tests\Feature;

use App\Models\Fasilitas;
use App\Models\Laporan;
use App\Models\Lokasi;
use App\Models\Pasar;
use App\Models\Rab;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StaffProgresRabApprovalTest extends TestCase
{
    use RefreshDatabase;

    private function setupData(): array
    {
        $roleStaff = Role::create(['nama_role' => 'Staff Sarana dan Prasarana']);
        $roleKabid = Role::create(['nama_role' => 'Kepala Bidang']);

        $pasar = Pasar::create(['nama_pasar' => 'Pasar Progres Test', 'alamat' => 'Alamat Progres']);

        $lokasi = Lokasi::create([
            'id_pasar' => $pasar->id_pasar,
            'id_induk' => null,
            'nama_lokasi' => 'Blok Progres',
            'tipe_lokasi' => 'Area',
            'tahun_dibangun' => 2020,
            'luas_tanah' => 100,
            'luas_bangunan' => 100,
        ]);

        $fasilitas = Fasilitas::create(['nama_fasilitas' => 'Lantai Kios']);

        $staff = User::create([
            'email' => 'staff_prog_rab@sisarpras.test',
            'password' => Hash::make('password123'),
            'nama_lengkap' => 'Staff Progres RAB',
            'id_role' => $roleStaff->id_role,
            'id_pasar' => $pasar->id_pasar,
            'status_akun' => 'Aktif',
        ]);

        $kabid = User::create([
            'email' => 'kabid_prog_rab@sisarpras.test',
            'password' => Hash::make('password123'),
            'nama_lengkap' => 'Kabid Progres RAB',
            'id_role' => $roleKabid->id_role,
            'id_pasar' => $pasar->id_pasar,
            'status_akun' => 'Aktif',
        ]);

        $rab = Rab::create([
            'id_rab' => Rab::generateId(),
            'status_verifikasi_rab' => 'Menunggu',
            'tanggal_persetujuan_awal' => null,
        ]);

        $laporan = Laporan::create([
            'id_laporan' => Laporan::generateId(),
            'id_lokasi' => $lokasi->id_lokasi,
            'id_fasilitas' => $fasilitas->id_fasilitas,
            'id_pelapor' => $staff->id_user,
            'kategori_laporan' => 'Sanitasi & Air',
            'item_kerusakan' => 'Lantai Bocor',
            'lokasi_spesifik' => 'Kios A1',
            'deskripsi_kerusakan' => 'Bocor',
            'kondisi_diharapkan' => 'Rapi',
            'tanggal_lapor' => now(),
            'status_laporan' => 'Disetujui',
            'kategori_kerusakan' => 'Sedang',
            'id_rab' => $rab->id_rab,
        ]);

        return compact('staff', 'kabid', 'laporan', 'rab');
    }

    public function test_progress_rejected_when_rab_never_approved(): void
    {
        Storage::fake('public');
        extract($this->setupData());

        $this->actingAs($staff);

        $foto = UploadedFile::fake()->create('progres0.jpg', 100, 'image/jpeg');

        $response = $this->post(route('staff.laporan.progres.store', $laporan->id_laporan), [
            'keterangan_perkembangan' => 'Mulai pengerjaan 0%',
            'foto_progres' => [$foto],
        ]);

        $response->assertSessionHas('error');
        $this->assertNull($rab->fresh()->tanggal_persetujuan_awal);
        $this->assertCount(0, $laporan->progresPerbaikan);
    }

    public function test_progress_allowed_after_first_rab_approval(): void
    {
        Storage::fake('public');
        extract($this->setupData());

        // 1. Kabid setujui RAB untuk pertama kali
        $this->actingAs($kabid);
        $responseSetujui = $this->post(route('kabid.rab.setujui', $rab->id_rab));
        $responseSetujui->assertRedirect(route('kabid.rab.index'));

        $rab->refresh();
        $this->assertSame('Disetujui', $rab->status_verifikasi_rab);
        $this->assertNotNull($rab->tanggal_persetujuan_awal);
        $firstApprovalTime = $rab->tanggal_persetujuan_awal;

        // 2. Staff menginput progres 0% -> Berhasil
        $this->actingAs($staff);
        $foto = UploadedFile::fake()->create('progres0.jpg', 100, 'image/jpeg');

        $responseProgres = $this->post(route('staff.laporan.progres.store', $laporan->id_laporan), [
            'keterangan_perkembangan' => 'Pekerjaan 0% dimulai',
            'foto_progres' => [$foto],
        ]);

        $responseProgres->assertRedirect();
        $this->assertCount(1, $laporan->fresh()->progresPerbaikan);
        $this->assertSame('0', (string)$laporan->fresh()->progresPerbaikan->first()->persentase_penyelesaian);

        // 3. Verifikasi ulang oleh Kabid tidak overwrite tanggal_persetujuan_awal
        $rab->update(['status_verifikasi_rab' => 'Menunggu']);
        $this->actingAs($kabid);
        $this->post(route('kabid.rab.setujui', $rab->id_rab));

        $this->assertSame($firstApprovalTime, $rab->fresh()->tanggal_persetujuan_awal);
    }

    public function test_progress_still_allowed_when_approved_rab_is_edited_back_to_menunggu(): void
    {
        Storage::fake('public');
        extract($this->setupData());

        // 1. Kabid setujui RAB pertama kali
        $this->actingAs($kabid);
        $this->post(route('kabid.rab.setujui', $rab->id_rab));

        // 2. Edit RAB -> Status kembali ke Menunggu
        $this->actingAs($staff);
        $this->put(route('staff.rab.update', $rab->id_rab), [
            'action' => 'submit',
            'rincian_kebutuhan' => ['Semen Tambahan'],
            'volume' => [2],
            'satuan' => ['sak'],
            'harga_satuan' => [75000],
        ]);

        $rab->refresh();
        $this->assertSame('Menunggu', $rab->status_verifikasi_rab);
        $this->assertNotNull($rab->tanggal_persetujuan_awal);

        // 3. Staff tetap boleh menginput progres perbaikan
        $foto = UploadedFile::fake()->create('progres0.jpg', 100, 'image/jpeg');

        $responseProgres = $this->post(route('staff.laporan.progres.store', $laporan->id_laporan), [
            'keterangan_perkembangan' => 'Pekerjaan 0% berjalan saat revisi RAB',
            'foto_progres' => [$foto],
        ]);

        $responseProgres->assertRedirect();
        $this->assertCount(1, $laporan->fresh()->progresPerbaikan);
    }
}
