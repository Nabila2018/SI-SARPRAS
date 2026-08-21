<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sab extends Model
{
    use HasFactory;

    protected $table = 'sab';
    protected $primaryKey = 'id_sab';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_sab',
        'nama_kebutuhan',
        'satuan',
        'harga_standar',
        'status_aktif',
    ];

    public function scopeAktif($query)
    {
        return $query->where('status_aktif', 'Aktif');
    }

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
        $prefix = 'SAB';

        $latest = static::where('id_sab', 'LIKE', $prefix . '%')
            ->orderByRaw('CAST(SUBSTRING(id_sab, ' . (strlen($prefix) + 1) . ') AS UNSIGNED) DESC')
            ->first();

        $number = 0;
        if ($latest) {
            $rawNumber = substr($latest->id_sab, strlen($prefix));
            $number = is_numeric($rawNumber) ? (int)$rawNumber : 0;
        }

        do {
            $number++;
            $candidate = $prefix . str_pad($number, 3, '0', STR_PAD_LEFT);
        } while (static::where('id_sab', $candidate)->exists());

        return $candidate;
    }

    public function detailRab()
    {
        return $this->hasMany(DetailRab::class, 'id_sab');
    }
}
