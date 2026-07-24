<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Laporan;

class VerifikasiLaporanController extends Controller
{
    /**
     * Menampilkan daftar laporan yang sudah diteruskan Staff ke Kabid.
     */
    public function index()
    {
        $laporans = Laporan::with([
            'lokasi.pasar',
            'fasilitas',
            'pelapor'
        ])
            ->where('status_laporan', 'Diproses')
            ->orderBy('tanggal_lapor', 'desc')
            ->get();

        return view('kabid.laporan.index', compact('laporans'));
    }

    /**
     * Menampilkan detail laporan.
     */
    public function show($id)
    {
        $laporan = Laporan::with([
            'lokasi.pasar',
            'fasilitas',
            'pelapor',
            'fotoLaporan'
        ])
            ->where('status_laporan', 'Diproses')
            ->findOrFail($id);

        return view('kabid.laporan.show', compact('laporan'));
    }

    /**
 * Kepala Bidang menyetujui laporan.
 */
    public function setujui($id)
    {

        $laporan = Laporan::where('status_laporan', 'Diproses')
            ->findOrFail($id);

        $laporan->update([
            'status_laporan' => 'Disetujui',
            'tanggal_verifikasi_evaluasi' => now(),
        ]);

        return redirect()
            ->route('kabid.laporan.index')
            ->with('success', 'Laporan berhasil disetujui.');
    }

    /**
 * Kepala Bidang mengembalikan laporan.
 */
    public function kembalikan($id)
    {
        $laporan = Laporan::where('status_laporan', 'Diproses')
            ->findOrFail($id);

        $validated = request()->validate([
            'catatan_revisi_evaluasi' => 'required|string|max:1000',
        ]);

        $laporan->update([
            'status_laporan' => 'Dikembalikan',
            'catatan_revisi_evaluasi' => $validated['catatan_revisi_evaluasi'],
            'tanggal_verifikasi_evaluasi' => now(),
        ]);

        return redirect()
            ->route('kabid.laporan.index')
            ->with('success', 'Laporan berhasil dikembalikan.');
    }
 }