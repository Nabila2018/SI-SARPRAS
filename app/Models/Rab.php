<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rab extends Model
{
    use HasFactory;

    protected $table = 'rab';
    protected $primaryKey = 'id_rab';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_rab',
        'status_verifikasi_rab',
        'catatan_revisi_rab',
        'tanggal_verifikasi_rab',
        'tanggal_persetujuan_awal',
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

        $latest = static::where('id_rab', 'LIKE', $prefix . '%')
            ->orderByRaw('CAST(SUBSTRING(id_rab, ' . (strlen($prefix) + 1) . ') AS UNSIGNED) DESC')
            ->first();

        $number = 0;
        if ($latest) {
            $rawNumber = substr($latest->id_rab, strlen($prefix));
            $number = is_numeric($rawNumber) ? (int)$rawNumber : 0;
        }

        do {
            $number++;
            $candidate = $prefix . str_pad($number, 3, '0', STR_PAD_LEFT);
        } while (static::where('id_rab', $candidate)->exists());

        return $candidate;
    }

    public function laporan()
    {
        return $this->hasMany(Laporan::class, 'id_rab', 'id_rab');
    }

    public function spj()
    {
        return $this->hasOne(Spj::class, 'id_rab', 'id_rab');
    }

    public function detailRab()
    {
        return $this->hasMany(DetailRab::class, 'id_rab', 'id_rab');
    }

    public function getTotalBiayaAttribute(): float
    {
        return (float) $this->detailRab->sum(function ($item) {
            return $item->volume * $item->harga_satuan;
        });
    }

    public function getNamaPasarAttribute(): string
    {
        $firstLaporan = $this->laporan->first();
        return $firstLaporan?->lokasi?->pasar?->nama_pasar ?? '-';
    }
}
