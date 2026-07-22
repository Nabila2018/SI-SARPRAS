<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FotoLaporan extends Model
{
    use HasFactory;

    protected $table = 'foto_laporan';
    protected $primaryKey = 'id_foto';
    public $timestamps = false;
    
    protected $fillable = [
        'id_laporan',
        'file_foto',
    ];

    public function laporan()
    {
        return $this->belongsTo(Laporan::class, 'id_laporan');
    }
}