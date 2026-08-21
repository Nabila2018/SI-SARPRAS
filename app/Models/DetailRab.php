<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailRab extends Model
{
    use HasFactory;

    protected $table = 'detail_rab';
    protected $primaryKey = 'id_detail_rab';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;  


    protected $fillable = [
        'id_detail_rab',
        'id_rab',
        'id_sab',
        'id_laporan',
        'rincian_kebutuhan',
        'volume',
        'satuan',
        'harga_satuan',
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
        $prefix = 'RAB';
        $latest = static::orderBy('id_detail_rab', 'desc')->first();
        if (!$latest) {
            return $prefix . '001';
        }
        $number = (int) substr($latest->id_detail_rab, strlen($prefix));
        return $prefix . str_pad($number + 1, 3, '0', STR_PAD_LEFT);
    }

    public function laporan()
    {
        return $this->belongsTo(Laporan::class, 'id_laporan');
    }

    public function rab()
    {
        return $this->belongsTo(Rab::class, 'id_rab');
    }

    public function sab()
    {
        return $this->belongsTo(Sab::class, 'id_sab');
    }
}