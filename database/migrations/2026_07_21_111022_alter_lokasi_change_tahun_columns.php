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
        Schema::table('lokasi', function (Blueprint $table) {
            // Hapus kolom lama
            $table->dropColumn('tahun_dibangun');
            $table->dropColumn('tahun_renovasi');

            // Tambah kolom baru
            $table->integer('tahun_mulai_dibangun')->nullable()->after('tipe_lokasi');
            $table->integer('tahun_selesai_dibangun')->nullable()->after('tahun_mulai_dibangun');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lokasi', function (Blueprint $table) {
            // Kembalikan ke semula kalau rollback
            $table->integer('tahun_dibangun')->nullable()->after('tipe_lokasi');
            $table->string('tahun_renovasi', 50)->nullable()->after('tahun_dibangun');

            // Hapus kolom baru
            $table->dropColumn('tahun_mulai_dibangun');
            $table->dropColumn('tahun_selesai_dibangun');
        });
    }
};