<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('proyek', function (Blueprint $table) {
            $table->string('id_proyek', 10)->primary();
            $table->string('nama_proyek', 150);
            $table->text('deskripsi_proyek')->nullable();
            $table->string('id_pasar', 10);
            $table->string('id_pembuat', 10);
            $table->timestamps();

            $table->foreign('id_pasar')->references('id_pasar')->on('pasar')->onDelete('cascade');
            $table->foreign('id_pembuat')->references('id_user')->on('user')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proyek');
    }
};
