<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sab', function (Blueprint $table) {
            $table->string('id_sab', 10)->primary();
            $table->string('nama_kebutuhan', 150);
            $table->string('satuan', 30);
            $table->decimal('harga_standar', 15, 2);
            $table->timestamps();
        });

        // Seed data awal SAB untuk pengujian dan penggunaan
        DB::table('sab')->insert([
            [
                'id_sab' => 'SAB001',
                'nama_kebutuhan' => 'Semen Portland 50kg',
                'satuan' => 'Sak',
                'harga_standar' => 75000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_sab' => 'SAB002',
                'nama_kebutuhan' => 'Pasir Beton / Pasung (m3)',
                'satuan' => 'M3',
                'harga_standar' => 280000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_sab' => 'SAB003',
                'nama_kebutuhan' => 'Batu Bata Merah',
                'satuan' => 'Buah',
                'harga_standar' => 1200.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_sab' => 'SAB004',
                'nama_kebutuhan' => 'Keramik Granit 60x60',
                'satuan' => 'Dus',
                'harga_standar' => 185000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_sab' => 'SAB005',
                'nama_kebutuhan' => 'Cat Tembok Tahan Air 20kg',
                'satuan' => 'Pail',
                'harga_standar' => 650000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_sab' => 'SAB006',
                'nama_kebutuhan' => 'Pipa PVC AW 3 inci',
                'satuan' => 'Batang',
                'harga_standar' => 95000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_sab' => 'SAB007',
                'nama_kebutuhan' => 'Seng Gelombang 0.3mm',
                'satuan' => 'Lembar',
                'harga_standar' => 70000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sab');
    }
};
