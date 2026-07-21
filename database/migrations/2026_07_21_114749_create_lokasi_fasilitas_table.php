<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lokasi_fasilitas', function (Blueprint $table) {
            $table->unsignedBigInteger('id_lokasi');
            $table->unsignedBigInteger('id_fasilitas');
            $table->integer('jumlah')->default(0);
            
            $table->primary(['id_lokasi', 'id_fasilitas']);
            
            $table->foreign('id_lokasi')
                  ->references('id_lokasi')
                  ->on('lokasi')
                  ->onDelete('restrict');
                  
            $table->foreign('id_fasilitas')
                  ->references('id_fasilitas')
                  ->on('fasilitas')
                  ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lokasi_fasilitas');
    }
};