<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pasar extends Model
{
    use HasFactory;

    protected $table = 'pasar';
    protected $primaryKey = 'id_pasar';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_pasar',
        'nama_pasar',
        'alamat',
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
        $prefix = 'PSR';
        $latest = static::orderBy('id_pasar', 'desc')->first();
        if (!$latest) {
            return $prefix . '001';
        }
        $number = (int) substr($latest->id_pasar, strlen($prefix));
        return $prefix . str_pad($number + 1, 3, '0', STR_PAD_LEFT);
    }

    public function lokasi()
    {
        return $this->hasMany(Lokasi::class, 'id_pasar');
    }

    public function user()
    {
        return $this->hasMany(User::class, 'id_pasar');
    }
}