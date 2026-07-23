<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambahkan status "Disetujui"
        DB::statement("
            ALTER TABLE laporan
            MODIFY status_laporan ENUM(
                'Menunggu',
                'Diproses',
                'Disetujui',
                'Dikembalikan',
                'Ditolak',
                'Selesai'
            ) NOT NULL
        ");

        // 2. Tambahkan alasan penolakan
        Schema::table('laporan', function (Blueprint $table) {
            $table->text('alasan_penolakan')
                ->nullable()
                ->after('status_laporan');

            // 3. Hapus status verifikasi evaluasi
            $table->dropColumn('status_verifikasi_evaluasi');
        });
    }

    public function down(): void
    {
        // Kembalikan kolom status verifikasi evaluasi
        Schema::table('laporan', function (Blueprint $table) {
            $table->enum('status_verifikasi_evaluasi', [
                'Menunggu',
                'Disetujui',
                'Dikembalikan'
            ])->nullable();

            $table->dropColumn('alasan_penolakan');
        });

        // Kembalikan enum status_laporan seperti sebelumnya
        DB::statement("
            ALTER TABLE laporan
            MODIFY status_laporan ENUM(
                'Menunggu',
                'Diproses',
                'Selesai',
                'Dikembalikan',
                'Ditolak'
            ) NOT NULL
        ");
    }
};