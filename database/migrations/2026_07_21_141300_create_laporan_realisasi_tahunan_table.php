<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_realisasi_tahunan', function (Blueprint $table) {
            $table->id('id_realisasi');
            $table->year('tahun_anggaran')->unique();
            $table->text('keterangan')->nullable();
            $table->string('file_realisasi', 255);
            $table->string('uploaded_by', 10);
            $table->dateTime('tanggal_upload');
            $table->timestamps();

            $table->foreign('uploaded_by')->references('id_user')->on('user');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_realisasi_tahunan');
    }
};