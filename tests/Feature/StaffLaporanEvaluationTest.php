<?php

namespace Tests\Feature;

use App\Models\Fasilitas;
use App\Models\Laporan;
use App\Models\Lokasi;
use App\Models\Pasar;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StaffLaporanEvaluationTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_save_and_edit_evaluation_without_changing_status(): void
    {
        $role = Role::create(['nama_role' => 'Staff Sarana dan Prasarana']);
        $pasar = Pasar::create([
            'nama_pasar' => 'Pasar Uji',
            'alamat' => 'Alamat uji',
        ]);
        $lokasi = Lokasi::create([
            'id_pasar' => $pasar->id_pasar,
            'id_induk' => null,
            'nama_lokasi' => 'Blok A',
            'tipe_lokasi' => 'Area',
            'tahun_dibangun' => 2020,
            'tahun_renovasi' => null,
            'luas_tanah' => 100.00,
            'luas_bangunan' => 80.00,
            'keterangan' => null,
        ]);
        $fasilitas = Fasilitas::create([
            'nama_fasilitas' => 'Kran Air',
        ]);

        $staff = User::create([
            'username' => 'staff1',
            'password' => Hash::make('password123'),
            'nama_lengkap' => 'Staff Uji',
            'id_role' => $role->id_role,
            'id_pasar' => $pasar->id_pasar,
            'status_akun' => 'Aktif',
        ]);

        $laporan = Laporan::create([
            'id_lokasi' => $lokasi->id_lokasi,
            'id_fasilitas' => $fasilitas->id_fasilitas,
            'id_pelapor' => $staff->id_user,
            'id_spj' => null,
            'kategori_laporan' => 'Sanitasi & Air',
            'item_kerusakan' => 'Kran bocor',
            'lokasi_spesifik' => 'Depan kios 2',
            'deskripsi_kerusakan' => 'Kran air bocor',
            'kondisi_diharapkan' => 'Kran berfungsi normal',
            'tanggal_lapor' => now(),
            'status_laporan' => 'Menunggu',
        ]);

        $this->actingAs($staff);

        $this->post(route('staff.laporan.evaluasi.store', $laporan->id_laporan), [
            'kategori_kerusakan' => 'Ringan',
            'catatan_pemeriksaan' => 'Perlu pengecekan lanjutan',
        ])->assertRedirect();

        $laporan->refresh();
        $this->assertSame('Ringan', $laporan->kategori_kerusakan);
        $this->assertSame('Perlu pengecekan lanjutan', $laporan->catatan_pemeriksaan);
        $this->assertSame('Menunggu', $laporan->status_laporan);

        $this->post(route('staff.laporan.evaluasi.store', $laporan->id_laporan), [
            'kategori_kerusakan' => 'Berat',
            'catatan_pemeriksaan' => 'Perlu penanganan segera',
        ])->assertRedirect();

        $laporan->refresh();
        $this->assertSame('Berat', $laporan->kategori_kerusakan);
        $this->assertSame('Perlu penanganan segera', $laporan->catatan_pemeriksaan);
        $this->assertSame('Menunggu', $laporan->status_laporan);
    }
}
