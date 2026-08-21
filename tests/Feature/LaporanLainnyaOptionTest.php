<?php

namespace Tests\Feature;

use App\Models\Fasilitas;
use App\Models\Laporan;
use App\Models\Lokasi;
use App\Models\LokasiFasilitas;
use App\Models\Pasar;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LaporanLainnyaOptionTest extends TestCase
{
    use RefreshDatabase;

    private function setupData(): array
    {
        Storage::fake('public');

        $roleUptd = Role::create(['nama_role' => 'Petugas UPTD']);
        $roleStaff = Role::create(['nama_role' => 'Staff Sarana dan Prasarana']);

        $pasar = Pasar::create([
            'nama_pasar' => 'Pasar Raya Uji',
            'alamat' => 'Jl. Pasar Uji No. 1',
        ]);

        $lokasi = Lokasi::create([
            'id_pasar' => $pasar->id_pasar,
            'id_induk' => null,
            'nama_lokasi' => 'Lantai 2 - Blok B',
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

        \Illuminate\Support\Facades\DB::table('lokasi_fasilitas')->insert([
            'id_lokasi' => $lokasi->id_lokasi,
            'id_fasilitas' => $fasilitasKios->id_fasilitas,
        ]);

        \Illuminate\Support\Facades\DB::table('lokasi_fasilitas')->insert([
            'id_lokasi' => $lokasi->id_lokasi,
            'id_fasilitas' => $fasilitasLainnya->id_fasilitas,
        ]);

        $uptdUser = User::create([
            'email' => 'uptd@sisarpras.test',
            'password' => Hash::make('password123'),
            'nama_lengkap' => 'Petugas UPTD Uji',
            'id_role' => $roleUptd->id_role,
            'id_pasar' => $pasar->id_pasar,
            'status_akun' => 'Aktif',
        ]);

        $staffUser = User::create([
            'email' => 'staff@sisarpras.test',
            'password' => Hash::make('password123'),
            'nama_lengkap' => 'Staff Uji',
            'id_role' => $roleStaff->id_role,
            'id_pasar' => $pasar->id_pasar,
            'status_akun' => 'Aktif',
        ]);

        return compact('pasar', 'lokasi', 'fasilitasKios', 'fasilitasLainnya', 'uptdUser', 'staffUser');
    }

    public function test_validation_fails_if_ruang_lainnya_or_kategori_lainnya_selected_without_manual_text(): void
    {
        extract($this->setupData());

        $this->actingAs($uptdUser);

        $response = $this->post(route('laporan.store'), [
            'id_pasar' => $pasar->id_pasar,
            'id_lokasi' => $lokasi->id_lokasi,
            'id_fasilitas' => $fasilitasLainnya->id_fasilitas,
            'nama_fasilitas_lainnya' => '',
            'kategori_laporan' => 'Lainnya',
            'kategori_laporan_lainnya' => '',
            'item_kerusakan' => 'Kipas Angin Dinding',
            'deskripsi_kerusakan' => 'Kipas angin rusak total',
            'kondisi_diharapkan' => 'Kipas diganti baru',
            'foto_laporan' => [UploadedFile::fake()->create('foto.jpg', 100, 'image/jpeg')],
        ]);

        $response->assertSessionHasErrors(['nama_fasilitas_lainnya', 'kategori_laporan_lainnya']);
    }

    public function test_successful_store_with_ruang_lainnya_and_kategori_lainnya(): void
    {
        extract($this->setupData());

        $initialFasilitasCount = Fasilitas::count();

        $this->actingAs($uptdUser);

        $response = $this->post(route('laporan.store'), [
            'id_pasar' => $pasar->id_pasar,
            'id_lokasi' => $lokasi->id_lokasi,
            'id_fasilitas' => $fasilitasLainnya->id_fasilitas,
            'nama_fasilitas_lainnya' => 'Gudang Alat Kebersihan',
            'kategori_laporan' => 'Lainnya',
            'kategori_laporan_lainnya' => 'Sistem Keamanan dan CCTV',
            'item_kerusakan' => 'Kipas Angin Dinding',
            'deskripsi_kerusakan' => 'Kipas angin rusak total',
            'kondisi_diharapkan' => 'Kipas diganti baru',
            'foto_laporan' => [UploadedFile::fake()->create('foto.jpg', 100, 'image/jpeg')],
        ]);

        $response->assertRedirect(route('laporan.index'));

        // Assert data master fasilitas TIDAK bertambah (Rule 6)
        $this->assertSame($initialFasilitasCount, Fasilitas::count());

        $laporan = Laporan::latest('tanggal_lapor')->first();
        $this->assertNotNull($laporan);
        $this->assertSame($fasilitasLainnya->id_fasilitas, $laporan->id_fasilitas);
        $this->assertSame('Gudang Alat Kebersihan', $laporan->nama_fasilitas_lainnya);
        $this->assertSame('Lainnya', $laporan->kategori_laporan);
        $this->assertSame('Sistem Keamanan dan CCTV', $laporan->kategori_laporan_lainnya);
        $this->assertSame('Kipas Angin Dinding', $laporan->item_kerusakan);

        // Assert display formatting (Rule 15)
        $this->assertSame('Ruang Lainnya (Gudang Alat Kebersihan)', $laporan->nama_fasilitas_display);
        $this->assertSame('Lainnya (Sistem Keamanan dan CCTV)', $laporan->kategori_laporan_display);
    }

    public function test_manual_inputs_are_cleared_to_null_if_regular_facility_or_category_selected(): void
    {
        extract($this->setupData());

        $this->actingAs($uptdUser);

        $response = $this->post(route('laporan.store'), [
            'id_pasar' => $pasar->id_pasar,
            'id_lokasi' => $lokasi->id_lokasi,
            'id_fasilitas' => $fasilitasKios->id_fasilitas, // Regular facility
            'nama_fasilitas_lainnya' => 'Isian Manual Yang Harus Dihapus',
            'kategori_laporan' => 'Sanitasi & Air', // Regular category
            'kategori_laporan_lainnya' => 'Isian Kategori Yang Harus Dihapus',
            'item_kerusakan' => 'Kran Bocor',
            'deskripsi_kerusakan' => 'Kran bocor parah',
            'kondisi_diharapkan' => 'Kran diganti',
            'foto_laporan' => [UploadedFile::fake()->create('foto.jpg', 100, 'image/jpeg')],
        ]);

        $response->assertRedirect(route('laporan.index'));

        $laporan = Laporan::latest('tanggal_lapor')->first();
        $this->assertNotNull($laporan);
        $this->assertNull($laporan->nama_fasilitas_lainnya);
        $this->assertNull($laporan->kategori_laporan_lainnya);
        $this->assertSame('Kios', $laporan->nama_fasilitas_display);
        $this->assertSame('Sanitasi & Air', $laporan->kategori_laporan_display);
    }

    public function test_detail_view_renders_display_labels_correctly_and_legacy_reports_open_without_error(): void
    {
        extract($this->setupData());

        // Laporan baru dengan 'Ruang Lainnya' dan Kategori 'Lainnya'
        $laporanBaru = Laporan::create([
            'id_lokasi' => $lokasi->id_lokasi,
            'id_fasilitas' => $fasilitasLainnya->id_fasilitas,
            'nama_fasilitas_lainnya' => 'Gudang Alat Kebersihan',
            'id_pelapor' => $uptdUser->id_user,
            'kategori_laporan' => 'Lainnya',
            'kategori_laporan_lainnya' => 'Sistem Keamanan dan CCTV',
            'item_kerusakan' => 'Kipas Angin Dinding',
            'lokasi_spesifik' => 'Depan kios 2',
            'deskripsi_kerusakan' => 'Kipas mati',
            'kondisi_diharapkan' => 'Kipas diganti',
            'tanggal_lapor' => now(),
            'status_laporan' => 'Menunggu',
        ]);

        // Laporan lama tanpa isian lainnya (null)
        $laporanLama = Laporan::create([
            'id_lokasi' => $lokasi->id_lokasi,
            'id_fasilitas' => $fasilitasKios->id_fasilitas,
            'nama_fasilitas_lainnya' => null,
            'id_pelapor' => $uptdUser->id_user,
            'kategori_laporan' => 'Sanitasi & Air',
            'kategori_laporan_lainnya' => null,
            'item_kerusakan' => 'Kran Air',
            'lokasi_spesifik' => 'Lantai 1',
            'deskripsi_kerusakan' => 'Bocor',
            'kondisi_diharapkan' => 'Normal',
            'tanggal_lapor' => now(),
            'status_laporan' => 'Menunggu',
        ]);

        $this->actingAs($staffUser);

        // Buka laporan baru
        $responseBaru = $this->get(route('laporan.show', $laporanBaru->id_laporan));
        $responseBaru->assertStatus(200);
        $responseBaru->assertSee('Ruang Lainnya (Gudang Alat Kebersihan)');

        // Buka laporan lama
        $responseLama = $this->get(route('laporan.show', $laporanLama->id_laporan));
        $responseLama->assertStatus(200);
        $responseLama->assertSee('Kios');
    }
}
