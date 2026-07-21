<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('foto_progres', function (Blueprint $table) {
            $table->id('id_foto_progres');
            $table->foreignId('id_progres')->constrained('progres_perbaikan', 'id_progres');
            $table->string('file_foto', 255);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('foto_progres');
    }
};