<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Restore the email column to the user table safely.
     * Compatible with fresh database installs AND partially executed states.
     */
    public function up(): void
    {
        // ----------------------------------------------------------------
        // Step 1: Create email column as nullable if it doesn't exist yet.
        // ----------------------------------------------------------------
        if (!Schema::hasColumn('user', 'email')) {
            Schema::table('user', function (Blueprint $table) {
                $table->string('email', 100)
                    ->nullable()
                    ->after('username');
            });
        }

        // ----------------------------------------------------------------
        // Step 2: Back-fill emails for seeded users if missing/empty.
        // ----------------------------------------------------------------
        $backfill = [
            'admin'           => 'staff@sisarpras.test',
            'uptd_pasar_raya' => 'uptd.pasar.raya@sisarpras.test',
            'uptd_alai'       => 'uptd.alai@sisarpras.test',
            'kabid'           => 'kabid@sisarpras.test',
            'kadin'           => 'kadin@sisarpras.test',
        ];

        foreach ($backfill as $username => $email) {
            DB::table('user')
                ->where('username', $username)
                ->where(function ($q) {
                    $q->whereNull('email')->orWhere('email', '');
                })
                ->update(['email' => $email]);
        }

        // Fallback for any extra unseeded users without email
        $unfilled = DB::table('user')
            ->where(function ($q) {
                $q->whereNull('email')->orWhere('email', '');
            })
            ->select('id_user')
            ->get();

        foreach ($unfilled as $row) {
            DB::table('user')
                ->where('id_user', $row->id_user)
                ->update([
                    'email' => 'user.' . $row->id_user . '@sisarpras.test',
                ]);
        }

        // ----------------------------------------------------------------
        // Step 3: Add UNIQUE index on email if it doesn't already exist.
        // ----------------------------------------------------------------
        $hasUniqueIndex = collect(DB::select("SHOW INDEX FROM `user` WHERE Column_name = 'email' AND Non_unique = 0"))->isNotEmpty();

        if (!$hasUniqueIndex) {
            Schema::table('user', function (Blueprint $table) {
                $table->unique('email', 'user_email_unique');
            });
        }

        // ----------------------------------------------------------------
        // Step 4: Enforce NOT NULL on email column.
        // ----------------------------------------------------------------
        Schema::table('user', function (Blueprint $table) {
            $table->string('email', 100)->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('user', function (Blueprint $table) {
            $table->dropUnique('user_email_unique');
            $table->dropColumn('email');
        });
    }
};
