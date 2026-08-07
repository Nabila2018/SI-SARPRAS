<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgresPerbaikan extends Model
{
    use HasFactory;

    protected $table = 'progres_perbaikan';
    protected $primaryKey = 'id_progres';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_progres',
        'id_laporan',
        'persentase_penyelesaian',
        'keterangan_perkembangan',
        'tanggal_update',
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
        $prefix = 'PRG';
        $latest = static::orderBy('id_progres', 'desc')->first();
        if (!$latest) {
            return $prefix . '001';
        }
        $number = (int) substr($latest->id_progres, strlen($prefix));
        return $prefix . str_pad($number + 1, 3, '0', STR_PAD_LEFT);
    }

    public function laporan()
    {
        return $this->belongsTo(Laporan::class, 'id_laporan');
    }

    public function fotoProgres()
    {
        return $this->hasMany(FotoProgres::class, 'id_progres');
    }
}