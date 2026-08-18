<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laporan', function (Blueprint $table) {
            $table->string('nama_fasilitas_lainnya', 100)->nullable()->after('id_fasilitas');
            $table->string('kategori_laporan_lainnya', 100)->nullable()->after('kategori_laporan');
        });

        DB::statement("
            ALTER TABLE laporan
            MODIFY kategori_laporan ENUM(
                'Sanitasi & Air',
                'Instalasi Listrik',
                'Prasarana Bangunan',
                'Fasilitas Umum',
                'Lainnya'
            ) NOT NULL
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE laporan
            MODIFY kategori_laporan ENUM(
                'Sanitasi & Air',
                'Instalasi Listrik',
                'Prasarana Bangunan',
                'Fasilitas Umum'
            ) NOT NULL
        ");

        Schema::table('laporan', function (Blueprint $table) {
            $table->dropColumn(['nama_fasilitas_lainnya', 'kategori_laporan_lainnya']);
        });
    }
};
