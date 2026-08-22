<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    use HasFactory;

    protected $table = 'laporan';
    protected $primaryKey = 'id_laporan';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_laporan',
        'id_lokasi',
        'id_fasilitas',
        'nama_fasilitas_lainnya',
        'id_pelapor',
        'id_rab',
        'kategori_laporan',
        'kategori_laporan_lainnya',
        'item_kerusakan',
        'lokasi_spesifik',
        'deskripsi_kerusakan',
        'kondisi_diharapkan',
        'tanggal_lapor',
        'status_laporan',
        'alasan_penolakan',
        'kategori_kerusakan',
        'catatan_pemeriksaan',
        'file_lampiran_evaluasi',
        'id_evaluator',
        'catatan_revisi_evaluasi',
        'tanggal_verifikasi_evaluasi',
        'status_verifikasi_rab',
        'catatan_revisi_rab',
        'tanggal_input_rab',
        'tanggal_verifikasi_rab',
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
        $prefix = 'LAP';

        $latest = static::where('id_laporan', 'LIKE', $prefix . '%')
            ->orderByRaw('CAST(SUBSTRING(id_laporan, ' . (strlen($prefix) + 1) . ') AS UNSIGNED) DESC')
            ->first();

        $number = 0;
        if ($latest) {
            $rawNumber = substr($latest->id_laporan, strlen($prefix));
            if (is_numeric($rawNumber)) {
                $number = (int) $rawNumber;
            }
        }

        do {
            $number++;
            $candidate = $prefix . str_pad($number, 3, '0', STR_PAD_LEFT);
        } while (static::where('id_laporan', $candidate)->exists());

        return $candidate;
    }

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'id_lokasi');
    }

    public function fasilitas()
    {
        return $this->belongsTo(Fasilitas::class, 'id_fasilitas');
    }

    public function pelapor()
    {
        return $this->belongsTo(User::class, 'id_pelapor');
    }

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'id_evaluator', 'id_user');
    }

    public function rab()
    {
        return $this->belongsTo(Rab::class, 'id_rab', 'id_rab');
    }

    public function fotoLaporan()
    {
        return $this->hasMany(FotoLaporan::class, 'id_laporan');
    }

    public function detailRab()
    {
        return $this->hasMany(DetailRab::class, 'id_laporan');
    }

    public function progresPerbaikan()
    {
        return $this->hasMany(ProgresPerbaikan::class, 'id_laporan');
    }

    public function getLatestProgressPercentageAttribute(): int
    {
        $latest = $this->progresPerbaikan->sortByDesc('tanggal_update')->first();
        return $latest ? (int)$latest->persentase_penyelesaian : 0;
    }

    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'id_laporan');
    }

    public function getNamaFasilitasDisplayAttribute(): string
    {
        $namaFasilitas = $this->fasilitas->nama_fasilitas ?? '-';
        if (!empty($this->nama_fasilitas_lainnya)) {
            return "{$namaFasilitas} ({$this->nama_fasilitas_lainnya})";
        }
        return $namaFasilitas;
    }

    public function getKategoriLaporanDisplayAttribute(): string
    {
        if ($this->kategori_laporan === 'Lainnya' && !empty($this->kategori_laporan_lainnya)) {
            return "Lainnya ({$this->kategori_laporan_lainnya})";
        }
        return $this->kategori_laporan ?? '-';
    }

    public function getStatusVerifikasiRabAttribute()
    {
        if ($this->rab) {
            return $this->rab->status_verifikasi_rab;
        }
        return $this->attributes['status_verifikasi_rab'] ?? null;
    }

    public function getCatatanRevisiRabAttribute()
    {
        if ($this->rab) {
            return $this->rab->catatan_revisi_rab;
        }
        return $this->attributes['catatan_revisi_rab'] ?? null;
    }

    public function getLampiranEvaluasiListAttribute(): array
    {
        if (empty($this->file_lampiran_evaluasi)) {
            return [];
        }

        $decoded = json_decode($this->file_lampiran_evaluasi, true);
        if (is_array($decoded)) {
            return array_values($decoded);
        }

        return [$this->file_lampiran_evaluasi];
    }
}