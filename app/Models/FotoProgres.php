<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FotoProgres extends Model
{
    use HasFactory;

    protected $table = 'foto_progres';
    protected $primaryKey = 'id_foto_progres';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_foto_progres',
        'id_progres',
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
        $prefix = 'FPG';
        $latest = static::orderBy('id_foto_progres', 'desc')->first();
        if (!$latest) {
            return $prefix . '001';
        }
        $number = (int) substr($latest->id_foto_progres, strlen($prefix));
        return $prefix . str_pad($number + 1, 3, '0', STR_PAD_LEFT);
    }

    public function progresPerbaikan()
    {
        return $this->belongsTo(ProgresPerbaikan::class, 'id_progres');
    }
}