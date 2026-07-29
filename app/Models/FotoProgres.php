<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FotoProgres extends Model
{
    use HasFactory;

    protected $table = 'foto_progres';
    protected $primaryKey = 'id_foto_progres';
    public $timestamps = false;

    protected $fillable = [
        'id_progres',
        'file_foto',
    ];

    public function progresPerbaikan()
    {
        return $this->belongsTo(ProgresPerbaikan::class, 'id_progres');
    }
}