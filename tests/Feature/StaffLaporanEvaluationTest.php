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

    public function test_staff_can_upload_optional_attachment_during_evaluation(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');
        extract($this->setupData());

        $this->actingAs($staff1);

        $file = \Illuminate\Http\UploadedFile::fake()->create('evaluasi_dokumentasi.pdf', 1024, 'application/pdf');

        $response = $this->post(route('staff.laporan.evaluasi.store', $laporan->id_laporan), [
            'kategori_kerusakan' => 'Sedang',
            'catatan_pemeriksaan' => 'Catatan hasil analisis teknis',
            'file_lampiran_evaluasi' => $file,
        ]);

        $response->assertRedirect();
        $laporan->refresh();

        $this->assertNotNull($laporan->file_lampiran_evaluasi);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($laporan->file_lampiran_evaluasi);
    }

    public function test_staff_can_upload_multiple_attachments_during_evaluation(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');
        extract($this->setupData());

        $this->actingAs($staff1);

        $filePdf = \Illuminate\Http\UploadedFile::fake()->create('evaluasi_dokumentasi.pdf', 1024, 'application/pdf');
        $fileImg = \Illuminate\Http\UploadedFile::fake()->create('evaluasi_foto.jpg', 600, 'image/jpeg');

        $response = $this->post(route('staff.laporan.evaluasi.store', $laporan->id_laporan), [
            'kategori_kerusakan' => 'Sedang',
            'catatan_pemeriksaan' => 'Catatan hasil analisis teknis dengan 2 lampiran (PDF dan Foto)',
            'file_lampiran_evaluasi' => [$filePdf, $fileImg],
        ]);

        $response->assertRedirect();
        $laporan->refresh();

        $this->assertCount(2, $laporan->lampiran_evaluasi_list);
        foreach ($laporan->lampiran_evaluasi_list as $savedPath) {
            \Illuminate\Support\Facades\Storage::disk('public')->assertExists($savedPath);
        }
    }

    public function test_editing_evaluasi_appends_new_attachments_without_replacing_existing(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');
        extract($this->setupData());

        $this->actingAs($staff1);

        $file1 = \Illuminate\Http\UploadedFile::fake()->create('dokumen1.pdf', 1024, 'application/pdf');
        $this->post(route('staff.laporan.evaluasi.store', $laporan->id_laporan), [
            'kategori_kerusakan' => 'Sedang',
            'catatan_pemeriksaan' => 'Catatan awal',
            'file_lampiran_evaluasi' => [$file1],
        ]);

        $laporan->refresh();
        $this->assertCount(1, $laporan->lampiran_evaluasi_list);

        $file2 = \Illuminate\Http\UploadedFile::fake()->create('foto2.jpg', 600, 'image/jpeg');
        $this->post(route('staff.laporan.evaluasi.store', $laporan->id_laporan), [
            'kategori_kerusakan' => 'Sedang',
            'catatan_pemeriksaan' => 'Catatan setelah edit',
            'file_lampiran_evaluasi' => [$file2],
        ]);

        $laporan->refresh();
        $this->assertCount(2, $laporan->lampiran_evaluasi_list);
        foreach ($laporan->lampiran_evaluasi_list as $savedPath) {
            \Illuminate\Support\Facades\Storage::disk('public')->assertExists($savedPath);
        }
    }

    public function test_staff_can_delete_individual_attachment_item_during_evaluation_edit(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');
        extract($this->setupData());

        $this->actingAs($staff1);

        $file1 = \Illuminate\Http\UploadedFile::fake()->create('dokumen1.pdf', 1024, 'application/pdf');
        $file2 = \Illuminate\Http\UploadedFile::fake()->create('foto2.jpg', 600, 'image/jpeg');

        $this->post(route('staff.laporan.evaluasi.store', $laporan->id_laporan), [
            'kategori_kerusakan' => 'Sedang',
            'catatan_pemeriksaan' => 'Catatan awal 2 lampiran',
            'file_lampiran_evaluasi' => [$file1, $file2],
        ]);

        $laporan->refresh();
        $this->assertCount(2, $laporan->lampiran_evaluasi_list);

        $pathToDelete = $laporan->lampiran_evaluasi_list[0];
        $pathToKeep = $laporan->lampiran_evaluasi_list[1];

        $this->post(route('staff.laporan.evaluasi.store', $laporan->id_laporan), [
            'kategori_kerusakan' => 'Sedang',
            'catatan_pemeriksaan' => 'Hapus lampiran 1 saja',
            'hapus_lampiran_items' => [$pathToDelete],
        ]);

        $laporan->refresh();
        $this->assertCount(1, $laporan->lampiran_evaluasi_list);
        $this->assertEquals($pathToKeep, $laporan->lampiran_evaluasi_list[0]);
        \Illuminate\Support\Facades\Storage::disk('public')->assertMissing($pathToDelete);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($pathToKeep);
    }
}
