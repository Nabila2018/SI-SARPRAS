<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bukti_pembelian', function (Blueprint $table) {
            $table->id('id_bukti');
            $table->foreignId('id_laporan')->constrained('laporan', 'id_laporan');
            $table->string('file_bukti', 255);
            $table->decimal('nominal', 15, 2);
            $table->datetime('tanggal_bukti');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bukti_pembelian');
    }
};