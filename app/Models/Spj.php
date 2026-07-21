<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Spj extends Model
{
    use HasFactory;

    protected $table = 'spj';
    protected $primaryKey = 'id_spj';

    protected $fillable = [
        'nomor_spj',
        'file_spj',
        'tanggal_dibuat',
    ];

    public function laporan()
    {
        return $this->hasMany(Laporan::class, 'id_spj');
    }
}