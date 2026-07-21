<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuktiPembelian extends Model
{
    use HasFactory;

    protected $table = 'bukti_pembelian';
    protected $primaryKey = 'id_bukti';

    protected $fillable = [
        'id_laporan',
        'file_bukti',
        'nominal',
        'tanggal_bukti',
    ];

    public function laporan()
    {
        return $this->belongsTo(Laporan::class, 'id_laporan');
    }
}