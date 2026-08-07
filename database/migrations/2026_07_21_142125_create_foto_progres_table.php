<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('foto_progres', function (Blueprint $table) {
            $table->string('id_foto_progres', 10)->primary();
            $table->string('id_progres', 10);
            $table->string('file_foto', 255);

            $table->foreign('id_progres')->references('id_progres')->on('progres_perbaikan')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('foto_progres');
    }
};