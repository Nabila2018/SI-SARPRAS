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

    private function setupData(): array
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

        $staff1 = User::create([
            'email' => 'staff1@sisarpras.test',
            'password' => Hash::make('password123'),
            'nama_lengkap' => 'Staff Pertama',
            'id_role' => $role->id_role,
            'id_pasar' => $pasar->id_pasar,
            'status_akun' => 'Aktif',
        ]);

        $staff2 = User::create([
            'email' => 'staff2@sisarpras.test',
            'password' => Hash::make('password123'),
            'nama_lengkap' => 'Staff Kedua',
            'id_role' => $role->id_role,
            'id_pasar' => $pasar->id_pasar,
            'status_akun' => 'Aktif',
        ]);

        $laporan = Laporan::create([
            'id_lokasi' => $lokasi->id_lokasi,
            'id_fasilitas' => $fasilitas->id_fasilitas,
            'id_pelapor' => $staff1->id_user,
            'id_spj' => null,
            'kategori_laporan' => 'Sanitasi & Air',
            'item_kerusakan' => 'Kran bocor',
            'lokasi_spesifik' => 'Depan kios 2',
            'deskripsi_kerusakan' => 'Kran air bocor',
            'kondisi_diharapkan' => 'Kran berfungsi normal',
            'tanggal_lapor' => now(),
            'status_laporan' => 'Menunggu',
            'id_evaluator' => null,
        ]);

        return compact('staff1', 'staff2', 'laporan');
    }

    public function test_first_evaluator_is_saved_and_remains_unchanged_when_edited_or_forwarded_by_another_staff(): void
    {
        extract($this->setupData());

        // 1. Staff 1 mengisi evaluasi pertama kali
        $this->actingAs($staff1);
        $this->post(route('staff.laporan.evaluasi.store', $laporan->id_laporan), [
            'kategori_kerusakan' => 'Ringan',
            'catatan_pemeriksaan' => 'Catatan awal oleh staff 1',
        ])->assertRedirect();

        $laporan->refresh();
        $this->assertSame('Ringan', $laporan->kategori_kerusakan);
        $this->assertSame('Catatan awal oleh staff 1', $laporan->catatan_pemeriksaan);
        $this->assertSame($staff1->id_user, $laporan->id_evaluator);
        $this->assertSame('Staff Pertama', $laporan->evaluator->nama_lengkap);

        // 2. Staff 2 mengedit evaluasi
        $this->actingAs($staff2);
        $this->post(route('staff.laporan.evaluasi.store', $laporan->id_laporan), [
            'kategori_kerusakan' => 'Berat',
            'catatan_pemeriksaan' => 'Catatan revisi oleh staff 2',
        ])->assertRedirect();

        $laporan->refresh();
        $this->assertSame('Berat', $laporan->kategori_kerusakan);
        $this->assertSame('Catatan revisi oleh staff 2', $laporan->catatan_pemeriksaan);
        // id_evaluator HARUS TETAP Staff 1 (tidak berubah ke Staff 2)
        $this->assertSame($staff1->id_user, $laporan->id_evaluator);
        $this->assertSame('Staff Pertama', $laporan->evaluator->nama_lengkap);

        // 3. Staff 2 meneruskan laporan ke Kabid
        $this->post(route('staff.laporan.forward', $laporan->id_laporan))->assertRedirect();

        $laporan->refresh();
        $this->assertSame('Diproses', $laporan->status_laporan);
        // id_evaluator TETAP Staff 1
        $this->assertSame($staff1->id_user, $laporan->id_evaluator);
    }

    public function test_legacy_laporan_with_null_evaluator_can_be_viewed_without_error(): void
    {
        extract($this->setupData());

        $this->actingAs($staff1);

        $response = $this->get(route('laporan.show', $laporan->id_laporan));
        $response->assertStatus(200);
        $response->assertDontSee('Dievaluasi oleh:');
    }
}
