<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Spj extends Model
{
    use HasFactory;

    protected $table = 'spj';
    protected $primaryKey = 'id_spj';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_spj',
        'nama_pekerjaan',
        'periode_mulai',
        'periode_selesai',
        'keterangan',
        'file_spj',
        'uploaded_by',
        'tanggal_upload',
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
        $prefix = 'SPJ';
        $latest = static::orderBy('id_spj', 'desc')->first();
        if (!$latest) {
            return $prefix . '001';
        }
        $number = (int) substr($latest->id_spj, strlen($prefix));
        return $prefix . str_pad($number + 1, 3, '0', STR_PAD_LEFT);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by', 'id_user');
    }

    public function laporan()
    {
        return $this->hasMany(Laporan::class, 'id_spj', 'id_spj');
    }
}