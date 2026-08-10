<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use Illuminate\Http\Request;

class KabidRabController extends Controller
{
    /**
     * Menampilkan daftar RAB yang menunggu verifikasi dari Kabid.
     */
    public function index(Request $request)
    {
        $query = Laporan::with(['lokasi.pasar', 'fasilitas', 'pelapor', 'detailRab'])
            ->whereNotNull('status_verifikasi_rab');

        // Default: prioritaskan tampilan RAB berstatus 'Menunggu'
        if ($request->filled('status')) {
            $query->where('status_verifikasi_rab', $request->status);
        } else {
            $query->where('status_verifikasi_rab', 'Menunggu');
        }

        // Filter pencarian berdasarkan nama pasar, fasilitas, atau ID laporan
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

        $rabList = $query
            ->orderBy('tanggal_input_rab', 'desc')
            ->paginate(10)
            ->appends($request->only(['search', 'status']));

        $statusList = ['Menunggu', 'Disetujui', 'Dikembalikan'];

        return view('kabid.rab.index', compact('rabList', 'statusList'));
    }

    /**
     * Redirect langsung ke Workspace Detail Laporan pada Tab RAB.
     */
    public function show($id)
    {
        return redirect()->route('laporan.show', ['id' => $id, 'tab' => 'rab']);
    }

    /**
     * Kepala Bidang menyetujui RAB.
     */
    public function setujui($id)
    {
        $laporan = Laporan::whereNotNull('status_verifikasi_rab')->findOrFail($id);

        if ($laporan->status_verifikasi_rab !== 'Menunggu') {
            return back()->with('error', 'Hanya RAB berstatus Menunggu yang dapat disetujui.');
        }

        $laporan->update([
            'status_verifikasi_rab' => 'Disetujui',
            'tanggal_verifikasi_rab' => now(),
        ]);

        return redirect()
            ->route('kabid.rab.index')
            ->with('success', 'RAB berhasil disetujui.');
    }

    /**
     * Kepala Bidang mengembalikan RAB untuk revisi.
     */
    public function kembalikan(Request $request, $id)
    {
        $laporan = Laporan::whereNotNull('status_verifikasi_rab')->findOrFail($id);

        if ($laporan->status_verifikasi_rab !== 'Menunggu') {
            return back()->with('error', 'Hanya RAB berstatus Menunggu yang dapat dikembalikan.');
        }

        $validated = $request->validate([
            'catatan_revisi_rab' => ['required', 'string', 'max:1000'],
        ], [
            'catatan_revisi_rab.required' => 'Catatan revisi RAB wajib diisi.',
            'catatan_revisi_rab.max' => 'Catatan revisi maksimal 1000 karakter.',
        ]);

        $laporan->update([
            'status_verifikasi_rab' => 'Dikembalikan',
            'catatan_revisi_rab' => $validated['catatan_revisi_rab'],
            'tanggal_verifikasi_rab' => now(),
        ]);

        return redirect()
            ->route('kabid.rab.index')
            ->with('success', 'RAB berhasil dikembalikan untuk revisi.');
    }
}
