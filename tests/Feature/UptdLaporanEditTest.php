<?php

namespace Tests\Feature;

use App\Models\Fasilitas;
use App\Models\FotoLaporan;
use App\Models\Laporan;
use App\Models\Lokasi;
use App\Models\Pasar;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UptdLaporanEditTest extends TestCase
{
    use RefreshDatabase;

    private function setupData(): array
    {
        Storage::fake('public');

        $roleUptd = Role::create(['nama_role' => 'Petugas UPTD']);
        $roleStaff = Role::create(['nama_role' => 'Staff Sarana dan Prasarana']);

        $pasar = Pasar::create([
            'nama_pasar' => 'Pasar Raya Edit Test',
            'alamat' => 'Jl. Edit No. 1',
        ]);

        $lokasi1 = Lokasi::create([
            'id_pasar' => $pasar->id_pasar,
            'id_induk' => null,
            'nama_lokasi' => 'Lantai 1',
            'tipe_lokasi' => 'Area',
            'tahun_dibangun' => 2020,
            'tahun_renovasi' => null,
            'luas_tanah' => 100.00,
            'luas_bangunan' => 80.00,
            'keterangan' => null,
        ]);

        $lokasi2 = Lokasi::create([
            'id_pasar' => $pasar->id_pasar,
            'id_induk' => null,
            'nama_lokasi' => 'Lantai 2',
            'tipe_lokasi' => 'Area',
            'tahun_dibangun' => 2020,
            'tahun_renovasi' => null,
            'luas_tanah' => 100.00,
            'luas_bangunan' => 80.00,
            'keterangan' => null,
        ]);

        $fasilitasKios = Fasilitas::create([
            'id_fasilitas' => 'FAS001',
            'nama_fasilitas' => 'Kios',
        ]);

        $fasilitasLainnya = Fasilitas::create([
            'id_fasilitas' => 'FAS015',
            'nama_fasilitas' => 'Ruang Lainnya',
        ]);

        DB::table('lokasi_fasilitas')->insert([
            ['id_lokasi' => $lokasi1->id_lokasi, 'id_fasilitas' => $fasilitasKios->id_fasilitas],
            ['id_lokasi' => $lokasi1->id_lokasi, 'id_fasilitas' => $fasilitasLainnya->id_fasilitas],
            ['id_lokasi' => $lokasi2->id_lokasi, 'id_fasilitas' => $fasilitasKios->id_fasilitas],
        ]);

        $uptdUser1 = User::create([
            'email' => 'uptd1@sisarpras.test',
            'password' => Hash::make('password123'),
            'nama_lengkap' => 'Petugas UPTD 1',
            'id_role' => $roleUptd->id_role,
            'id_pasar' => $pasar->id_pasar,
            'status_akun' => 'Aktif',
        ]);

        $uptdUser2 = User::create([
            'email' => 'uptd2@sisarpras.test',
            'password' => Hash::make('password123'),
            'nama_lengkap' => 'Petugas UPTD 2',
            'id_role' => $roleUptd->id_role,
            'id_pasar' => $pasar->id_pasar,
            'status_akun' => 'Aktif',
        ]);

        $staffUser = User::create([
            'email' => 'staff@sisarpras.test',
            'password' => Hash::make('password123'),
            'nama_lengkap' => 'Staff Evaluator',
            'id_role' => $roleStaff->id_role,
            'id_pasar' => $pasar->id_pasar,
            'status_akun' => 'Aktif',
        ]);

        return compact('pasar', 'lokasi1', 'lokasi2', 'fasilitasKios', 'fasilitasLainnya', 'uptdUser1', 'uptdUser2', 'staffUser');
    }

    public function test_uptd_creator_can_view_edit_page_and_update_report_when_status_is_menunggu(): void
    {
        extract($this->setupData());

        $laporan = Laporan::create([
            'id_lokasi' => $lokasi1->id_lokasi,
            'id_fasilitas' => $fasilitasKios->id_fasilitas,
            'nama_fasilitas_lainnya' => null,
            'id_pelapor' => $uptdUser1->id_user,
            'kategori_laporan' => 'Sanitasi & Air',
            'kategori_laporan_lainnya' => null,
            'item_kerusakan' => 'Kran Air Mampet',
            'lokasi_spesifik' => 'Kamar mandi barat',
            'deskripsi_kerusakan' => 'Kran air tidak mengalir',
            'kondisi_diharapkan' => 'Air lancar',
            'tanggal_lapor' => now(),
            'status_laporan' => 'Menunggu',
        ]);

        $foto = FotoLaporan::create([
            'id_foto' => FotoLaporan::generateId(),
            'id_laporan' => $laporan->id_laporan,
            'file_foto' => 'laporan/dummy1.jpg',
        ]);

        $this->actingAs($uptdUser1);

        // Access edit page
        $editResponse = $this->get(route('laporan.edit', $laporan->id_laporan));
        $editResponse->assertStatus(200);
        $editResponse->assertSee('Edit Laporan Kerusakan');

        // Update report
        $updateResponse = $this->put(route('laporan.update', $laporan->id_laporan), [
            'id_pasar' => $pasar->id_pasar,
            'id_lokasi' => $lokasi2->id_lokasi,
            'id_fasilitas' => $fasilitasKios->id_fasilitas,
            'kategori_laporan' => 'Instalasi Listrik',
            'item_kerusakan' => 'Saklar Lampu',
            'lokasi_spesifik' => 'Selasar Utama',
            'deskripsi_kerusakan' => 'Saklar mati total dan konslet',
            'kondisi_diharapkan' => 'Saklar diganti baru',
        ]);

        $updateResponse->assertRedirect(route('laporan.show', $laporan->id_laporan));

        $laporan->refresh();
        $this->assertSame($lokasi2->id_lokasi, $laporan->id_lokasi);
        $this->assertSame('Instalasi Listrik', $laporan->kategori_laporan);
        $this->assertSame('Saklar Lampu', $laporan->item_kerusakan);
        $this->assertSame('Saklar mati total dan konslet', $laporan->deskripsi_kerusakan);
    }

    public function test_uptd_cannot_edit_report_created_by_another_user(): void
    {
        extract($this->setupData());

        $laporan = Laporan::create([
            'id_lokasi' => $lokasi1->id_lokasi,
            'id_fasilitas' => $fasilitasKios->id_fasilitas,
            'id_pelapor' => $uptdUser1->id_user,
            'kategori_laporan' => 'Sanitasi & Air',
            'item_kerusakan' => 'Kran Air',
            'deskripsi_kerusakan' => 'Bocor',
            'kondisi_diharapkan' => 'Bagus',
            'tanggal_lapor' => now(),
            'status_laporan' => 'Menunggu',
        ]);

        $this->actingAs($uptdUser2); // Different user

        $responseGet = $this->get(route('laporan.edit', $laporan->id_laporan));
        $responseGet->assertStatus(403);

        $responsePut = $this->put(route('laporan.update', $laporan->id_laporan), [
            'id_pasar' => $pasar->id_pasar,
            'id_lokasi' => $lokasi1->id_lokasi,
            'id_fasilitas' => $fasilitasKios->id_fasilitas,
            'kategori_laporan' => 'Instalasi Listrik',
            'item_kerusakan' => 'Hacker Update',
            'deskripsi_kerusakan' => 'Hacked',
            'kondisi_diharapkan' => 'Hacked',
        ]);
        $responsePut->assertStatus(403);
    }

    public function test_uptd_cannot_edit_report_if_status_is_not_menunggu(): void
    {
        extract($this->setupData());

        $laporan = Laporan::create([
            'id_lokasi' => $lokasi1->id_lokasi,
            'id_fasilitas' => $fasilitasKios->id_fasilitas,
            'id_pelapor' => $uptdUser1->id_user,
            'kategori_laporan' => 'Sanitasi & Air',
            'item_kerusakan' => 'Kran Air',
            'deskripsi_kerusakan' => 'Bocor',
            'kondisi_diharapkan' => 'Bagus',
            'tanggal_lapor' => now(),
            'status_laporan' => 'Diproses', // Status non-Menunggu
        ]);

        $this->actingAs($uptdUser1);

        $responseGet = $this->get(route('laporan.edit', $laporan->id_laporan));
        $responseGet->assertStatus(403);

        $responsePut = $this->put(route('laporan.update', $laporan->id_laporan), [
            'id_pasar' => $pasar->id_pasar,
            'id_lokasi' => $lokasi1->id_lokasi,
            'id_fasilitas' => $fasilitasKios->id_fasilitas,
            'kategori_laporan' => 'Sanitasi & Air',
            'item_kerusakan' => 'Kran Air Update',
            'deskripsi_kerusakan' => 'Bocor',
            'kondisi_diharapkan' => 'Bagus',
        ]);
        $responsePut->assertStatus(403);
    }

    public function test_uptd_cannot_edit_report_if_staff_evaluation_has_started(): void
    {
        extract($this->setupData());

        $laporan = Laporan::create([
            'id_lokasi' => $lokasi1->id_lokasi,
            'id_fasilitas' => $fasilitasKios->id_fasilitas,
            'id_pelapor' => $uptdUser1->id_user,
            'id_evaluator' => $staffUser->id_user, // Staff evaluation started!
            'kategori_kerusakan' => 'Ringan',
            'kategori_laporan' => 'Sanitasi & Air',
            'item_kerusakan' => 'Kran Air',
            'deskripsi_kerusakan' => 'Bocor',
            'kondisi_diharapkan' => 'Bagus',
            'tanggal_lapor' => now(),
            'status_laporan' => 'Menunggu',
        ]);

        $this->actingAs($uptdUser1);

        $responseGet = $this->get(route('laporan.edit', $laporan->id_laporan));
        $responseGet->assertStatus(403);

        $responsePut = $this->put(route('laporan.update', $laporan->id_laporan), [
            'id_pasar' => $pasar->id_pasar,
            'id_lokasi' => $lokasi1->id_lokasi,
            'id_fasilitas' => $fasilitasKios->id_fasilitas,
            'kategori_laporan' => 'Sanitasi & Air',
            'item_kerusakan' => 'Kran Air Update',
            'deskripsi_kerusakan' => 'Bocor',
            'kondisi_diharapkan' => 'Bagus',
        ]);
        $responsePut->assertStatus(403);
    }

    public function test_validation_requires_at_least_one_photo_to_remain(): void
    {
        extract($this->setupData());

        $laporan = Laporan::create([
            'id_lokasi' => $lokasi1->id_lokasi,
            'id_fasilitas' => $fasilitasKios->id_fasilitas,
            'id_pelapor' => $uptdUser1->id_user,
            'kategori_laporan' => 'Sanitasi & Air',
            'item_kerusakan' => 'Kran Air',
            'deskripsi_kerusakan' => 'Bocor',
            'kondisi_diharapkan' => 'Bagus',
            'tanggal_lapor' => now(),
            'status_laporan' => 'Menunggu',
        ]);

        $foto = FotoLaporan::create([
            'id_foto' => FotoLaporan::generateId(),
            'id_laporan' => $laporan->id_laporan,
            'file_foto' => 'laporan/only_photo.jpg',
        ]);

        $this->actingAs($uptdUser1);

        // Attempt to delete the only existing photo without uploading a new photo
        $response = $this->put(route('laporan.update', $laporan->id_laporan), [
            'id_pasar' => $pasar->id_pasar,
            'id_lokasi' => $lokasi1->id_lokasi,
            'id_fasilitas' => $fasilitasKios->id_fasilitas,
            'kategori_laporan' => 'Sanitasi & Air',
            'item_kerusakan' => 'Kran Air',
            'deskripsi_kerusakan' => 'Bocor',
            'kondisi_diharapkan' => 'Bagus',
            'hapus_foto' => [$foto->id_foto],
        ]);

        $response->assertSessionHasErrors(['foto_laporan']);
        $this->assertDatabaseHas('foto_laporan', ['id_foto' => $foto->id_foto]);
    }

    public function test_deleting_photo_of_another_report_is_prevented(): void
    {
        extract($this->setupData());

        $laporan1 = Laporan::create([
            'id_lokasi' => $lokasi1->id_lokasi,
            'id_fasilitas' => $fasilitasKios->id_fasilitas,
            'id_pelapor' => $uptdUser1->id_user,
            'kategori_laporan' => 'Sanitasi & Air',
            'item_kerusakan' => 'Laporan 1',
            'deskripsi_kerusakan' => 'Desc 1',
            'kondisi_diharapkan' => 'Exp 1',
            'tanggal_lapor' => now(),
            'status_laporan' => 'Menunggu',
        ]);
        $foto1 = FotoLaporan::create([
            'id_foto' => 'FLT101',
            'id_laporan' => $laporan1->id_laporan,
            'file_foto' => 'laporan/p1.jpg',
        ]);

        $laporan2 = Laporan::create([
            'id_lokasi' => $lokasi1->id_lokasi,
            'id_fasilitas' => $fasilitasKios->id_fasilitas,
            'id_pelapor' => $uptdUser1->id_user,
            'kategori_laporan' => 'Sanitasi & Air',
            'item_kerusakan' => 'Laporan 2',
            'deskripsi_kerusakan' => 'Desc 2',
            'kondisi_diharapkan' => 'Exp 2',
            'tanggal_lapor' => now(),
            'status_laporan' => 'Menunggu',
        ]);
        $foto2 = FotoLaporan::create([
            'id_foto' => 'FLT102',
            'id_laporan' => $laporan2->id_laporan,
            'file_foto' => 'laporan/p2.jpg',
        ]);

        $this->actingAs($uptdUser1);

        // Attempt to pass foto2's id_foto when updating laporan1 (should not delete foto2!)
        $this->put(route('laporan.update', $laporan1->id_laporan), [
            'id_pasar' => $pasar->id_pasar,
            'id_lokasi' => $lokasi1->id_lokasi,
            'id_fasilitas' => $fasilitasKios->id_fasilitas,
            'kategori_laporan' => 'Sanitasi & Air',
            'item_kerusakan' => 'Laporan 1 Updated',
            'deskripsi_kerusakan' => 'Desc 1',
            'kondisi_diharapkan' => 'Exp 1',
            'hapus_foto' => [$foto2->id_foto],
        ]);

        // Foto 2 of Laporan 2 MUST still exist in database!
        $this->assertDatabaseHas('foto_laporan', ['id_foto' => $foto2->id_foto]);
    }

    public function test_edit_supports_ruang_lainnya_and_kategori_lainnya_manual_inputs(): void
    {
        extract($this->setupData());

        $laporan = Laporan::create([
            'id_lokasi' => $lokasi1->id_lokasi,
            'id_fasilitas' => $fasilitasKios->id_fasilitas,
            'id_pelapor' => $uptdUser1->id_user,
            'kategori_laporan' => 'Sanitasi & Air',
            'item_kerusakan' => 'Kran Air',
            'deskripsi_kerusakan' => 'Bocor',
            'kondisi_diharapkan' => 'Bagus',
            'tanggal_lapor' => now(),
            'status_laporan' => 'Menunggu',
        ]);

        FotoLaporan::create([
            'id_foto' => FotoLaporan::generateId(),
            'id_laporan' => $laporan->id_laporan,
            'file_foto' => 'laporan/foto.jpg',
        ]);

        $this->actingAs($uptdUser1);

        $response = $this->put(route('laporan.update', $laporan->id_laporan), [
            'id_pasar' => $pasar->id_pasar,
            'id_lokasi' => $lokasi1->id_lokasi,
            'id_fasilitas' => $fasilitasLainnya->id_fasilitas,
            'nama_fasilitas_lainnya' => 'Gudang Kebersihan Edit',
            'kategori_laporan' => 'Lainnya',
            'kategori_laporan_lainnya' => 'Sistem CCTV Edit',
            'item_kerusakan' => 'Kamera Edit',
            'deskripsi_kerusakan' => 'Kamera berdebu',
            'kondisi_diharapkan' => 'Kamera dibersihkan',
        ]);

        $response->assertRedirect(route('laporan.show', $laporan->id_laporan));

        $laporan->refresh();
        $this->assertSame('Gudang Kebersihan Edit', $laporan->nama_fasilitas_lainnya);
        $this->assertSame('Sistem CCTV Edit', $laporan->kategori_laporan_lainnya);
        $this->assertSame('Ruang Lainnya (Gudang Kebersihan Edit)', $laporan->nama_fasilitas_display);
        $this->assertSame('Lainnya (Sistem CCTV Edit)', $laporan->kategori_laporan_display);
    }

    public function test_uptd_can_filter_report_history_by_status(): void
    {
        extract($this->setupData());

        $laporanMenunggu = Laporan::create([
            'id_lokasi' => $lokasi1->id_lokasi,
            'id_fasilitas' => $fasilitasKios->id_fasilitas,
            'id_pelapor' => $uptdUser1->id_user,
            'kategori_laporan' => 'Sanitasi & Air',
            'item_kerusakan' => 'Laporan Menunggu',
            'deskripsi_kerusakan' => 'Desc M',
            'kondisi_diharapkan' => 'Exp M',
            'tanggal_lapor' => now(),
            'status_laporan' => 'Menunggu',
        ]);

        $laporanSelesai = Laporan::create([
            'id_lokasi' => $lokasi1->id_lokasi,
            'id_fasilitas' => $fasilitasKios->id_fasilitas,
            'id_pelapor' => $uptdUser1->id_user,
            'kategori_laporan' => 'Sanitasi & Air',
            'item_kerusakan' => 'Laporan Selesai',
            'deskripsi_kerusakan' => 'Desc S',
            'kondisi_diharapkan' => 'Exp S',
            'tanggal_lapor' => now(),
            'status_laporan' => 'Selesai',
        ]);

        $this->actingAs($uptdUser1);

        $responseFilterMenunggu = $this->get(route('laporan.index', ['status_laporan' => 'Menunggu']));
        $responseFilterMenunggu->assertStatus(200);
        $responseFilterMenunggu->assertSee($laporanMenunggu->id_laporan);
        $responseFilterMenunggu->assertDontSee($laporanSelesai->id_laporan);

        $responseFilterSelesai = $this->get(route('laporan.index', ['status_laporan' => 'Selesai']));
        $responseFilterSelesai->assertStatus(200);
        $responseFilterSelesai->assertSee($laporanSelesai->id_laporan);
        $responseFilterSelesai->assertDontSee($laporanMenunggu->id_laporan);
    }
}
