<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('progres_perbaikan', function (Blueprint $table) {
            $table->id('id_progres');
            $table->foreignId('id_laporan')->constrained('laporan', 'id_laporan');
            $table->integer('persentase_penyelesaian');
            $table->text('keterangan_perkembangan');
            $table->dateTime('tanggal_update');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('progres_perbaikan');
    }
};