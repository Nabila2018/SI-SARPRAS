<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    use HasFactory;

    protected $table = 'laporan';
    protected $primaryKey = 'id_laporan';
    public $timestamps = false;

    protected $fillable = [
        'id_lokasi',
        'id_fasilitas',
        'id_pelapor',
        'id_spj',
        'kategori_laporan',
        'item_kerusakan',
        'lokasi_spesifik',
        'deskripsi_kerusakan',
        'kondisi_diharapkan',
        'tanggal_lapor',
        'status_laporan',
        'kategori_kerusakan',
        'catatan_pemeriksaan',
        'catatan_revisi_evaluasi',
        'tanggal_verifikasi_evaluasi',
        'status_verifikasi_rab',
        'catatan_revisi_rab',
        'tanggal_input_rab',
        'tanggal_verifikasi_rab',
    ];

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

    public function spj()
    {
        return $this->belongsTo(Spj::class, 'id_spj');
    }

    public function fotoLaporan()
    {
        return $this->hasMany(FotoLaporan::class, 'id_laporan');
    }

    public function detailRab()
    {
        return $this->hasMany(DetailRab::class, 'id_laporan');
    }

    public function buktiPembelian()
    {
        return $this->hasMany(BuktiPembelian::class, 'id_laporan');
    }

    public function progresPerbaikan()
    {
        return $this->hasMany(ProgresPerbaikan::class, 'id_laporan');
    }

    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'id_laporan');
    }
}