<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lokasi extends Model
{
    use HasFactory;

    protected $table = 'lokasi';
    protected $primaryKey = 'id_lokasi';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_lokasi',
        'id_pasar',
        'id_induk',
        'nama_lokasi',
        'tipe_lokasi',
        'tahun_mulai_dibangun',
        'tahun_selesai_dibangun',
        'luas_tanah',
        'luas_bangunan',
        'keterangan',
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
        $prefix = 'LOC';
        $latest = static::orderBy('id_lokasi', 'desc')->first();
        if (!$latest) {
            return $prefix . '001';
        }
        $number = (int) substr($latest->id_lokasi, strlen($prefix));
        return $prefix . str_pad($number + 1, 3, '0', STR_PAD_LEFT);
    }

    public function pasar()
    {
        return $this->belongsTo(Pasar::class, 'id_pasar');
    }

    public function induk()
    {
        return $this->belongsTo(Lokasi::class, 'id_induk');
    }

    public function anak()
    {
        return $this->hasMany(Lokasi::class, 'id_induk');
    }

    public function lokasiFasilitas()
    {
        return $this->hasMany(LokasiFasilitas::class, 'id_lokasi');
    }
}