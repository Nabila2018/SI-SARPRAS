<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->string('id_notifikasi', 10)->primary();
            $table->string('id_user', 10);
            $table->string('id_laporan', 10);
            $table->string('judul_notifikasi', 100);
            $table->text('pesan_notifikasi');
            $table->tinyInteger('is_read')->default(0);
            $table->datetime('created_at');

            $table->foreign('id_user')->references('id_user')->on('user')->onDelete('cascade');
            $table->foreign('id_laporan')->references('id_laporan')->on('laporan')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};