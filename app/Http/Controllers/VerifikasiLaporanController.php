<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use Illuminate\Http\Request;

class VerifikasiLaporanController extends Controller
{
    /**
     * Menampilkan daftar laporan yang menunggu verifikasi evaluasi dari Kabid.
     */
    public function index(Request $request)
    {
        $query = Laporan::with([
            'lokasi.pasar',
            'fasilitas',
            'pelapor'
        ]);

        // Filter berdasarkan status jika ada, default hanya yang 'Diproses' (Menunggu Verifikasi Evaluasi)
        if ($request->filled('status')) {
            $query->where('status_laporan', $request->status);
        } else {
            $query->where('status_laporan', 'Diproses');
        }

        // Search query
        $search = trim((string) $request->input('search', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->whereHas('lokasi.pasar', function ($subQuery) use ($search) {
                    $subQuery->where('nama_pasar', 'like', "%{$search}%");
                })->orWhereHas('fasilitas', function ($subQuery) use ($search) {
                    $subQuery->where('nama_fasilitas', 'like', "%{$search}%");
                })->orWhere('id_laporan', 'like', "%{$search}%");
            });
        }

        $laporans = $query->orderBy('tanggal_lapor', 'desc')
            ->paginate(10)
            ->appends($request->only(['search', 'status']));

        return view('kabid.laporan.index', compact('laporans'));
    }

    /**
     * Redirect langsung ke Workspace Detail Laporan pada Tab Evaluasi.
     */
    public function show($id)
    {
        return redirect()->route('laporan.show', ['id' => $id, 'tab' => 'evaluasi']);
    }

    /**
     * Kepala Bidang menyetujui evaluasi laporan.
     */
    public function setujui($id)
    {
        $laporan = Laporan::whereIn('status_laporan', ['Diproses', 'Menunggu'])->findOrFail($id);

        $laporan->update([
            'status_laporan' => 'Disetujui',
            'tanggal_verifikasi_evaluasi' => now(),
        ]);

        return redirect()
            ->route('kabid.laporan.index')
            ->with('success', 'Hasil evaluasi laporan berhasil disetujui.');
    }

    /**
     * Kepala Bidang mengembalikan evaluasi laporan untuk revisi.
     */
    public function kembalikan($id)
    {
        $laporan = Laporan::whereIn('status_laporan', ['Diproses', 'Menunggu'])->findOrFail($id);

        $validated = request()->validate([
            'catatan_revisi_evaluasi' => 'required|string|max:1000',
        ], [
            'catatan_revisi_evaluasi.required' => 'Catatan revisi evaluasi wajib diisi.',
            'catatan_revisi_evaluasi.max' => 'Catatan revisi maksimal 1000 karakter.',
        ]);

        $laporan->update([
            'status_laporan' => 'Dikembalikan',
            'catatan_revisi_evaluasi' => $validated['catatan_revisi_evaluasi'],
            'tanggal_verifikasi_evaluasi' => now(),
        ]);

        return redirect()
            ->route('kabid.laporan.index')
            ->with('success', 'Hasil evaluasi laporan berhasil dikembalikan untuk revisi.');
    }
}