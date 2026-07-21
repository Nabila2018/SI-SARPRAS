<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id('id_notifikasi');
            $table->foreignId('id_user')->constrained('user', 'id_user');
            $table->foreignId('id_laporan')->constrained('laporan', 'id_laporan');
            $table->string('judul_notifikasi', 100);
            $table->text('pesan_notifikasi');
            $table->tinyInteger('is_read')->default(0);
            $table->datetime('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};