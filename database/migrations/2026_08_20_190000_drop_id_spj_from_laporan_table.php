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
            $table->dropForeign(['id_spj']);
            $table->dropColumn('id_spj');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan', function (Blueprint $table) {
            $table->string('id_spj', 10)->nullable()->after('id_pelapor');
            $table->foreign('id_spj')->references('id_spj')->on('spj');
        });
    }
};
