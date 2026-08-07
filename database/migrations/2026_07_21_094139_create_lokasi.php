<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lokasi', function (Blueprint $table) {
            $table->string('id_lokasi', 10)->primary();
            $table->string('id_pasar', 10);
            $table->string('id_induk', 10)->nullable();
            $table->string('nama_lokasi', 100);
            $table->string('tipe_lokasi', 30);
            $table->integer('tahun_dibangun')->nullable();
            $table->string('tahun_renovasi', 50)->nullable();
            $table->decimal('luas_tanah', 10, 2)->nullable();
            $table->decimal('luas_bangunan', 10, 2)->nullable();
            $table->string('keterangan', 255)->nullable();
            $table->timestamps();

            $table->foreign('id_pasar')
                  ->references('id_pasar')
                  ->on('pasar')
                  ->onDelete('cascade');

            $table->foreign('id_induk')
                  ->references('id_lokasi')
                  ->on('lokasi')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lokasi');
    }
};