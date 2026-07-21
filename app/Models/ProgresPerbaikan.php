<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgresPerbaikan extends Model
{
    use HasFactory;

    protected $table = 'progres_perbaikan';
    protected $primaryKey = 'id_progres';

    protected $fillable = [
        'id_laporan',
        'persentase_penyelesaian',
        'keterangan_perkembangan',
        'tanggal_update',
    ];

    public function laporan()
    {
        return $this->belongsTo(Laporan::class, 'id_laporan');
    }

    public function fotoProgres()
    {
        return $this->hasMany(FotoProgres::class, 'id_progres');
    }
}