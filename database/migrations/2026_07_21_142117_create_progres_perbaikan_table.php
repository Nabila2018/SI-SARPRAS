<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('progres_perbaikan', function (Blueprint $table) {
            $table->string('id_progres', 10)->primary();
            $table->string('id_laporan', 10);
            $table->integer('persentase_penyelesaian');
            $table->text('keterangan_perkembangan');
            $table->dateTime('tanggal_update');

            $table->foreign('id_laporan')->references('id_laporan')->on('laporan')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('progres_perbaikan');
    }
};