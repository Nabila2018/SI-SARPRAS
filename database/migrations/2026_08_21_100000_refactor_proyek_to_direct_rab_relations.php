<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tambahkan id_rab ke tabel laporan
        if (!Schema::hasColumn('laporan', 'id_rab')) {
            Schema::table('laporan', function (Blueprint $table) {
                $table->string('id_rab', 10)->nullable()->after('id_evaluator');
                $table->foreign('id_rab')->references('id_rab')->on('rab')->onDelete('set null');
            });
        }

        // 2. Tambahkan id_rab & nama_pekerjaan ke tabel spj
        if (!Schema::hasColumn('spj', 'id_rab')) {
            Schema::table('spj', function (Blueprint $table) {
                $table->string('id_rab', 10)->nullable()->unique()->after('id_spj');
                $table->foreign('id_rab')->references('id_rab')->on('rab')->onDelete('set null');
            });
        }

        if (!Schema::hasColumn('spj', 'nama_pekerjaan')) {
            Schema::table('spj', function (Blueprint $table) {
                $table->string('nama_pekerjaan', 255)->nullable()->after('id_rab');
            });
        }

        // 3. Migrasikan data relasi existing dari id_proyek ke id_rab & nama_pekerjaan
        if (Schema::hasTable('proyek')) {
            if (Schema::hasColumn('laporan', 'id_proyek') && Schema::hasColumn('rab', 'id_proyek')) {
                DB::statement("
                    UPDATE laporan
                    SET id_rab = (SELECT id_rab FROM rab WHERE rab.id_proyek = laporan.id_proyek)
                    WHERE id_proyek IS NOT NULL AND EXISTS (SELECT 1 FROM rab WHERE rab.id_proyek = laporan.id_proyek)
                ");
            }

            if (Schema::hasColumn('spj', 'id_proyek') && Schema::hasColumn('rab', 'id_proyek')) {
                DB::statement("
                    UPDATE spj
                    SET id_rab = (SELECT id_rab FROM rab WHERE rab.id_proyek = spj.id_proyek)
                    WHERE id_proyek IS NOT NULL AND EXISTS (SELECT 1 FROM rab WHERE rab.id_proyek = spj.id_proyek)
                ");

                DB::statement("
                    UPDATE spj
                    SET nama_pekerjaan = (SELECT nama_proyek FROM proyek WHERE proyek.id_proyek = spj.id_proyek)
                    WHERE id_proyek IS NOT NULL AND EXISTS (SELECT 1 FROM proyek WHERE proyek.id_proyek = spj.id_proyek)
                ");
            }
        }

        // 4. Drop FK & kolom id_proyek pada tabel laporan, spj, rab
        if (Schema::hasColumn('laporan', 'id_proyek')) {
            Schema::table('laporan', function (Blueprint $table) {
                $table->dropForeign(['id_proyek']);
                $table->dropColumn('id_proyek');
            });
        }

        if (Schema::hasColumn('spj', 'id_proyek')) {
            Schema::table('spj', function (Blueprint $table) {
                $table->dropForeign(['id_proyek']);
                $table->dropColumn('id_proyek');
            });
        }

        if (Schema::hasColumn('rab', 'id_proyek')) {
            Schema::table('rab', function (Blueprint $table) {
                $table->dropForeign(['id_proyek']);
                $table->dropColumn('id_proyek');
            });
        }

        // 5. Drop tabel proyek
        Schema::dropIfExists('proyek');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse migration needed for total refactoring
    }
};
