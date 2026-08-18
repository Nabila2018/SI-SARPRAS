<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laporan', function (Blueprint $table) {
            $table->string('id_evaluator', 10)->nullable()->after('catatan_pemeriksaan');
            $table->foreign('id_evaluator')->references('id_user')->on('user')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('laporan', function (Blueprint $table) {
            $table->dropForeign(['id_evaluator']);
            $table->dropColumn('id_evaluator');
        });
    }
};
