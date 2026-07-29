<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Safely drop username column from the user table.
     */
    public function up(): void
    {
        if (Schema::hasColumn('user', 'username')) {
            Schema::table('user', function (Blueprint $table) {
                $table->dropUnique('user_username_unique');
                $table->dropColumn('username');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('user', 'username')) {
            Schema::table('user', function (Blueprint $table) {
                $table->string('username', 50)->nullable()->unique()->after('id_user');
            });
        }
    }
};
