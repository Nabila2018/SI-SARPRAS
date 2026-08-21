<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use App\Models\Pasar;
use App\Models\DetailRab;
use App\Models\ProgresPerbaikan;
use App\Models\FotoProgres;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class StaffLaporanController extends Controller
{
    /**
     * Export RAB ke berkas PDF.
     */
    public function exportRabPdf($id)
    {
        $laporan = Laporan::with(['lokasi.pasar', 'fasilitas', 'pelapor', 'detailRab'])->findOrFail($id);

        if ($laporan->detailRab->isEmpty()) {
            return back()->with('error', 'RAB belum memiliki rincian kebutuhan.');
        }

        $logoBase64 = '';
        if (extension_loaded('gd')) {
            $logoPath = public_path('images/Logo Dinas Perdagangan Kota Padang.png');
            if (file_exists($logoPath)) {
                $type = pathinfo($logoPath, PATHINFO_EXTENSION);
                $data = file_get_contents($logoPath);
                $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.rab', compact('laporan', 'logoBase64'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download("RAB_{$laporan->id_laporan}.pdf");
    }

    // Daftar semua laporan masuk (Staff)
    public function index(Request $request)
    {
        $query = $this->applyFilters(Laporan::query(), $request);

        $laporan = $query
            ->with(['lokasi.pasar', 'fasilitas', 'pelapor', 'evaluator'])
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

        if (!in_array($laporan->status_laporan, ['Menunggu', 'Dikembalikan'])) {
            return back()->with('error', 'Evaluasi hanya dapat dilakukan saat laporan berstatus Menunggu atau Dikembalikan.');
        }

        $data = $request->validate([
            'kategori_kerusakan' => ['required', 'in:Ringan,Sedang,Berat'],
            'catatan_pemeriksaan' => ['nullable', 'string', 'max:2000'],
            'file_lampiran_evaluasi' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,doc,docx', 'max:5120'],
        ]);

        $updateData = [
            'kategori_kerusakan' => $data['kategori_kerusakan'],
            'catatan_pemeriksaan' => $data['catatan_pemeriksaan'],
            'tanggal_evaluasi'    => now(),
        ];

        if ($request->boolean('hapus_lampiran_evaluasi')) {
            if ($laporan->file_lampiran_evaluasi) {
                Storage::disk('public')->delete($laporan->file_lampiran_evaluasi);
            }
            $updateData['file_lampiran_evaluasi'] = null;
        }

        if ($request->hasFile('file_lampiran_evaluasi')) {
            if ($laporan->file_lampiran_evaluasi) {
                Storage::disk('public')->delete($laporan->file_lampiran_evaluasi);
            }
            $updateData['file_lampiran_evaluasi'] = $request->file('file_lampiran_evaluasi')->store('evaluasi', 'public');
        }

        if (is_null($laporan->id_evaluator)) {
            $updateData['id_evaluator'] = auth()->user()->id_user;
        }

        $laporan->update($updateData);

        return redirect()
            ->route('laporan.show', ['id' => $laporan->id_laporan, 'tab' => 'evaluasi'])
            ->with('success', 'Evaluasi berhasil disimpan.');
    }

    public function forwardToKabid($id)
    {
        $laporan = Laporan::findOrFail($id);

        if (auth()->user()->role->nama_role !== 'Staff Sarana dan Prasarana') {
            abort(403, 'Akses ditolak.');
        }

        if (!in_array($laporan->status_laporan, ['Menunggu', 'Dikembalikan'])) {
            return back()->with('error', 'Laporan ini tidak dapat diteruskan karena status sudah berubah.');
        }

        if (empty($laporan->kategori_kerusakan) && empty($laporan->catatan_pemeriksaan)) {
            return back()->with('error', 'Evaluasi harus diisi sebelum melanjutkan ke Kabid.');
        }

        $isResubmit = $laporan->status_laporan === 'Dikembalikan';

        $laporan->update([
            'status_laporan' => 'Diproses',
        ]);

        // Event 2: Staff kirim evaluasi -> Kabid
        \App\Services\NotificationService::sendToRole(
            'Kepala Bidang',
            'Evaluasi Laporan Baru',
            "Staff meneruskan evaluasi laporan {$laporan->id_laporan} ({$laporan->item_kerusakan}) untuk diverifikasi Kabid.",
            route('kabid.laporan.show', $laporan->id_laporan),
            $laporan->id_laporan
        );

        $successMsg = $isResubmit
            ? 'Evaluasi berhasil dikirim ulang ke Kabid.'
            : 'Laporan berhasil diteruskan ke Kabid.';

        return redirect()
            ->route('laporan.show', ['id' => $laporan->id_laporan, 'tab' => 'evaluasi'])
            ->with('success', $successMsg);
    }

    /**
     * Tampilkan halaman RAB (buat baru atau edit).
     */
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

    /**
     * Simpan progres perbaikan laporan (0%, 50%, 100%).
     */
    public function storeProgres(Request $request, $id)
    {
        if (auth()->user()->role->nama_role !== 'Staff Sarana dan Prasarana') {
            abort(403, 'Akses ditolak.');
        }

        $laporan = Laporan::with(['progresPerbaikan', 'rab'])->findOrFail($id);

        $rab = $laporan->rab;

        if (!$rab || (is_null($rab->tanggal_persetujuan_awal) && $rab->status_verifikasi_rab !== 'Disetujui')) {
            return back()->with('error', 'Progres perbaikan hanya dapat dikelola setelah RAB disetujui Kabid.');
        }

        $existingStages = $laporan->progresPerbaikan->pluck('persentase_penyelesaian')->toArray();

        // Tentukan tahap selanjutnya
        $nextStage = null;
        if (!in_array('0', $existingStages)) {
            $nextStage = '0';
        } elseif (!in_array('50', $existingStages)) {
            $nextStage = '50';
        } elseif (!in_array('100', $existingStages)) {
            $nextStage = '100';
        } else {
            return back()->with('error', 'Seluruh tahap progres perbaikan (100%) sudah lengkap.');
        }

        $validated = $request->validate([
            'keterangan_perkembangan' => ['required', 'string', 'max:2000'],
            'foto_progres' => ['required', 'array', 'min:1', 'max:5'],
            'foto_progres.*' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:4096'],
        ], [
            'keterangan_perkembangan.required' => 'Keterangan perkembangan wajib diisi.',
            'foto_progres.required' => 'Minimal 1 foto progres wajib diunggah.',
            'foto_progres.min' => 'Minimal 1 foto progres wajib diunggah.',
            'foto_progres.max' => 'Maksimal 5 foto progres yang dapat diunggah sekaligus.',
            'foto_progres.*.image' => 'File foto harus berupa gambar (jpg, jpeg, png).',
            'foto_progres.*.max' => 'Ukuran file foto maksimal 4 MB per foto.',
        ]);

        DB::beginTransaction();

        try {
            $progres = ProgresPerbaikan::create([
                'id_progres' => ProgresPerbaikan::generateId(),
                'id_laporan' => $laporan->id_laporan,
                'persentase_penyelesaian' => $nextStage,
                'keterangan_perkembangan' => $validated['keterangan_perkembangan'],
                'tanggal_update' => now(),
            ]);

            // Jika progres perbaikan sudah mencapai 100%, ubah status_laporan menjadi 'Selesai'
            if ((string)$nextStage === '100') {
                $laporan->update(['status_laporan' => 'Selesai']);

                // Event 6: Progress mencapai 100% / pekerjaan selesai -> UPTD terkait
                if ($laporan->id_pelapor) {
                    \App\Services\NotificationService::sendToUser(
                        $laporan->id_pelapor,
                        'Progress Pekerjaan 100% (Selesai)',
                        "Pekerjaan perbaikan untuk laporan {$laporan->id_laporan} ({$laporan->item_kerusakan}) telah selesai 100%.",
                        route('laporan.show', $laporan->id_laporan),
                        $laporan->id_laporan
                    );
                }
            }

            if ($request->hasFile('foto_progres')) {
                foreach ($request->file('foto_progres') as $file) {
                    $path = $file->store('progres', 'public');
                    FotoProgres::create([
                        'id_foto_progres' => FotoProgres::generateId(),
                        'id_progres' => $progres->id_progres,
                        'file_foto' => $path,
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('laporan.show', ['id' => $laporan->id_laporan, 'tab' => 'progress'])
                ->with('success', "Progres perbaikan Tahap {$nextStage}% berhasil disimpan.");
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function updateProgres(Request $request, $id, $id_progres)
    {
        $laporan = Laporan::with('rab')->findOrFail($id);

        if (auth()->user()->role->nama_role !== 'Staff Sarana dan Prasarana') {
            abort(403, 'Akses ditolak.');
        }

        $rab = $laporan->rab;

        if (!$rab || (is_null($rab->tanggal_persetujuan_awal) && $rab->status_verifikasi_rab !== 'Disetujui')) {
            return back()->with('error', 'Progres perbaikan hanya dapat dikelola setelah RAB disetujui Kabid.');
        }

        $progres = ProgresPerbaikan::where('id_laporan', $laporan->id_laporan)
            ->where('id_progres', $id_progres)
            ->firstOrFail();

        $validated = $request->validate([
            'keterangan_perkembangan' => ['required', 'string', 'max:2000'],
            'hapus_foto' => ['nullable', 'array'],
            'hapus_foto.*' => ['string'],
            'foto_progres' => ['nullable', 'array', 'max:5'],
            'foto_progres.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:4096'],
        ], [
            'keterangan_perkembangan.required' => 'Keterangan perkembangan wajib diisi.',
            'foto_progres.max' => 'Maksimal 5 foto progres baru yang dapat diunggah sekaligus.',
            'foto_progres.*.image' => 'File foto harus berupa gambar (jpg, jpeg, png).',
            'foto_progres.*.max' => 'Ukuran file foto maksimal 4 MB per foto.',
        ]);

        $currentPhotos = $progres->fotoProgres;
        $toDeleteIds = $validated['hapus_foto'] ?? [];
        $newFilesCount = $request->hasFile('foto_progres') ? count($request->file('foto_progres')) : 0;
        $remainingCount = ($currentPhotos->count() - count($toDeleteIds)) + $newFilesCount;

        if ($remainingCount < 1) {
            return back()->with('error', 'Minimal 1 foto dokumentasi progres harus tetap ada.');
        }

        DB::beginTransaction();

        try {
            if (!empty($toDeleteIds)) {
                $fotosToDelete = FotoProgres::where('id_progres', $progres->id_progres)
                    ->whereIn('id_foto_progres', $toDeleteIds)
                    ->get();

                foreach ($fotosToDelete as $foto) {
                    Storage::disk('public')->delete($foto->file_foto);
                    $foto->delete();
                }
            }

            if ($request->hasFile('foto_progres')) {
                foreach ($request->file('foto_progres') as $file) {
                    $path = $file->store('progres', 'public');
                    FotoProgres::create([
                        'id_foto_progres' => FotoProgres::generateId(),
                        'id_progres' => $progres->id_progres,
                        'file_foto' => $path,
                    ]);
                }
            }

            $progres->update([
                'keterangan_perkembangan' => $validated['keterangan_perkembangan'],
                'tanggal_update' => now(),
            ]);

            DB::commit();

            return redirect()
                ->route('laporan.show', ['id' => $laporan->id_laporan, 'tab' => 'progress'])
                ->with('success', "Progres perbaikan Tahap {$progres->persentase_penyelesaian}% berhasil diperbarui.");
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}