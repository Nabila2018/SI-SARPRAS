<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lokasi extends Model
{
    use HasFactory;

    protected $table = 'lokasi';
    protected $primaryKey = 'id_lokasi';

    protected $fillable = [
        'id_pasar',
        'id_induk',
        'nama_lokasi',
        'tipe_lokasi',
        'tahun_mulai_dibangun',
        'tahun_selesai_dibangun',
        'luas_tanah',
        'luas_bangunan',
        'keterangan',
    ];

    public function pasar()
    {
        return $this->belongsTo(Pasar::class, 'id_pasar');
    }

    public function induk()
    {
        return $this->belongsTo(Lokasi::class, 'id_induk');
    }

    public function anak()
    {
        return $this->hasMany(Lokasi::class, 'id_induk');
    }

    public function lokasiFasilitas()
    {
        return $this->hasMany(LokasiFasilitas::class, 'id_lokasi');
    }
}