<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuktiPembelian extends Model
{
    use HasFactory;

    protected $table = 'bukti_pembelian';
    protected $primaryKey = 'id_bukti';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_bukti',
        'id_laporan',
        'file_bukti',
        'nominal',
        'tanggal_bukti',
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
        $prefix = 'BKT';
        $latest = static::orderBy('id_bukti', 'desc')->first();
        if (!$latest) {
            return $prefix . '001';
        }
        $number = (int) substr($latest->id_bukti, strlen($prefix));
        return $prefix . str_pad($number + 1, 3, '0', STR_PAD_LEFT);
    }

    public function laporan()
    {
        return $this->belongsTo(Laporan::class, 'id_laporan');
    }
}