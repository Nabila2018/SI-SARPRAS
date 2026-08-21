<?php

namespace App\Http\Controllers;

use App\Models\Rab;
use Illuminate\Http\Request;

class KabidRabController extends Controller
{
    protected function authorizeKabid()
    {
        if (auth()->user()->role->nama_role !== 'Kepala Bidang') {
            abort(403, 'Akses ditolak. Hanya Kepala Bidang yang berhak melakukan verifikasi RAB.');
        }
    }

    /**
     * Menampilkan daftar RAB yang perlu diverifikasi Kabid.
     */
    public function index(Request $request)
    {
        $this->authorizeKabid();

        $query = Rab::with(['laporan.lokasi.pasar', 'detailRab']);

        if ($request->filled('status')) {
            $query->where('status_verifikasi_rab', $request->status);
        } else {
            $query->whereIn('status_verifikasi_rab', ['Menunggu', 'Disetujui', 'Dikembalikan']);
        }

        $search = trim((string) $request->input('search', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('id_rab', 'like', "%{$search}%")
                  ->orWhereHas('laporan.lokasi.pasar', function ($subQuery) use ($search) {
                      $subQuery->where('nama_pasar', 'like', "%{$search}%");
                  })
                  ->orWhereHas('detailRab', function ($subQuery) use ($search) {
                      $subQuery->where('rincian_kebutuhan', 'like', "%{$search}%");
                  });
            });
        }

        $rabList = $query
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->only(['search', 'status']));

        $statusList = ['Menunggu', 'Disetujui', 'Dikembalikan'];

        return view('kabid.rab.index', compact('rabList', 'statusList'));
    }

    /**
     * Tampilkan detail verifikasi RAB.
     */
    public function show($id)
    {
        $this->authorizeKabid();

        $rab = Rab::with(['laporan.lokasi.pasar', 'laporan.fasilitas', 'detailRab.sab'])
            ->where('id_rab', $id)
            ->firstOrFail();

        return view('kabid.rab.show', compact('rab'));
    }

    /**
     * Kepala Bidang menyetujui RAB.
     */
    public function setujui($id)
    {
        $this->authorizeKabid();

        $rab = Rab::where('id_rab', $id)->firstOrFail();

        if ($rab->status_verifikasi_rab !== 'Menunggu') {
            return back()->with('error', 'Hanya RAB berstatus Menunggu yang dapat disetujui.');
        }

        $updateData = [
            'status_verifikasi_rab' => 'Disetujui',
            'tanggal_verifikasi_rab' => now(),
            'catatan_revisi_rab' => null,
        ];

        if (is_null($rab->tanggal_persetujuan_awal)) {
            $updateData['tanggal_persetujuan_awal'] = now();
        }

        $rab->update($updateData);

        // Event 5: Kabid setujui RAB -> Staff
        $firstLap = $rab->laporan()->first()?->id_laporan;
        \App\Services\NotificationService::sendToRole(
            'Staff Sarana dan Prasarana',
            'RAB Disetujui Kabid',
            "Kabid menyetujui RAB {$rab->id_rab}.",
            route('staff.rab.show', $rab->id_rab),
            $firstLap
        );

        return redirect()
            ->route('kabid.rab.index')
            ->with('success', "RAB {$rab->id_rab} berhasil disetujui.");
    }

    /**
     * Kepala Bidang mengembalikan RAB untuk revisi.
     */
    public function kembalikan(Request $request, $id)
    {
        $this->authorizeKabid();

        $rab = Rab::where('id_rab', $id)->firstOrFail();

        if ($rab->status_verifikasi_rab !== 'Menunggu') {
            return back()->with('error', 'Hanya RAB berstatus Menunggu yang dapat dikembalikan.');
        }

        $validated = $request->validate([
            'catatan_revisi_rab' => ['required', 'string', 'max:1000'],
        ], [
            'catatan_revisi_rab.required' => 'Catatan revisi RAB wajib diisi.',
            'catatan_revisi_rab.max' => 'Catatan revisi maksimal 1000 karakter.',
        ]);

        $rab->update([
            'status_verifikasi_rab' => 'Dikembalikan',
            'catatan_revisi_rab' => $validated['catatan_revisi_rab'],
            'tanggal_verifikasi_rab' => now(),
        ]);

        // Event 5: Kabid kembalikan RAB -> Staff
        $firstLap = $rab->laporan()->first()?->id_laporan;
        \App\Services\NotificationService::sendToRole(
            'Staff Sarana dan Prasarana',
            'RAB Dikembalikan Kabid',
            "Kabid mengembalikan RAB {$rab->id_rab} untuk revisi.",
            route('staff.rab.show', $rab->id_rab),
            $firstLap
        );

        return redirect()
            ->route('kabid.rab.index')
            ->with('success', "RAB {$rab->id_rab} berhasil dikembalikan untuk revisi.");
    }
}
