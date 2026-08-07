<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan', function (Blueprint $table) {
            $table->string('id_laporan', 10)->primary();
            $table->string('id_lokasi', 10);
            $table->string('id_fasilitas', 10);
            $table->string('id_pelapor', 10);
            $table->string('id_spj', 10)->nullable();

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

            $table->foreign('id_lokasi')->references('id_lokasi')->on('lokasi');
            $table->foreign('id_fasilitas')->references('id_fasilitas')->on('fasilitas');
            $table->foreign('id_pelapor')->references('id_user')->on('user');
            $table->foreign('id_spj')->references('id_spj')->on('spj');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan');
    }
};