<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proyek extends Model
{
    use HasFactory;

    protected $table = 'proyek';
    protected $primaryKey = 'id_proyek';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_proyek',
        'nama_proyek',
        'deskripsi_proyek',
        'id_pasar',
        'id_pembuat',
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
        $prefix = 'PRJ';

        $latest = static::where('id_proyek', 'LIKE', $prefix . '%')
            ->orderByRaw('CAST(SUBSTRING(id_proyek, ' . (strlen($prefix) + 1) . ') AS UNSIGNED) DESC')
            ->first();

        $number = 0;
        if ($latest) {
            $rawNumber = substr($latest->id_proyek, strlen($prefix));
            $number = is_numeric($rawNumber) ? (int)$rawNumber : 0;
        }

        do {
            $number++;
            $candidate = $prefix . str_pad($number, 3, '0', STR_PAD_LEFT);
        } while (static::where('id_proyek', $candidate)->exists());

        return $candidate;
    }

    public function laporan()
    {
        return $this->hasMany(Laporan::class, 'id_proyek');
    }

    public function pasar()
    {
        return $this->belongsTo(Pasar::class, 'id_pasar');
    }

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'id_pembuat', 'id_user');
    }
}
