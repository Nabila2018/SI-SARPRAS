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
        Schema::table('detail_rab', function (Blueprint $table) {
            $table->string('id_rab', 10)->nullable()->after('id_detail_rab');
            $table->foreign('id_rab')->references('id_rab')->on('rab')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_rab', function (Blueprint $table) {
            $table->dropForeign(['id_rab']);
            $table->dropColumn('id_rab');
        });
    }
};
