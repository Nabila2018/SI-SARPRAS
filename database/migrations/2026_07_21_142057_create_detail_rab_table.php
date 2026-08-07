<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_rab', function (Blueprint $table) {
            $table->string('id_detail_rab', 10)->primary();
            $table->string('id_laporan', 10);
            $table->string('rincian_kebutuhan', 150);
            $table->decimal('volume', 10, 3);
            $table->string('satuan', 30);
            $table->decimal('harga_satuan', 15, 2);

            $table->foreign('id_laporan')->references('id_laporan')->on('laporan')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_rab');
    }
};