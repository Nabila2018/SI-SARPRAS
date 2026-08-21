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
        Schema::create('rab', function (Blueprint $table) {
            $table->string('id_rab', 10)->primary();
            $table->string('id_proyek', 10)->unique();
            $table->enum('status_verifikasi_rab', ['Draft', 'Menunggu', 'Disetujui', 'Dikembalikan'])->default('Draft');
            $table->text('catatan_revisi_rab')->nullable();
            $table->dateTime('tanggal_verifikasi_rab')->nullable();
            $table->timestamps();

            $table->foreign('id_proyek')->references('id_proyek')->on('proyek')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rab');
    }
};
