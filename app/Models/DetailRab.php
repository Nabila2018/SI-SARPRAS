<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailRab extends Model
{
    use HasFactory;

    protected $table = 'detail_rab';
    protected $primaryKey = 'id_detail_rab';
    public $timestamps = false;  


    protected $fillable = [
        'id_laporan',
        'rincian_kebutuhan',
        'volume',
        'satuan',
        'harga_satuan',
    ];

    public function laporan()
    {
        return $this->belongsTo(Laporan::class, 'id_laporan');
    }
}