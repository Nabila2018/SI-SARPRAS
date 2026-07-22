<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE laporan
            MODIFY status_laporan ENUM(
                'Menunggu',
                'Diproses',
                'Dikembalikan',
                'Ditolak',
                'Selesai'
            ) NOT NULL
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE laporan
            MODIFY status_laporan ENUM(
                'Menunggu',
                'Diproses',
                'Selesai',
                'Dikembalikan'
            ) NOT NULL
        ");
    }
};