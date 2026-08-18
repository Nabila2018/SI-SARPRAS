<?php

namespace Tests\Feature;

use App\Models\Fasilitas;
use App\Models\FotoProgres;
use App\Models\Laporan;
use App\Models\Lokasi;
use App\Models\Pasar;
use App\Models\ProgresPerbaikan;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StaffProgresEditTest extends TestCase
{
    use RefreshDatabase;

    private function setupData(): array
    {
        Storage::fake('public');

        $roleStaff = Role::create(['nama_role' => 'Staff Sarana dan Prasarana']);
        $pasar = Pasar::create(['nama_pasar' => 'Pasar Uji', 'alamat' => 'Alamat Uji']);
        $lokasi = Lokasi::create([
            'id_pasar' => $pasar->id_pasar,
            'id_induk' => null,
            'nama_lokasi' => 'Blok B',
            'tipe_lokasi' => 'Area',
            'tahun_dibangun' => 2020,
            'luas_tanah' => 100,
            'luas_bangunan' => 100,
        ]);
        $fasilitas = Fasilitas::create(['nama_fasilitas' => 'Atap']);

        $staff = User::create([
            'email' => 'staff_progres@sisarpras.test',
            'password' => Hash::make('password123'),
            'nama_lengkap' => 'Staff Progres',
            'id_role' => $roleStaff->id_role,
            'id_pasar' => $pasar->id_pasar,
            'status_akun' => 'Aktif',
        ]);

        $laporan = Laporan::create([
            'id_lokasi' => $lokasi->id_lokasi,
            'id_fasilitas' => $fasilitas->id_fasilitas,
            'id_pelapor' => $staff->id_user,
            'kategori_laporan' => 'Sanitasi & Air',
            'item_kerusakan' => 'Atap Bocor',
            'lokasi_spesifik' => 'Kios 10',
            'deskripsi_kerusakan' => 'Atap bocor parah',
            'kondisi_diharapkan' => 'Atap diperbaiki',
            'tanggal_lapor' => now(),
            'status_laporan' => 'Diproses',
            'status_verifikasi_rab' => 'Disetujui',
        ]);

        $progres = ProgresPerbaikan::create([
            'id_laporan' => $laporan->id_laporan,
            'persentase_penyelesaian' => '0',
            'keterangan_perkembangan' => 'Persiapan material awal',
            'tanggal_update' => now(),
        ]);

        $foto = FotoProgres::create([
            'id_progres' => $progres->id_progres,
            'file_foto' => 'progres/awal.jpg',
        ]);

        return compact('staff', 'laporan', 'progres', 'foto');
    }

    public function test_staff_can_edit_progres_keterangan_and_add_photo(): void
    {
        extract($this->setupData());

        $this->actingAs($staff);

        $newPhoto = UploadedFile::fake()->create('baru.jpg', 500, 'image/jpeg');

        $response = $this->post(route('staff.laporan.progres.update', [
            'id' => $laporan->id_laporan,
            'id_progres' => $progres->id_progres,
        ]), [
            'keterangan_perkembangan' => 'Keterangan progres berhasil diperbarui',
            'foto_progres' => [$newPhoto],
        ]);

        $response->assertRedirect();

        $progres->refresh();
        $this->assertSame('Keterangan progres berhasil diperbarui', $progres->keterangan_perkembangan);
        $this->assertCount(2, $progres->fotoProgres);
    }
}
