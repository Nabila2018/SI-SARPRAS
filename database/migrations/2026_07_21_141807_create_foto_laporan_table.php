<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('foto_laporan', function (Blueprint $table) {
            $table->string('id_foto', 10)->primary();
            $table->string('id_laporan', 10);
            $table->string('file_foto', 255);

            $table->foreign('id_laporan')->references('id_laporan')->on('laporan')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('foto_laporan');
    }
};