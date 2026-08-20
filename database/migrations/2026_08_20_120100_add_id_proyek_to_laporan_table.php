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
        Schema::table('laporan', function (Blueprint $table) {
            $table->string('id_proyek', 10)->nullable()->after('id_spj');
            $table->foreign('id_proyek')->references('id_proyek')->on('proyek');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan', function (Blueprint $table) {
            $table->dropForeign(['id_proyek']);
            $table->dropColumn('id_proyek');
        });
    }
};
