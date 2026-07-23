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

    public function storeEvaluation(Request $request, $id)
    {
        $laporan = Laporan::findOrFail($id);

        if (auth()->user()->role->nama_role !== 'Staff Sarana dan Prasarana') {
            abort(403, 'Akses ditolak.');
        }

        if ($laporan->status_laporan !== 'Menunggu') {
            return back()->with('error', 'Evaluasi hanya dapat dilakukan saat laporan masih berstatus Menunggu.');
        }

        $data = $request->validate([
            'kategori_kerusakan' => ['required', 'in:Ringan,Sedang,Berat'],
            'catatan_pemeriksaan' => ['nullable', 'string', 'max:2000'],
        ]);

        $laporan->update([
            'kategori_kerusakan' => $data['kategori_kerusakan'],
            'catatan_pemeriksaan' => $data['catatan_pemeriksaan'],
        ]);

        return redirect()
            ->route('laporan.show', $laporan->id_laporan)
            ->with('success', 'Evaluasi berhasil disimpan.');
    }

    public function forwardToKabid($id)
    {
        $laporan = Laporan::findOrFail($id);

        if (auth()->user()->role->nama_role !== 'Staff Sarana dan Prasarana') {
            abort(403, 'Akses ditolak.');
        }

        if ($laporan->status_laporan !== 'Menunggu') {
            return back()->with('error', 'Laporan ini tidak dapat diteruskan karena status sudah berubah.');
        }

        if (empty($laporan->kategori_kerusakan) && empty($laporan->catatan_pemeriksaan)) {
            return back()->with('error', 'Evaluasi harus diisi sebelum melanjutkan ke Kabid.');
        }

        $laporan->update([
            'status_laporan' => 'Diproses',
        ]);

        return redirect()
            ->route('laporan.show', $laporan->id_laporan)
            ->with('success', 'Laporan berhasil diteruskan ke Kabid.');
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