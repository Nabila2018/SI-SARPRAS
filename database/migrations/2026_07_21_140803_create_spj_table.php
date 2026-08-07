<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spj', function (Blueprint $table) {
            $table->string('id_spj', 10)->primary();
            $table->string('nama_pekerjaan', 255);
            $table->date('periode_mulai');
            $table->date('periode_selesai');
            $table->text('keterangan')->nullable();
            $table->string('file_spj', 255);
            $table->string('uploaded_by', 10);
            $table->dateTime('tanggal_upload');
            $table->timestamps();

            $table->foreign('uploaded_by')->references('id_user')->on('user');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spj');
    }
};