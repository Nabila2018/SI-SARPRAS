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
        'nama_pekerjaan',
        'periode_mulai',
        'periode_selesai',
        'keterangan',
        'file_spj',
        'uploaded_by',
        'tanggal_upload',
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by', 'id_user');
    }

    public function laporan()
    {
        return $this->hasMany(Laporan::class, 'id_spj', 'id_spj');
    }
}