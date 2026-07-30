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
            $table->string('nama_pekerjaan', 255);
            $table->date('periode_mulai');
            $table->date('periode_selesai');
            $table->text('keterangan')->nullable();
            $table->string('file_spj', 255);
            $table->foreignId('uploaded_by')->constrained('user', 'id_user');
            $table->dateTime('tanggal_upload');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spj');
    }
};