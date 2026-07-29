<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add remember_token column to user table safely.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('user', 'remember_token')) {
            Schema::table('user', function (Blueprint $table) {
                $table->rememberToken()->after('status_akun');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('user', 'remember_token')) {
            Schema::table('user', function (Blueprint $table) {
                $table->dropRememberToken();
            });
        }
    }
};
