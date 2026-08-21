<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('notifikasi')) {
            if (!Schema::hasColumn('notifikasi', 'link_target')) {
                Schema::table('notifikasi', function (Blueprint $table) {
                    $table->string('link_target', 255)->nullable()->after('pesan_notifikasi');
                });
            }

            try {
                DB::statement("ALTER TABLE `notifikasi` MODIFY `id_laporan` VARCHAR(10) NULL");
            } catch (\Throwable $e) {
                // Abaikan jika database penyokong tidak memerlukan pembaruan ini
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('notifikasi') && Schema::hasColumn('notifikasi', 'link_target')) {
            Schema::table('notifikasi', function (Blueprint $table) {
                $table->dropColumn('link_target');
            });
        }
    }
};
