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
            $table->string('id_sab', 10)->nullable()->after('id_rab');
            $table->foreign('id_sab')->references('id_sab')->on('sab')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_rab', function (Blueprint $table) {
            $table->dropForeign(['id_sab']);
            $table->dropColumn('id_sab');
        });
    }
};
