<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use App\Models\Pasar;
use App\Models\DetailRab;
use App\Models\ProgresPerbaikan;
use App\Models\FotoProgres;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


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

        /**
     * Tampilkan halaman RAB (buat baru atau edit).
     */
    public function showRab($id)
    {
        $laporan = Laporan::with(['lokasi.pasar', 'fasilitas', 'pelapor', 'detailRab'])
            ->findOrFail($id);

        // Cek role
        if (auth()->user()->role->nama_role !== 'Staff Sarana dan Prasarana') {
            abort(403, 'Akses ditolak.');
        }

        // Cek jika RAB belum dibuat dan status laporan belum disetujui
        if (!$laporan->detailRab()->exists() && in_array($laporan->status_laporan, ['Menunggu', 'Ditolak', 'Dikembalikan'])) {
            return back()->with('error', 'RAB hanya dapat diakses jika laporan sudah disetujui.');
        }

        return view('staff.laporan.rab', compact('laporan'));
    }

        /**
     * Simpan atau update RAB (detail_rab).
     */
    public function storeRab(Request $request, $id)
    {
        $laporan = Laporan::findOrFail($id);

        if (auth()->user()->role->nama_role !== 'Staff Sarana dan Prasarana') {
            abort(403, 'Akses ditolak.');
        }

        if ($laporan->status_laporan !== 'Disetujui') {
            return back()->with('error', 'RAB hanya dapat dibuat jika laporan sudah disetujui.');
        }

        // Validasi: minimal 1 baris detail
        $validated = $request->validate([
            'rincian_kebutuhan' => ['required', 'array', 'min:1'],
            'rincian_kebutuhan.*' => ['required', 'string', 'max:150'],
            'volume' => ['required', 'array', 'min:1'],
            'volume.*' => ['required', 'numeric', 'min:0.001'],
            'satuan' => ['required', 'array', 'min:1'],
            'satuan.*' => ['required', 'string', 'max:30'],
            'harga_satuan' => ['required', 'array', 'min:1'],
            'harga_satuan.*' => ['required', 'numeric', 'min:1'],
        ]);

        DB::beginTransaction();

        try {
            // Hapus detail_rab lama (kalau edit)
            $laporan->detailRab()->delete();

            // Insert detail_rab baru
            $details = [];
            foreach ($validated['rincian_kebutuhan'] as $index => $rincian) {
                $details[] = [
                    'id_laporan' => $laporan->id_laporan,
                    'rincian_kebutuhan' => $rincian,
                    'volume' => $validated['volume'][$index],
                    'satuan' => $validated['satuan'][$index],
                    'harga_satuan' => $validated['harga_satuan'][$index],
                ];
            }

            DetailRab::insert($details);

            // Update tanggal input RAB
            $laporan->update([
                'tanggal_input_rab' => now(),
            ]);

            DB::commit();

            return redirect()
                ->route('staff.laporan.rab.show', $laporan->id_laporan)
                ->with('success', 'RAB berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

        /**
     * Teruskan RAB ke Kabid untuk verifikasi.
     */
    public function forwardRab($id)
    {
        $laporan = Laporan::with('detailRab')->findOrFail($id);

        if (auth()->user()->role->nama_role !== 'Staff Sarana dan Prasarana') {
            abort(403, 'Akses ditolak.');
        }

        if ($laporan->status_laporan !== 'Disetujui') {
            return back()->with('error', 'RAB hanya dapat diteruskan jika laporan sudah disetujui.');
        }

        if ($laporan->detailRab->isEmpty()) {
            return back()->with('error', 'RAB tidak boleh kosong. Isi detail kebutuhan terlebih dahulu.');
        }

        $laporan->update([
            'status_verifikasi_rab' => 'Menunggu',
            'tanggal_input_rab' => now(),
        ]);

        return redirect()
            ->route('staff.laporan.rab.show', $laporan->id_laporan)
            ->with('success', 'RAB berhasil diteruskan ke Kabid.');
    }
        /**
     * Daftar RAB yang sudah dibuat oleh Staff.
     */
        /**
     * Daftar RAB yang sudah dibuat oleh Staff.
     */
    public function indexRab(Request $request)
    {
        if (auth()->user()->role->nama_role !== 'Staff Sarana dan Prasarana') {
            abort(403, 'Akses ditolak.');
        }

        $query = Laporan::with(['lokasi.pasar', 'fasilitas', 'pelapor', 'detailRab'])
            ->whereHas('detailRab');  // ← FIX: Tampilkan yang punya detail_rab

        // Filter status RAB
        if ($request->filled('status')) {
            $query->where('status_verifikasi_rab', $request->status);
        }

        // Filter search
        $search = trim((string) $request->input('search', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->whereHas('lokasi.pasar', function ($subQuery) use ($search) {
                    $subQuery->where('nama_pasar', 'like', "%{$search}%");
                })->orWhereHas('fasilitas', function ($subQuery) use ($search) {
                    $subQuery->where('nama_fasilitas', 'like', "%{$search}%");
                });
            });
        }

        $rabList = $query->orderBy('tanggal_input_rab', 'desc')->paginate(10)->appends($request->only(['search', 'status']));

        $statusList = ['Menunggu', 'Disetujui', 'Dikembalikan'];

        return view('staff.rab.index', compact('rabList', 'statusList'));
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

    /**
     * Simpan progres perbaikan laporan (0%, 50%, 100%).
     */
    public function storeProgres(Request $request, $id)
    {
        if (auth()->user()->role->nama_role !== 'Staff Sarana dan Prasarana') {
            abort(403, 'Akses ditolak.');
        }

        $laporan = Laporan::with('progresPerbaikan')->findOrFail($id);

        if ($laporan->status_verifikasi_rab !== 'Disetujui') {
            return back()->with('error', 'Progres perbaikan hanya dapat ditambahkan setelah RAB disetujui oleh Kabid.');
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
            'foto_progres' => ['required', 'array', 'min:1'],
            'foto_progres.*' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:4096'],
        ], [
            'keterangan_perkembangan.required' => 'Keterangan perkembangan wajib diisi.',
            'foto_progres.required' => 'Minimal 1 foto progres wajib diunggah.',
            'foto_progres.min' => 'Minimal 1 foto progres wajib diunggah.',
            'foto_progres.*.image' => 'File foto harus berupa gambar (jpg, jpeg, png).',
            'foto_progres.*.max' => 'Ukuran file foto maksimal 4 MB.',
        ]);

        DB::beginTransaction();

        try {
            $progres = ProgresPerbaikan::create([
                'id_laporan' => $laporan->id_laporan,
                'persentase_penyelesaian' => $nextStage,
                'keterangan_perkembangan' => $validated['keterangan_perkembangan'],
                'tanggal_update' => now(),
            ]);

            // Jika progres perbaikan sudah mencapai 100%, ubah status_laporan menjadi 'Selesai'
            if ((string)$nextStage === '100') {
                $laporan->update(['status_laporan' => 'Selesai']);
            }

            if ($request->hasFile('foto_progres')) {
                foreach ($request->file('foto_progres') as $file) {
                    $path = $file->store('progres', 'public');
                    FotoProgres::create([
                        'id_progres' => $progres->id_progres,
                        'file_foto' => $path,
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('laporan.show', $laporan->id_laporan)
                ->with('success', "Progres perbaikan Tahap {$nextStage}% berhasil disimpan.");
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}