<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use Illuminate\Http\Request;

class KabidRabController extends Controller
{
    /**
     * Menampilkan daftar RAB yang diteruskan Staff ke Kabid.
     */
    public function index(Request $request)
    {
        $query = Laporan::with(['lokasi.pasar', 'fasilitas', 'pelapor', 'detailRab'])
            ->whereNotNull('status_verifikasi_rab');

        // Filter berdasarkan status verifikasi RAB
        if ($request->filled('status')) {
            $query->where('status_verifikasi_rab', $request->status);
        }

        // Filter pencarian berdasarkan nama pasar, fasilitas, atau item kerusakan
        $search = trim((string) $request->input('search', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->whereHas('lokasi.pasar', function ($subQuery) use ($search) {
                    $subQuery->where('nama_pasar', 'like', "%{$search}%");
                })->orWhereHas('fasilitas', function ($subQuery) use ($search) {
                    $subQuery->where('nama_fasilitas', 'like', "%{$search}%");
                })->orWhere('item_kerusakan', 'like', "%{$search}%");
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
     * Menampilkan detail RAB dan laporan terkait.
     */
    public function show($id)
    {
        $laporan = Laporan::with(['lokasi.pasar', 'fasilitas', 'pelapor', 'detailRab', 'fotoLaporan'])
            ->whereNotNull('status_verifikasi_rab')
            ->findOrFail($id);

        return view('kabid.rab.show', compact('laporan'));
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
            ->route('kabid.rab.show', $laporan->id_laporan)
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
            ->route('kabid.rab.show', $laporan->id_laporan)
            ->with('success', 'RAB berhasil dikembalikan untuk revisi.');
    }
}
