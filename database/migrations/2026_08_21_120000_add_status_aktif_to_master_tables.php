<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('pasar', 'status_aktif')) {
            Schema::table('pasar', function (Blueprint $table) {
                $table->enum('status_aktif', ['Aktif', 'Nonaktif'])->default('Aktif')->after('alamat');
            });
        }

        if (!Schema::hasColumn('lokasi', 'status_aktif')) {
            Schema::table('lokasi', function (Blueprint $table) {
                $table->enum('status_aktif', ['Aktif', 'Nonaktif'])->default('Aktif')->after('keterangan');
            });
        }

        if (!Schema::hasColumn('fasilitas', 'status_aktif')) {
            Schema::table('fasilitas', function (Blueprint $table) {
                $table->enum('status_aktif', ['Aktif', 'Nonaktif'])->default('Aktif')->after('nama_fasilitas');
            });
        }

        if (!Schema::hasTable('kategori_laporan')) {
            Schema::create('kategori_laporan', function (Blueprint $table) {
                $table->string('id_kategori')->primary();
                $table->string('nama_kategori');
                $table->enum('status_aktif', ['Aktif', 'Nonaktif'])->default('Aktif');
                $table->timestamps();
            });

            $defaults = [
                ['id_kategori' => 'KAT001', 'nama_kategori' => 'Sanitasi & Air', 'status_aktif' => 'Aktif', 'created_at' => now(), 'updated_at' => now()],
                ['id_kategori' => 'KAT002', 'nama_kategori' => 'Instalasi Listrik', 'status_aktif' => 'Aktif', 'created_at' => now(), 'updated_at' => now()],
                ['id_kategori' => 'KAT003', 'nama_kategori' => 'Prasarana Bangunan', 'status_aktif' => 'Aktif', 'created_at' => now(), 'updated_at' => now()],
                ['id_kategori' => 'KAT004', 'nama_kategori' => 'Fasilitas Umum', 'status_aktif' => 'Aktif', 'created_at' => now(), 'updated_at' => now()],
                ['id_kategori' => 'KAT005', 'nama_kategori' => 'Lainnya', 'status_aktif' => 'Aktif', 'created_at' => now(), 'updated_at' => now()],
            ];

            DB::table('kategori_laporan')->insert($defaults);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('pasar', 'status_aktif')) {
            Schema::table('pasar', function (Blueprint $table) {
                $table->dropColumn('status_aktif');
            });
        }

        if (Schema::hasColumn('lokasi', 'status_aktif')) {
            Schema::table('lokasi', function (Blueprint $table) {
                $table->dropColumn('status_aktif');
            });
        }

        if (Schema::hasColumn('fasilitas', 'status_aktif')) {
            Schema::table('fasilitas', function (Blueprint $table) {
                $table->dropColumn('status_aktif');
            });
        }

        Schema::dropIfExists('kategori_laporan');
    }
};
