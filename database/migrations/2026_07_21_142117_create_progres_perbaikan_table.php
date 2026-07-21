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
            $table->enum('persentase_penyelesaian', ['0', '50', '100']);
            $table->text('keterangan_perkembangan');
            $table->datetime('tanggal_update');
            
            $table->unique(['id_laporan', 'persentase_penyelesaian']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('progres_perbaikan');
    }
};