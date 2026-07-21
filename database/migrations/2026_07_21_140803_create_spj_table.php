<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spj', function (Blueprint $table) {
            $table->id('id_spj');
            $table->string('nomor_spj', 50);
            $table->string('file_spj', 255);
            $table->datetime('tanggal_dibuat');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spj');
    }
};