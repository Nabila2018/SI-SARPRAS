<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanRealisasiTahunan extends Model
{
    use HasFactory;

    protected $table = 'laporan_realisasi_tahunan';
    protected $primaryKey = 'id_realisasi';

    protected $fillable = [
        'tahun',
        'file_realisasi',
        'tanggal_upload',
    ];
}