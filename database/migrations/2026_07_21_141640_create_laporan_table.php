<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan', function (Blueprint $table) {
            $table->id('id_laporan');
            $table->foreignId('id_lokasi')->constrained('lokasi', 'id_lokasi');
            $table->foreignId('id_fasilitas')->constrained('fasilitas', 'id_fasilitas');
            $table->foreignId('id_pelapor')->constrained('user', 'id_user');
            $table->foreignId('id_spj')->nullable()->constrained('spj', 'id_spj');

            $table->enum('kategori_laporan', ['Sanitasi & Air', 'Instalasi Listrik', 'Prasarana Bangunan', 'Fasilitas Umum']);
            $table->string('item_kerusakan', 100);
            $table->string('lokasi_spesifik', 255)->nullable();
            $table->text('deskripsi_kerusakan');
            $table->text('kondisi_diharapkan');
            $table->datetime('tanggal_lapor');

            $table->enum('status_laporan', ['Menunggu', 'Diproses', 'Selesai', 'Dikembalikan']);

            // Evaluasi
            $table->enum('kategori_kerusakan', ['Ringan', 'Sedang', 'Berat'])->nullable();
            $table->text('catatan_pemeriksaan')->nullable();
            $table->enum('status_verifikasi_evaluasi', ['Menunggu', 'Disetujui', 'Dikembalikan'])->nullable();
            $table->text('catatan_revisi_evaluasi')->nullable();
            $table->datetime('tanggal_verifikasi_evaluasi')->nullable();

            // RAB Header
            $table->enum('status_verifikasi_rab', ['Menunggu', 'Disetujui', 'Dikembalikan'])->nullable();
            $table->text('catatan_revisi_rab')->nullable();
            $table->datetime('tanggal_input_rab')->nullable();
            $table->datetime('tanggal_verifikasi_rab')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan');
    }
};