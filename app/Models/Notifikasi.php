<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    use HasFactory;

    protected $table = 'notifikasi';
    protected $primaryKey = 'id_notifikasi';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_notifikasi',
        'id_user',
        'id_laporan',
        'judul_notifikasi',
        'pesan_notifikasi',
        'is_read',
        'created_at',
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
        $prefix = 'NTF';
        $latest = static::orderBy('id_notifikasi', 'desc')->first();
        if (!$latest) {
            return $prefix . '001';
        }
        $number = (int) substr($latest->id_notifikasi, strlen($prefix));
        return $prefix . str_pad($number + 1, 3, '0', STR_PAD_LEFT);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function laporan()
    {
        return $this->belongsTo(Laporan::class, 'id_laporan');
    }
}