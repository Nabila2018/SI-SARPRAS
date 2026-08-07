<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FotoLaporan extends Model
{
    use HasFactory;

    protected $table = 'foto_laporan';
    protected $primaryKey = 'id_foto';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    
    protected $fillable = [
        'id_foto',
        'id_laporan',
        'file_foto',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = static::generateId();
            }
        });
    }

    public static function generateId(): string
    {
        $prefix = 'FLT';
        $latest = static::orderBy('id_foto', 'desc')->first();
        if (!$latest) {
            return $prefix . '001';
        }
        $number = (int) substr($latest->id_foto, strlen($prefix));
        return $prefix . str_pad($number + 1, 3, '0', STR_PAD_LEFT);
    }

    public function laporan()
    {
        return $this->belongsTo(Laporan::class, 'id_laporan');
    }
}