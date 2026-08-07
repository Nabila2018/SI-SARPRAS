<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bukti_pembelian', function (Blueprint $table) {
            $table->string('id_bukti', 10)->primary();
            $table->string('id_laporan', 10);
            $table->string('file_bukti', 255);
            $table->decimal('nominal', 15, 2);
            $table->datetime('tanggal_bukti');

            $table->foreign('id_laporan')->references('id_laporan')->on('laporan')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bukti_pembelian');
    }
};