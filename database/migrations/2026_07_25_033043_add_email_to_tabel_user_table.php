<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambahkan email sebagai nullable dulu agar data lama aman
        Schema::table('user', function (Blueprint $table) {
            $table->string('email', 100)
                ->nullable()
                ->unique()
                ->after('username');
        });

        // Isi email untuk akun yang sudah ada
        DB::table('user')->where('username', 'admin')
            ->update(['email' => 'staff@sisarpras.test']);

        DB::table('user')->where('username', 'uptd_pasar_raya')
            ->update(['email' => 'uptd.raya@sisarpras.test']);

        DB::table('user')->where('username', 'uptd_alai')
            ->update(['email' => 'uptd.alai@sisarpras.test']);

        DB::table('user')->where('username', 'kabid')
            ->update(['email' => 'kabid@sisarpras.test']);

        DB::table('user')->where('username', 'kadin')
            ->update(['email' => 'kadin@sisarpras.test']);
    }

    public function down(): void
    {
        Schema::table('user', function (Blueprint $table) {
            $table->dropColumn('email');
        });
    }
};