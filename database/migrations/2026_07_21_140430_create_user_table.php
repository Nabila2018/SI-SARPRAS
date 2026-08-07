<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user', function (Blueprint $table) {
            $table->string('id_user', 10)->primary();
            $table->string('username', 50)->unique();
            $table->string('password', 255);
            $table->string('nama_lengkap', 100);
            $table->string('id_role', 10);
            $table->string('id_pasar', 10)->nullable();
            $table->enum('status_akun', ['Aktif', 'Nonaktif'])->default('Aktif');
            $table->timestamps();

            $table->foreign('id_role')->references('id_role')->on('role');
            $table->foreign('id_pasar')->references('id_pasar')->on('pasar');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user');
    }
};