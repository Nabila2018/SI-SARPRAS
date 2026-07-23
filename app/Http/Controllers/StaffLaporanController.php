<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use Illuminate\Http\Request;

class StaffLaporanController extends Controller
{
    // Daftar semua laporan masuk (Staff)
    public function index()
    {
        $laporan = Laporan::with(['lokasi.pasar', 'fasilitas', 'pelapor'])
            ->orderBy('tanggal_lapor', 'desc')
            ->paginate(5);

        return view('staff.laporan.index', compact('laporan'));
    }
}