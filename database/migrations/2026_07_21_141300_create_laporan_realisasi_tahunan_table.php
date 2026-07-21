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
            $table->integer('tahun')->unique();
            $table->string('file_realisasi', 255);
            $table->datetime('tanggal_upload');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_realisasi_tahunan');
    }
};