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
        Schema::table('sab', function (Blueprint $table) {
            $table->enum('status_aktif', ['Aktif', 'Nonaktif'])->default('Aktif')->after('harga_standar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sab', function (Blueprint $table) {
            $table->dropColumn('status_aktif');
        });
    }
};
