<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('foto_laporan', function (Blueprint $table) {
            $table->id('id_foto');
            $table->foreignId('id_laporan')->constrained('laporan', 'id_laporan');
            $table->string('file_foto', 255);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('foto_laporan');
    }
};