<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use App\Models\Pasar;
use Illuminate\Http\Request;

class StaffLaporanController extends Controller
{
    // Daftar semua laporan masuk (Staff)
    public function index(Request $request)
    {
        $query = $this->applyFilters(Laporan::query(), $request);

        $laporan = $query
            ->with(['lokasi.pasar', 'fasilitas', 'pelapor'])
            ->orderBy('tanggal_lapor', 'desc')
            ->paginate(5)
            ->appends($request->only(['search', 'pasar', 'status']));

        $pasarList = Pasar::orderBy('nama_pasar')->get();
        $statusList = ['Menunggu', 'Diproses', 'Selesai', 'Dikembalikan', 'Ditolak'];

        return view('staff.laporan.index', compact('laporan', 'pasarList', 'statusList'));
    }

    protected function applyFilters($query, Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $pasarId = $request->input('pasar');
        $status = $request->input('status');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->whereHas('lokasi.pasar', function ($subQuery) use ($search) {
                    $subQuery->where('nama_pasar', 'like', "%{$search}%");
                })->orWhereHas('fasilitas', function ($subQuery) use ($search) {
                    $subQuery->where('nama_fasilitas', 'like', "%{$search}%");
                });
            });
        }

        if ($pasarId) {
            $query->whereHas('lokasi.pasar', function ($subQuery) use ($pasarId) {
                $subQuery->where('id_pasar', $pasarId);
            });
        }

        if ($status) {
            $query->where('status_laporan', $status);
        }

        return $query;
    }
}